<?php

return [
    'navigation'       => 'Ranking Settings',
    'navigation_group' => 'Configuration',

    'tabs' => [
        'chars'  => 'Characters',
        'guilds' => 'Guilds',
    ],

    'sections' => [
        'config'               => 'Ranking Configuration',
        'columns'              => 'Display Columns',
        'columns_description'  => 'Map SQL result columns to their display labels used in the public ranking table.',
        'excluded'             => 'Excluded Characters',
        'excluded_description' => 'Characters selected here will be hidden from the ranking completely.',
        'coming_soon'          => 'Guild Ranking',
        'coming_soon_description' => 'Guild Ranking configuration will be available in a future update.',
    ],

    'fields' => [
        'title'                  => 'Ranking Title',
        'title_placeholder'      => 'Character Ranking',
        'title_guild_placeholder' => 'Guild Ranking',
        'cache_ttl'              => 'Cache TTL',
        'cache_ttl_description'  => 'How long the ranking results are cached (0 = disabled).',
        'limit'                  => 'Max. Entries',
        'limit_description'      => 'Maximum number of players shown in the ranking (0 = no limit).',
        'column_name'            => 'SQL Column',
        'column_label'           => 'Display Label',
        'add_column'             => 'Add Column',
        'excluded'               => 'Excluded Characters',
        'excluded_description'   => 'Search for characters by name and select them to remove them from the ranking.',
        'excluded_guilds'        => 'Excluded Guilds',
        'excluded_guilds_description' => 'Search for guilds by name and select them to remove them from the ranking.',
    ],

    'actions' => [
        'save' => 'Save Settings',
        'test' => 'Test Chars Query',
        'test_guilds' => 'Test Guilds Query',
    ],

    'notifications' => [
        'saved_title'   => 'Ranking Settings Saved',
        'saved_message' => 'Your ranking configuration has been updated successfully.',
    ],

    'preview' => [
        'title'       => 'Query Preview',
        'rows'        => 'rows returned',
        'live_hint'   => 'This is a live result, not from cache.',
        'preview_limit_hint' => 'Preview is limited to 10 entries. The actual ranking will use your configured Max. Entries limit.',
        'no_sql'      => 'Query could not be executed.',
        'error_title' => 'Query Error',
    ],
];
