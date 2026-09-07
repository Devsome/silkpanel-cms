<?php

return [
    'navigation' => 'Online Players Source',
    'title' => 'Online Players Source',

    'sections' => [
        'status' => 'Status',
        'status_description' => 'By default the online player count is read from the standard `_ShardCurrentUser` table. Enable the override to read it from any other database, table and column instead. `_ShardCurrentUser` always stays the fallback.',
        'source' => 'Source',
        'source_description' => 'Choose the database connection, the table and the column that holds the online player count, plus how that column should be aggregated.',
        'ordering' => 'Row selection',
        'ordering_description' => 'Only used for "Value of a single row": the row read is picked by this ordering, e.g. the newest log entry.',
        'filter' => 'Filter (optional)',
        'filter_description' => 'Restrict the query to rows where a column matches a value, e.g. only a specific shard.',
    ],

    'fields' => [
        'enabled' => 'Use a custom online player source',
        'enabled_description' => 'When disabled — or when the custom query fails — the count is read from `_ShardCurrentUser`.',
        'connection' => 'Database connection',
        'connection_description' => 'One of the configured `sro_*` connections.',
        'table' => 'Table',
        'table_description' => 'The table the online player count is read from.',
        'column' => 'Column',
        'column_description' => 'The column the selected aggregate is applied to.',
        'aggregate' => 'Aggregate',
        'aggregate_description' => 'How the column is turned into a single number.',
        'order_column' => 'Order by column',
        'order_column_description' => 'For example a log date, so the newest row is used.',
        'order_direction' => 'Direction',
        'filter_column' => 'Filter column',
        'filter_column_description' => 'Leave empty to query the whole table.',
        'filter_value' => 'Filter value',
        'filter_value_description' => 'The value the filter column has to match.',
    ],

    'aggregates' => [
        'value' => 'Value of a single row (like _ShardCurrentUser)',
        'count_rows' => 'Count rows — COUNT(*)',
        'count' => 'Count values — COUNT(column)',
        'count_distinct' => 'Count unique values — COUNT(DISTINCT column)',
        'sum' => 'Sum — SUM(column)',
        'max' => 'Highest value — MAX(column)',
    ],

    'order_directions' => [
        'desc' => 'Descending (newest first)',
        'asc' => 'Ascending (oldest first)',
    ],

    'actions' => [
        'test' => 'Test source',
    ],

    'notifications' => [
        'test_success_title' => 'Source works',
        'test_success_message' => 'The custom source returned :count online players (fallback `_ShardCurrentUser`: :fallback).',
        'test_failed_title' => 'Source failed',
        'test_incomplete_title' => 'Source incomplete',
        'test_incomplete_message' => 'Select a connection, a table and — for the chosen aggregate — a column first.',
    ],
];
