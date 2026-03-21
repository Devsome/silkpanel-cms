<?php

use Illuminate\Support\Str;

return [
    'api_key' => env('SILKPANEL_API_KEY'),

    'version' => Str::lower(env('SILKPANEL_SERVER_VERSION', 'vsro')),

    'uniques' => [
        'MOB_CH_TIGERWOMAN' => [
            'id' => 1954,
            'name' => 'Tiger Girl',
        ],
        'MOB_OA_URUCHI' => [
            'id' => 1982,
            'name' => 'Uruchi',
        ],
        'MOB_KK_ISYUTARU' => [
            'id' => 2002,
            'name' => 'Isyutaru',
        ],
        'MOB_TK_BONELORD' => [
            'id' => 3810,
            'name' => 'Lord Yarkan',
        ],
        'MOB_RM_TAHOMET' => [
            'id' => 3875,
            'name' => 'Demon Shaitan',
        ],
        'MOB_AM_IVY' => [
            'id' => 14778,
            'name' => 'Captain Ivy',
        ],
        'MOB_EU_KERBEROS' => [
            'id' => 5871,
            'name' => 'Cerberus',
        ],
        'MOB_RM_ROC' => [
            'id' => 3877,
            'name' => 'Roc',
        ],
        'MOB_TQ_WHITESNAKE' => [
            'id' => 14839,
            'name' => 'Medusa',
        ],
    ],
];
