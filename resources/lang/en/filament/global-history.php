<?php

return [
    'navigation' => 'Global History (VSRO)',
    'title'      => 'Global History Source (VSRO)',

    'sections' => [
        'status'              => 'Status',
        'status_description'  => 'Enable and expose the public Global History page (/history/globals), the homepage widget and the character panel on VSRO using the configured source below.',
        'source'              => 'Source table',
        'source_description'  => 'Pick the database connection and the table that holds your global (yell) chat messages. Load the columns, then map them below.',
        'join'               => 'Optional left join',
        'join_description'   => 'Join another table — e.g. _Char — to resolve the character name, ID and avatar (RefObjID) from a character id stored in your source table. Cross-database joins on the same SQL Server instance are supported.',
        'mapping'             => 'Field mapping',
        'mapping_description' => 'Map your source (and joined) columns onto the fields the frontend expects. Comment and Event Time are required; Character Name, Character ID and RefObjID (avatar) are optional but recommended.',
    ],

    'fields' => [
        'enabled'                    => 'Enable VSRO Global History',
        'enabled_description'        => 'When off, all Global History surfaces stay hidden on VSRO.',
        'connection'                 => 'Database connection',
        'table'                      => 'Source table',
        'table_description'          => 'The table that stores global/yell messages. Use the refresh button to load its columns.',
        'filter_column'              => 'Filter column (optional)',
        'filter_column_description'  => 'Restrict rows to global messages only, e.g. a channel/type column (iSRO uses TargetName = [YELL]). Leave empty to use the whole table.',
        'filter_value'               => 'Filter value (optional)',
        'filter_value_description'   => 'The value the filter column must equal, e.g. [YELL].',
        'cache_ttl'                  => 'Cache TTL',
        'cache_ttl_description'      => 'How long widget/character results are cached, in seconds. The main page is not cached.',
        'join_enabled'               => 'Enable left join',
        'join_enabled_description'   => 'Join a second table to pull additional columns (e.g. character name/avatar).',
        'join_connection'            => 'Join connection',
        'join_table'                 => 'Join table',
        'join_table_description'     => 'The table to join, e.g. _Char. Use the refresh button to load its columns.',
        'join_local_key'             => 'Local key (source column)',
        'join_local_key_description' => 'Column on the source table, e.g. CharID.',
        'join_foreign_key'           => 'Foreign key (join column)',
        'join_foreign_key_description' => 'Matching column on the join table, e.g. CharID.',
        'map_comment'                => 'Message → Comment',
        'map_comment_description'    => 'The message text column. Required.',
        'map_eventtime'              => 'Timestamp → EventTime',
        'map_eventtime_description'  => 'The date/time column used for ordering (newest first). Required.',
        'map_charname'               => 'Character name → CharName',
        'map_charname_description'   => 'The sender character name (e.g. _Char.CharName16 via join).',
        'map_charid'                 => 'Character ID → CharID',
        'map_charid_description'     => 'Used to link to the character page (e.g. _Char.CharID via join).',
        'map_refobjid'               => 'Model ID → RefObjID',
        'map_refobjid_description'   => 'Character model id used to render the avatar (e.g. _Char.RefObjID via join).',
    ],

    'mapping' => [
        'base_group' => 'Source table columns',
        'join_group' => 'Join table columns',
    ],

    'actions' => [
        'load_columns' => 'Load columns',
        'test'         => 'Test output',
        'save'         => 'Save',
    ],

    'notifications' => [
        'saved_title'            => 'Saved',
        'saved_message'          => 'The Global History source configuration has been saved.',
        'missing_table_title'    => 'No table selected',
        'missing_table_message'  => 'Please select a table before loading columns.',
        'columns_loaded_title'   => 'Columns loaded',
        'columns_loaded_message' => ':count column(s) loaded.',
        'columns_error_title'    => 'Could not load columns',
        'no_columns_message'     => 'No columns were found for the selected table.',
    ],

    'preview' => [
        'not_configured'      => 'The source could not be built from the current values. Check the connection, table, join and mapping.',
        'missing_source'      => 'Please select a database connection and a source table first (then load its columns).',
        'missing_required_map' => 'Please map at least Message → Comment and Timestamp → EventTime.',
        'error_title'         => 'Query failed',
        'title'               => 'Test output',
        'rows'                => 'rows',
        'live_hint'           => 'Based on the current (unsaved) form values.',
        'preview_limit_hint'  => 'Preview is limited to 10 rows.',
    ],
];
