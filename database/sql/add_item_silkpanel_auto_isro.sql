CREATE OR ALTER PROCEDURE [dbo].[_ADD_ITEM_SILKPANEL_AUTO_ISRO]
    @CharName      VARCHAR(64)  = NULL,      -- Charaktername
    @CharID        INT          = NULL,      -- oder direkte CharID
    @CodeName      VARCHAR(128) = NULL,      -- Item-Codename128
    @RefItemID     INT          = NULL,      -- oder direkte RefItemID
    @Data          INT          = 1,         -- Menge/Haltbarkeit
    @OptLevel      INT          = 0,         -- +Wert (0-15)
    @Variance      BIGINT       = NULL,      -- Varianz (für Equipment)
    @Destination   VARCHAR(10)  = NULL OUTPUT, -- 'Inventory' oder 'Storage'
    @Slot          INT          = 0   OUTPUT,  -- belegter Slot
    @NewItemID     BIGINT       = 0   OUTPUT   -- neue Item-ID
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    -- 1. Item-Identifikation
    IF @RefItemID IS NULL
    BEGIN
        IF @CodeName IS NULL
        BEGIN
            RAISERROR('Either CodeName or RefItemID must be provided.', 16, 1);
            RETURN -1;
        END
        SELECT @RefItemID = ID FROM _RefObjCommon WITH (NOLOCK) WHERE Codename128 = @CodeName;
        IF @RefItemID IS NULL OR @RefItemID = 0 RETURN -4; -- unbekanntes Item
    END
    ELSE IF NOT EXISTS (SELECT 1 FROM _RefObjCommon WITH (NOLOCK) WHERE ID = @RefItemID)
        RETURN -4;

    -- 2. Charakter ermitteln und Inventargröße laden
    DECLARE @InvSize INT = 45;
    IF @CharID IS NULL
    BEGIN
        IF @CharName IS NULL
        BEGIN
            RAISERROR('Either CharName or CharID must be provided.', 16, 1);
            RETURN -2;
        END
        SELECT @CharID = CharID, @InvSize = ISNULL(InventorySize, 45)
        FROM _Char WITH (NOLOCK)
        WHERE CharName16 = @CharName;
        IF @CharID IS NULL RETURN -2;
    END
    ELSE
    BEGIN
        SELECT @InvSize = ISNULL(InventorySize, 45)
        FROM _Char WITH (NOLOCK)
        WHERE CharID = @CharID;
        IF @InvSize IS NULL RETURN -2;
    END

    -- 3. Item-Details (Link, Typen) aus _RefObjCommon
    DECLARE @Link INT,
            @Type1 TINYINT, @Type2 TINYINT, @Type3 TINYINT, @Type4 TINYINT;
    SELECT @Link = Link,
           @Type1 = TypeID1, @Type2 = TypeID2, @Type3 = TypeID3, @Type4 = TypeID4
    FROM _RefObjCommon WITH (NOLOCK)
    WHERE ID = @RefItemID;
    IF @Link IS NULL OR @Link = 0 RETURN -5;

    -- 4. Item-Typ prüfen
    IF @Type1 <> 3
    BEGIN
        RAISERROR('Not an item: %s', 16, 1, @CodeName);
        RETURN -6;
    END

    -- 5. Equipment / Pet / Stapelbar erkennen und @Data korrigieren
    DECLARE @IsEquip INT = 0, @IsPet INT = 0;
    IF (@Type1 = 3 AND @Type2 = 1)
        SET @IsEquip = 1;
    ELSE IF (@Type1 = 3 AND @Type2 = 2 AND @Type3 = 1 AND @Type4 IN (1,2,3))
        SET @IsPet = 1;

    IF @IsEquip = 1
    BEGIN
        SELECT @Data = Dur_L FROM _RefObjItem WITH (NOLOCK) WHERE ID = @Link;
        IF @OptLevel < 0 SET @OptLevel = 0 ELSE IF @OptLevel > 15 SET @OptLevel = 15;
    END
    ELSE
    BEGIN
        IF @IsPet = 1
            SET @Data = 0;
        ELSE
        BEGIN
            DECLARE @MaxStack INT;
            SELECT @MaxStack = MaxStack FROM _RefObjItem WITH (NOLOCK) WHERE ID = @Link;
            IF @Data <= 0 OR @Data > @MaxStack SET @Data = @MaxStack;
        END
    END

    -- 6. UserJID für Storage holen
    DECLARE @UserJID INT;
    SELECT @UserJID = UserJID FROM _User WITH (NOLOCK) WHERE CharID = @CharID;
    IF @UserJID IS NULL RETURN -2; -- Sollte nicht vorkommen

    -- 7. Inventar-Platz suchen (über _RefDummySlot / iSRO-Logik)
    DECLARE @EmptySlotInv INT = NULL;
    SELECT TOP 1 @EmptySlotInv = D.cnt
    FROM _RefDummySlot AS D WITH (NOLOCK)
    WHERE D.cnt >= 13
      AND D.cnt < @InvSize
      AND NOT EXISTS (SELECT 1 FROM _Inventory AS I WITH (NOLOCK) WHERE I.Slot = D.cnt AND I.CharID = @CharID);

    IF @EmptySlotInv IS NOT NULL
    BEGIN
        -- Inventar einfügen
        DECLARE @DummySerial BIGINT = 0;
        DECLARE @InvItemID BIGINT = 0;
        IF @IsEquip = 1
            EXEC _STRG_ADD_ITEM_INVENTORY_NoTX 0, @CharID, @EmptySlotInv, @InvItemID OUTPUT, @DummySerial OUTPUT, @RefItemID, @OptLevel, @Variance, @Data;
        ELSE
            EXEC _STRG_ADD_ITEM_INVENTORY_NoTX 0, @CharID, @EmptySlotInv, @InvItemID OUTPUT, @DummySerial OUTPUT, @RefItemID, NULL, NULL, @Data;

        IF @InvItemID = 0
        BEGIN
            RAISERROR('Inventory insert failed: %s', 16, 1, @CharName);
            RETURN -7;
        END
        SET @Destination = 'Inventory';
        SET @Slot = @EmptySlotInv;
        SET @NewItemID = @InvItemID;
        RETURN 1;
    END

    -- 8. Inventar voll → Storage (Chest) versuchen
    DECLARE @ChestSize INT = 150;
    SELECT @ChestSize = ISNULL(ChestSize, 150) FROM _ChestInfo WITH (NOLOCK) WHERE JID = @UserJID;
    IF @ChestSize IS NULL SET @ChestSize = 150;

    DECLARE @EmptySlotChest INT = NULL;
    SELECT TOP 1 @EmptySlotChest = Slot
    FROM _Chest WITH (NOLOCK)
    WHERE UserJID = @UserJID
      AND Slot < @ChestSize
      AND (ItemID = 0 OR ItemID IS NULL)
    ORDER BY Slot;

    IF @EmptySlotChest IS NOT NULL
    BEGIN
        -- Storage einfügen via iSRO-Prozedur (vorausgesetzt _ADD_ITEM_EXTERN_CHEST_FAST existiert)
        DECLARE @ChestRet INT;
        EXEC @ChestRet = _ADD_ITEM_EXTERN_CHEST_FAST @UserJID, @RefItemID, @Data, @OptLevel;

        IF @ChestRet = 1
        BEGIN
            -- Neue ItemID aus der Chest auslesen
            SELECT @NewItemID = ISNULL(MAX(ItemID), 0)
            FROM _Chest WITH (NOLOCK)
            WHERE UserJID = @UserJID AND Slot = @EmptySlotChest;

            SET @Destination = 'Storage';
            SET @Slot = @EmptySlotChest;
            RETURN 1;
        END
        ELSE
        BEGIN
            RAISERROR('Storage insert failed via _ADD_ITEM_EXTERN_CHEST_FAST: %s', 16, 1, @CharName);
            RETURN -7;
        END
    END

    -- 9. Nichts frei → Fehler
    RAISERROR('No free slot in inventory or storage: %s', 16, 1, @CharName);
    RETURN -3; -- both full
END