<?php

namespace App\Enums;

/**
 * Mapping of ISRO Character IDs to VSRO Character IDs for avatar images
 * The VSRO IDs are correct and match the image files
 * Chinese Characters have the same IDs in both versions
 * Only European Characters differ between ISRO and VSRO
 */
enum CharacterAvatarMapEnum: int
{
    // Chinese Characters
    case CHAR_CH_MAN_ADVENTURER = 1907;
    case CHAR_CH_MAN_BOGY = 1908;
    case CHAR_CH_MAN_FIGHTER = 1909;
    case CHAR_CH_MAN_MERCHANT = 1910;
    case CHAR_CH_MAN_MONK = 1911;
    case CHAR_CH_MAN_MONKEY = 1912;
    case CHAR_CH_MAN_NECROMANCER = 1913;
    case CHAR_CH_MAN_NOBLEBOY = 1914;
    case CHAR_CH_MAN_PERFORMER = 1915;
    case CHAR_CH_MAN_PRIEST = 1916;
    case CHAR_CH_MAN_SCHOLAR = 1917;
    case CHAR_CH_MAN_TATTOO = 1918;
    case CHAR_CH_MAN_WARRIOR = 1919;
    case CHAR_CH_WOMAN_ADVENTURER = 1920;
    case CHAR_CH_WOMAN_ASSASSIN = 1921;
    case CHAR_CH_WOMAN_BOGY = 1922;
    case CHAR_CH_WOMAN_FIGHTER = 1923;
    case CHAR_CH_WOMAN_FOX = 1924;
    case CHAR_CH_WOMAN_KANGSI = 1925;
    case CHAR_CH_WOMAN_KISAENG = 1926;
    case CHAR_CH_WOMAN_MERCHANT = 1927;
    case CHAR_CH_WOMAN_NECROMENCERB = 1928;
    case CHAR_CH_WOMAN_NECROMENCERW = 1929;
    case CHAR_CH_WOMAN_NOBLEGIRL = 1930;
    case CHAR_CH_WOMAN_SCHOLAR = 1931;
    case CHAR_CH_WOMAN_WARRIOR = 1932;

        // European Characters - VSRO IDs
    case CHAR_EU_MAN_ADVENTURER = 14726;
    case CHAR_EU_MAN_ANGEL = 14727;
    case CHAR_EU_MAN_BARBARIAN = 14725;
    case CHAR_EU_MAN_DEVIL = 14728;
    case CHAR_EU_MAN_EXORCIST = 14718;
    case CHAR_EU_MAN_GLADIATOR = 14724;
    case CHAR_EU_MAN_KNIGHT = 14722;
    case CHAR_EU_MAN_MERCHANT = 14720;
    case CHAR_EU_MAN_NECROMENCER = 14719;
    case CHAR_EU_MAN_NOBLE = 14717;
    case CHAR_EU_MAN_PRIEST = 14721;
    case CHAR_EU_MAN_WARRIOR = 14723;
    case CHAR_EU_MAN_WEREWOLF = 14729;
    case CHAR_EU_WOMAN_ADVENTURER = 14738;
    case CHAR_EU_WOMAN_AMAZONESS = 14736;
    case CHAR_EU_WOMAN_ANGEL = 14740;
    case CHAR_EU_WOMAN_CRUSADER = 14735;
    case CHAR_EU_WOMAN_DEVIL = 14741;
    case CHAR_EU_WOMAN_GLADIATOR = 14739;
    case CHAR_EU_WOMAN_KNIGHT = 14737;
    case CHAR_EU_WOMAN_MERCHANT = 14733;
    case CHAR_EU_WOMAN_NOBLE = 14730;
    case CHAR_EU_WOMAN_ORACLE = 14734;
    case CHAR_EU_WOMAN_SUCCUBUS = 14742;
    case CHAR_EU_WOMAN_SUMMONER = 14732;
    case CHAR_EU_WOMAN_WITCH = 14731;

    /**
     * Maps an ISRO Character ID to the correct VSRO ID (for images)
     * 
     * @param int $isroId The character ID from ISRO
     * @return int The corresponding VSRO ID for the avatar images
     */
    public static function mapIsroToVsro(int $isroId): int
    {
        // Mapping only necessary for European Characters
        // Chinese Characters have the same IDs in both versions
        $isroToVsroMap = [
            // European Man
            14726 => 14884, // CHAR_EU_MAN_ADVENTURER
            14727 => 14885, // CHAR_EU_MAN_ANGEL
            14725 => 14883, // CHAR_EU_MAN_BARBARIAN
            14728 => 14886, // CHAR_EU_MAN_DEVIL
            14718 => 14876, // CHAR_EU_MAN_EXORCIST
            14724 => 14882, // CHAR_EU_MAN_GLADIATOR
            14722 => 14880, // CHAR_EU_MAN_KNIGHT
            14720 => 14878, // CHAR_EU_MAN_MERCHANT
            14719 => 14877, // CHAR_EU_MAN_NECROMENCER
            14717 => 14875, // CHAR_EU_MAN_NOBLE
            14721 => 14879, // CHAR_EU_MAN_PRIEST
            14723 => 14881, // CHAR_EU_MAN_WARRIOR
            14729 => 14887, // CHAR_EU_MAN_WEREWOLF

            // European Woman
            14738 => 14896, // CHAR_EU_WOMAN_ADVENTURER
            14736 => 14894, // CHAR_EU_WOMAN_AMAZONESS
            14740 => 14898, // CHAR_EU_WOMAN_ANGEL
            14735 => 14893, // CHAR_EU_WOMAN_CRUSADER
            14741 => 14899, // CHAR_EU_WOMAN_DEVIL
            14739 => 14897, // CHAR_EU_WOMAN_GLADIATOR
            14737 => 14895, // CHAR_EU_WOMAN_KNIGHT
            14733 => 14891, // CHAR_EU_WOMAN_MERCHANT
            14730 => 14888, // CHAR_EU_WOMAN_NOBLE
            14734 => 14892, // CHAR_EU_WOMAN_ORACLE
            14742 => 14900, // CHAR_EU_WOMAN_SUCCUBUS
            14732 => 14890, // CHAR_EU_WOMAN_SUMMONER
            14731 => 14889, // CHAR_EU_WOMAN_WITCH
        ];

        // If the ID exists in the mapping, use the mapping
        if (isset($isroToVsroMap[$isroId])) {
            return $isroToVsroMap[$isroId];
        }

        // Otherwise return the ID unchanged (e.g., for Chinese Characters)
        return $isroId;
    }

    /**
     * Returns the avatar image URL for a character ID
     * 
     * @param int $characterId The character ID (can be ISRO or VSRO)
     * @param string $version The version ('isro' or 'vsro'). Default: 'vsro'
     * @return string The complete asset URL to the avatar image file
     */
    public static function getAvatarUrl(int $characterId, string $version = 'vsro'): string
    {
        $vsroId = $version === 'isro'
            ? self::mapIsroToVsro($characterId)
            : $characterId;

        return asset('images/silkroad/chars_avatar/' . $vsroId . '.png');
    }
}
