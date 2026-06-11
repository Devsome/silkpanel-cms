<?php

return [
    'navigation' => 'Pages',

    'custom_procedures' => [
        'navigation' => 'Custom Procedures',
        'navigation_group' => 'Configuration',

        'sections' => [
            'action_mapping' => 'Action Mapping',
            'action_mapping_description' => 'Configure which CMS action should call which MSSQL procedure.',
            'parameter_mapping' => 'Parameter Mapping',
            'parameter_mapping_description' => 'Laravel keys are fixed per action. Only the MSSQL parameter name and execution order are configurable.',
            'additional_parameters' => 'Additional Parameters (Optional)',
            'additional_parameters_description' => 'Add extra payload keys for procedures that require more inputs than the fixed action keys. Optional default value is used when the key is missing at runtime.',
            'procedure_preview' => 'Procedure Preview',
            'procedure_preview_description' => 'Signature and SQL preview from the selected connection.',
            'testing' => 'Testing',
            'testing_description' => 'Run the configured procedure with test values.',
        ],

        'fields' => [
            'action' => 'Action',
            'action_label' => 'Action Label',
            'is_active' => 'Enable procedure for this action',
            'use_fallback' => 'Use default Laravel fallback when procedure fails',
            'database_connection' => 'Database connection',
            'procedure_picker' => 'Available procedures',
            'procedure_picker_help' => 'Optional: pick a procedure from the selected database connection.',
            'procedure_name' => 'Stored procedure name',
            'procedure_name_placeholder' => '[dbo].[YOUR_PROCEDURE] or _ADD_ITEM_EXTERN',
            'mapped_parameters' => 'Mapped parameters',
            'laravel_key' => 'Laravel key',
            'procedure_param' => 'Procedure parameter',
            'procedure_param_placeholder' => '@PlayerID',
            'position' => 'Position',
            'extra_mapped_parameters' => 'Extra mapped parameters',
            'payload_key' => 'Payload key',
            'payload_key_placeholder' => 'service_company',
            'extra_procedure_param_placeholder' => '@ServiceCompany',
            'default_value' => 'Default value (optional)',
            'default_value_placeholder' => '1 or channel_a',
            'procedure_signature' => 'Detected signature',
            'test_values' => 'Test values',
            'test_value' => 'Value',
            'test_value_placeholder' => 'Enter test value',
        ],

        'actions' => [
            'add_extra_parameter' => 'Add extra parameter',
        ],

        'table' => [
            'action' => 'Action',
            'procedure' => 'Procedure',
            'connection' => 'Connection',
            'status' => 'Status',
            'fallback' => 'Fallback',
            'error' => 'Error',
            'executed' => 'Executed',
            'success' => 'Success',
            'failed' => 'Failed',
            'fallback_used' => 'Used',
            'fallback_no' => 'No',
        ],

        'notifications' => [
            'validation_failed_title' => 'Validation failed',
            'mapping_saved_title' => 'Procedure mapping saved',
            'mapping_saved_body' => 'Your procedure configuration has been updated.',
            'no_action_title' => 'No action selected',
            'test_success_title' => 'Procedure test successful',
            'test_failed_title' => 'Procedure test failed',
        ],

        'messages' => [
            'completed_without_output' => 'The procedure call completed without additional output.',
            'procedure_name_missing' => 'No procedure name configured for this action. Please set a procedure name first.',
            'procedure_mapping_inactive' => 'This action has no active procedure mapping.',
            'custom_procedures_disabled' => 'Custom procedures are globally disabled in settings.',
            'procedure_not_found_on_connection' => 'Procedure not found on selected connection.',
            'preview_not_supported' => 'Preview for this database driver is not supported yet.',
            'preview_load_failed' => 'Could not load procedure preview: :error',
        ],

        'connections' => [
            'sro_shard' => 'SRO Shard',
            'sro_account' => 'SRO Account',
            'sro_log' => 'SRO Log',
            'sro_custom' => 'SRO Custom',
            'sro_portal' => 'SRO Portal',
            'mysql' => 'MySQL',
        ],
    ],

    'translated' => 'Translated',
    'not_translated' => 'Not Translated',

    'table' => [
        'id' => '#',
        'slug' => 'Slug',
        'languages_status' => 'Languages',
        'published_at' => 'Published At',
        'deleted_at' => 'Deleted At',
        'delete' => 'Delete',
        'restore' => 'Restore',
        'empty_heading' => 'No pages found',
        'empty_description' => 'Create your first page to get started.',
    ],

    'edit' => [
        'sections' => [
            'translations' => 'Translations',
            'page' => 'Page',
            'content_blocks' => 'Content Blocks',
            'content_blocks_help' => 'Use content blocks to build the page content. You can create different types of blocks and reorder them as needed.',
        ],
        'slug' => 'Slug',
        'slug_help' => 'The slug is used to generate the URL for the page.',
        'published_at' => 'Published At',
        'select_language' => 'Select Language',
        'select_language_help' => 'Select a language to edit the translation.',

        'page_content' => 'Page Content',
        'page_title' => 'Page Title',
        'seo_title' => 'SEO Title',
        'seo_title_max_length' => ':length / 60 characters',
        'seo_description' => 'SEO Description',
        'seo_description_max_length' => ':length / 160 characters',
        'is_complete' => 'Is Complete',
        'is_complete_help' => 'Indicates whether the translation is complete.',

        'builder' => [
            'headline' => 'Headline',
            'subheadline' => 'Subheadline',
            'headline_level' => 'Headline Level',
            'heading_one' => 'Heading 1',
            'heading_two' => 'Heading 2',
            'heading_three' => 'Heading 3',
            'paragraph' => 'Paragraph',
            'rich_text' => 'Rich Text',
            'bbcode' => 'BBCode',
            'bbcode_help' => 'Available tags: [b], [i], [u], [strike], [highlight], [quote], [quote=name], [code], [email], [email=...], [url], [url=...], [img], [img=WIDTHxHEIGHT], [color=...], [font=...], [size=...], [small], [big], [sub], [sup], [spoiler], [spoiler=title], [left], [center], [right], [indent], [list], [list=1|a|A|i|I], [table], [table=head], [tr], [th], [td], [video], [youtube], [hr], [hr=0|1|2]WIDTH%[/hr].',
            'image' => 'Image',
            'alt_text' => 'Alt Text',
            'content' => 'Content',
        ],
    ],

];
