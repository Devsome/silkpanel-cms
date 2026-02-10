<?php

use Illuminate\Support\Str;

return [
    'api_key' => env('SILKPANEL_API_KEY'),

    'version' => Str::lower(env('SILKPANEL_SERVER_VERSION', 'vsro')),
];
