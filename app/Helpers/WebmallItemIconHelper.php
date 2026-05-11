<?php

namespace App\Helpers;

use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

class WebmallItemIconHelper
{
    /**
     * Returns the public asset path (relative to public/images/silkroad/) for a given ref_item_id.
     * Falls back to 'icon_default.png' if the file is missing.
     */
    public static function iconPath(int $refItemId): string
    {
        $item = RefObjCommon::select(['ID', 'AssocFileIcon128'])
            ->where('ID', $refItemId)
            ->first();

        return self::resolveIcon($item?->AssocFileIcon128);
    }

    /**
     * Resolves an AssocFileIcon128 string to a relative icon path under public/images/silkroad/.
     */
    public static function resolveIcon(?string $assocFile): string
    {
        if (!$assocFile) {
            return 'icon_default.png';
        }

        $icon = str_replace('\\', '/', trim($assocFile));
        $icon = preg_replace('/\.ddj$/i', '', $icon);
        $icon = strtolower($icon . '.png');

        if (!file_exists(public_path('images/silkroad/' . $icon))) {
            return 'icon_default.png';
        }

        return $icon;
    }

    /**
     * Returns true if the item should show the Sox/Seal overlay (seal.gif).
     *
     * Uses only CodeName128 and TypeID2 from _RefObjCommon — no join to _RefObjItem needed.
     * The sox_type config keys (A_RARE, B_RARE, C_RARE, SET_A_RARE, SET_B_RARE) all appear
     * in the ItemClass > 0 bucket, so any real item matching the keyword is a seal item.
     * TypeID2 == 4 (Avatars/Cosmetics) are excluded, matching the inventory partial logic.
     */
    public static function isSeal(object $refObj): bool
    {
        if ((int) ($refObj->TypeID2 ?? 0) === 4) {
            return false;
        }

        $codeName = (string) ($refObj->CodeName128 ?? '');

        foreach (array_keys(config('item.sox_type', [])) as $threshold) {
            foreach (array_keys(config('item.sox_type')[$threshold]) as $keyword) {
                if (str_contains($codeName, (string) $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }
}
