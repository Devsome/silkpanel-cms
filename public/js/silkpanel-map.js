/**
 * SilkPanel Map — stripped-down SilkRoad map handler for player tracking.
 * Based on JellyBitz/xSROMap (MIT License), trimmed to the essentials:
 *   - World map + all dungeon tile layers
 *   - AddPlayer / RemovePlayer / SetView / FlyView
 * Removed: sidebar, drawing toolbar, NPC/teleport/location markers,
 *          easyButton, Geoman (pm), coordinate-link popups.
 *
 * Requires: Leaflet.js
 * Images  : /images/silkroad/minimap/
 */
/* jshint esversion: 6 */

// Virtual marker optimisation — only renders markers within the viewport.
L.Marker.addInitHook(function () {
    if (this.options.virtual) {
        this.on('add', function () {
            this._updateIconVisibility = function () {
                if (this._map == null) return;
                var isVisible = this._map.getBounds().contains(this.getLatLng()),
                    wasVisible = this._wasVisible,
                    icon = this._icon,
                    iconParent = this._iconParent,
                    shadow = this._shadow,
                    shadowParent = this._shadowParent;

                if (!iconParent) iconParent = this._iconParent = icon.parentNode;
                if (shadow && !shadowParent) shadowParent = this._shadowParent = shadow.parentNode;

                if (isVisible !== wasVisible) {
                    if (isVisible) {
                        iconParent.appendChild(icon);
                        if (shadow) shadowParent.appendChild(shadow);
                    } else {
                        iconParent.removeChild(icon);
                        if (shadow) shadowParent.removeChild(shadow);
                    }
                    this._wasVisible = isVisible;
                }
            };
            this._map.on('resize moveend zoomend', this._updateIconVisibility, this);
            this._updateIconVisibility();
        }, this);
    }
});

var xSROMap = (function () {
    'use strict';

    // ── Configuration ────────────────────────────────────────────────────────
    var imgHost = '/images/silkroad/minimap/';

    // ── State ─────────────────────────────────────────────────────────────────
    var map;
    var mapLayer;
    var mappingLayers = {};
    var playerMarkers = {};   // id → Leaflet marker

    // ── Coordinate helpers ────────────────────────────────────────────────────

    /**
     * Convert internal SRO coordinates to Leaflet [lat, lng].
     */
    var CoordSROToMap = function (coords) {
        var lat, lng;
        if (coords.region > 32767) {
            // Dungeon
            lng = (128 * 192 + coords.x / 10) / 192;
            lat = (127 * 192 + coords.y / 10) / 192;
            return [lat, lng];
        }
        if (coords.posY && coords.posX) {
            // Game-world (posX/posY)
            lat = (coords.posY / 192) + 91;
            lng = (coords.posX / 192) + 135;
        } else {
            // IC coords (x, y, region)
            lng = (coords.region & 0xFF) + coords.x / 1920;
            lat = ((coords.region >> 8) & 0xFF) + coords.y / 1920 - 1;
        }
        return [lat, lng];
    };

    /**
     * Convert game-world posX/posY to internal SRO coords.
     */
    var CoordsGameToSRO = function (gc) {
        gc.x = Math.round(Math.abs(gc.posX) % 192.0 * 10.0);
        if (gc.posX < 0.0) gc.x = 1920 - gc.x;
        gc.y = Math.round(Math.abs(gc.posY) % 192.0 * 10.0);
        if (gc.posY < 0.0) gc.y = 1920 - gc.y;
        gc.z = 0;
        var xSector = Math.round((gc.posX - gc.x / 10.0) / 192.0 + 135);
        var ySector = Math.round((gc.posY - gc.y / 10.0) / 192.0 + 92);
        gc.region = (ySector << 8) | xSector;
        return gc;
    };

    /**
     * Normalise coordinates to internal SRO format.
     * x, y, z, region — as returned by the API (IC coords).
     */
    var fixCoords = function (x, y, z, region) {
        if (region != null && region < 0) region += 65536;
        if (region == null) {
            return CoordsGameToSRO({ posX: x, posY: y });
        }
        return { x: x, y: y, z: z, region: region };
    };

    // ── Layer helpers ─────────────────────────────────────────────────────────

    /**
     * Return the tile layer for the given coord (world or specific dungeon).
     */
    var getLayer = function (coord) {
        if (coord.region > 32767) {
            var layer = mappingLayers['' + coord.region];
            if (layer) {
                if (layer.options.overlap) {
                    var floors = layer.options.overlap;
                    for (var i = 0; i < floors.length; i++) {
                        if (coord.z < floors[i].options.posZ) break;
                        layer = floors[i];
                    }
                } else {
                    layer.options.posZ = 0;
                }
                layer.options.region = coord.region;
            }
            return layer;
        }
        return mappingLayers[''];
    };

    /**
     * Switch the active tile layer and re-add player markers for that layer.
     */
    var setMapLayer = function (tileLayer) {
        if (!tileLayer || tileLayer === mapLayer) return;
        map.eachLayer(function (l) { map.removeLayer(l); });
        mapLayer = tileLayer;
        map.addLayer(mapLayer);
        for (var id in playerMarkers) {
            var m = playerMarkers[id];
            if (m.options.xMap.layer === mapLayer) m.addTo(map);
        }
    };

    var setView = function (coord) {
        setMapLayer(getLayer(coord));
        map.panTo(CoordSROToMap(coord), 8);
    };

    var flyView = function (coord) {
        setMapLayer(getLayer(coord));
        map.setView(CoordSROToMap(coord), 8);
    };

    // ── Map + layer initialisation ────────────────────────────────────────────

    var initLayers = function () {
        map = L.map('map', { crs: L.CRS.Simple, minZoom: 0, maxZoom: 9, zoomControl: false });
        new L.Control.Zoom({ position: 'topright' }).addTo(map);

        // Fix circle rendering on CRS.Simple
        L.LatLng.prototype.distanceTo = function (other) {
            var dx = other.lng - this.lng, dy = other.lat - this.lat;
            return Math.sqrt(dx * dx + dy * dy);
        };

        // Tile layer with Y-axis inversion
        var SRLayer = L.TileLayer.extend({
            getTileUrl: function (tile) {
                tile.y = -tile.y;
                return L.TileLayer.prototype.getTileUrl.call(this, tile);
            }
        });

        var mapSize = 49152;
        map.fitBounds([[0, 0], [mapSize, mapSize]]);

        // ── World map (default layer) ─────────────────────────────────────
        mapLayer = new SRLayer(imgHost + '{z}/{x}x{y}.jpg', { attribution: '<a href="#">World Map</a>' });
        mappingLayers[''] = mapLayer;
        map.addLayer(mapLayer);
        // Default view: Jangan (region=25000=0x61A8 → sectorX=168, sectorZ=97)
        // Formula: lat = sectorZ + posY/1920 - 1, lng = sectorX + posX/1920
        map.setView([96.88, 168.5], 8);

        // ── Dungeon layers ────────────────────────────────────────────────

        // Donwhang Stone Cave (4 floors)
        mappingLayers['32769'] = new SRLayer(imgHost + 'd/{z}/dh_a01_floor01_{x}x{y}.jpg', {
            attribution: '<a href="#">Donwhang Stone Cave [1F]</a>',
            posZ: 0,
            overlap: [
                new SRLayer(imgHost + 'd/{z}/dh_a01_floor02_{x}x{y}.jpg', { attribution: '<a href="#">Donwhang Stone Cave [2F]</a>', posZ: 115 }),
                new SRLayer(imgHost + 'd/{z}/dh_a01_floor03_{x}x{y}.jpg', { attribution: '<a href="#">Donwhang Stone Cave [3F]</a>', posZ: 230 }),
                new SRLayer(imgHost + 'd/{z}/dh_a01_floor04_{x}x{y}.jpg', { attribution: '<a href="#">Donwhang Stone Cave [4F]</a>', posZ: 345 }),
            ],
        });

        // Tomb of Qui-Shin / Jangan Underground (6 floors)
        mappingLayers['32775'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor01_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B1]</a>' });
        mappingLayers['32774'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor02_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B2]</a>' });
        mappingLayers['32773'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor03_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B3]</a>' });
        mappingLayers['32772'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor04_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B4]</a>' });
        mappingLayers['32771'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor05_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B5]</a>' });
        mappingLayers['32770'] = new SRLayer(imgHost + 'd/{z}/qt_a01_floor06_{x}x{y}.jpg', { attribution: '<a href="#">Tomb of Qui-Shin [B6]</a>' });

        // Job Temple (Egypt)
        var jobPath = imgHost + 'd/{z}/rn_sd_egypt1_01_{x}x{y}.jpg';
        mappingLayers['32784'] = new SRLayer(jobPath, { attribution: '<a href="#">Temple</a>' });
        mappingLayers['32783'] = new SRLayer(imgHost + 'd/{z}/rn_sd_egypt1_02_{x}x{y}.jpg', { attribution: '<a href="#">Sanctum of Seth</a>' });
        mappingLayers['32782'] = new SRLayer(jobPath, { attribution: '<a href="#">Sanctum of Haroeris</a>' });
        mappingLayers['32781'] = new SRLayer(jobPath, { attribution: '<a href="#">Sanctum of Isis</a>' });
        mappingLayers['32780'] = new SRLayer(jobPath, { attribution: '<a href="#">Sanctum of Anubis</a>' });
        mappingLayers['32779'] = new SRLayer(jobPath, { attribution: '<a href="#">Sanctum of Blue Eye</a>' });

        // Cave of Meditation (Fortress war)
        mappingLayers['32785'] = new SRLayer(imgHost + 'd/{z}/fort_dungeon01_{x}x{y}.jpg', { attribution: '<a href="#">Cave of Meditation [1F]</a>' });

        // Flame Mountain
        mappingLayers['32786'] = new SRLayer(imgHost + 'd/{z}/flame_dungeon01_{x}x{y}.jpg', { attribution: '<a href="#">Flame Mountain</a>' });

        // Jupiter rooms
        mappingLayers['32787'] = new SRLayer(imgHost + 'd/{z}/rn_jupiter_02_{x}x{y}.jpg', { attribution: '<a href="#">The Earth\'s Room</a>' });
        mappingLayers['32788'] = new SRLayer(imgHost + 'd/{z}/rn_jupiter_03_{x}x{y}.jpg', { attribution: '<a href="#">Yuno\'s Room</a>' });
        mappingLayers['32789'] = new SRLayer(imgHost + 'd/{z}/rn_jupiter_04_{x}x{y}.jpg', { attribution: '<a href="#">Jupiter\'s Room</a>' });
        mappingLayers['32790'] = new SRLayer(imgHost + 'd/{z}/rn_jupiter_01_{x}x{y}.jpg', { attribution: '<a href="#">Zealots Hideout</a>' });

        // Kalia's Hideout (Bahdag room)
        mappingLayers['32793'] = new SRLayer(imgHost + 'd/{z}/RN_ARABIA_FIELD_02_BOSS_{x}x{y}.jpg', { attribution: '<a href="#">Kalia\'s Hideout</a>' });
    };

    // ── Public API ────────────────────────────────────────────────────────────
    return {
        /**
         * Initialise the map. The Leaflet map is always bound to #map.
         * Optional x, y (IC world coords) set the initial view.
         */
        init: function (x, y, z, region) {
            initLayers();
            if (x != null && y != null) {
                setView(fixCoords(x, y, z != null ? z : null, region != null ? region : null));
            }
        },

        /**
         * Add a player marker. Ignored if the id is already registered.
         * @param {number|string} id       Unique character ID
         * @param {string}        html     Popup HTML content
         * @param {number}        x        IC x coordinate
         * @param {number}        y        IC y coordinate
         * @param {number}        z        IC z coordinate
         * @param {number}        region   LatestRegion value
         * @param {object}       [options]
         * @param {string}       [options.color='#60a5fa']   Marker fill colour
         * @param {boolean}      [options.highlighted=false]  Larger size + glow
         */
        AddPlayer: function (id, html, x, y, z, region, options) {
            if (playerMarkers[id]) return;
            var coord = fixCoords(x, y, z, region);
            var o = options || {};
            var size = o.highlighted ? 14 : 10;
            var color = o.color || '#60a5fa';
            var glow = o.highlighted
                ? ';box-shadow:0 0 0 3px rgba(255,255,255,.5),0 0 8px 2px ' + color
                : '';
            var pulse = o.pulse ? ';animation:silk-pulse 1.1s ease-in-out infinite' : '';
            var dimmed = o.dimmed ? ';opacity:0.25' : '';
            var icon = L.divIcon({
                className: '',
                html: '<span style="display:block;width:' + size + 'px;height:' + size + 'px;'
                    + 'border-radius:50%;background:' + color + ';'
                    + 'border:2px solid rgba(255,255,255,.9)' + glow + pulse + dimmed + '"></span>',
                iconSize: [size, size],
                iconAnchor: [size / 2, size / 2],
                popupAnchor: [0, -(size / 2 + 4)],
            });
            var marker = L.marker(CoordSROToMap(coord), { icon: icon, virtual: true }).bindPopup(html);
            var layer = getLayer(coord);
            if (layer === mapLayer) marker.addTo(map);
            marker.options.xMap = { layer: layer, coordinates: coord };
            playerMarkers[id] = marker;
        },

        /**
         * Remove a player marker by id.
         */
        RemovePlayer: function (id) {
            var marker = playerMarkers[id];
            if (!marker) return;
            if (marker.options.xMap.layer === mapLayer) {
                map.eachLayer(function (l) { if (l === marker) map.removeLayer(l); });
            }
            delete playerMarkers[id];
        },

        /**
         * Pan the map to the given IC coordinates.
         */
        SetView: function (x, y, z, region) {
            setView(fixCoords(x, y, z, region));
        },

        /**
         * Fly (animate) the map to the given IC coordinates.
         */
        FlyView: function (x, y, z, region) {
            flyView(fixCoords(x, y, z, region));
        },
    };
}());
