<?php

return [
    'navigation' => 'World Map',
    'navigation_group' => 'Silkroad',

    // Toolbar
    'search_placeholder'  => 'Search character…',
    'online_label'        => 'Online',
    'all_label'           => 'Total',
    'limit_reached'       => 'limit :n',
    'refreshed_label'     => 'Refreshed',
    'refresh_btn'         => 'Refresh',
    'loading_btn'         => 'Loading…',
    'manual_option'       => 'Manual',
    'show_offline'        => 'Show offline',
    'show_job_only'       => 'Show only Job',

    // Character popup
    'popup_level'         => 'Level',
    'popup_guild_id'      => 'Guild ID',
    'popup_last_logout'   => 'Last logout',
    'popup_job_suit'      => '⚔ Job Suit',
    'popup_offline'       => '⚪ Offline',

    // Errors
    'error_lib'           => 'Map library failed to load.',
    'error_load'          => 'Failed to load character positions',

    // Settings tab labels (used in ManageSettings.php)
    'settings' => [
        'tab'                               => 'World Map',
        'section'                           => 'World Map Settings',
        'frontend_enabled'                  => 'Activated Worldmap + Api route in frontend',
        'frontend_enabled_description'      => 'When disabled, the map page and the frontend API route return 404 for all users.',
        'excluded_chars'                    => 'Excluded characters from the map',
        'excluded_chars_description'        => 'Characters in this list will be hidden from the frontend map (e.g. GMs). The admin view always shows all characters.',
        'max_characters'                    => 'Maximum characters shown on the map',
        'max_characters_description'        => 'Hard limit for both admin and frontend. Default: 500.',
    ],
];
