<?php

return [
    'navigation' => 'Page Settings',
    'navigation_group' => 'Configuration',

    'form' => [
        'key' => 'Settings Key',
        'label' => 'Label',
        'description' => 'Description',
        'type' => 'Field Type',
        'value' => 'Value',

        'tabs' => [
            'general' => 'General',
            'silkroad_online' => 'Silkroad Online',
            'page_info' => 'Page Info',
            'design' => 'Design',
            'features' => 'Features',
            'partners' => 'Partners',
            'referral' => 'Referral',
            'contact' => 'Contact',
            'social_media' => 'Social Media',
            'ticket_system' => 'Ticket System',
        ],

        'page_info' => [
            'site_title' => 'Site Title',
            'site_title_placeholder' => 'Website title',
            'site_description' => 'Site Description',
            'site_description_placeholder' => 'Meta description for search engines',
            'site_keywords' => 'SEO Keywords',
            'site_keywords_placeholder' => 'Separated by comma',
            'frontend_languages' => 'Frontend Languages',
            'frontend_languages_description' => 'Select the languages available in the frontend language switch.',
        ],

        'silkroad_online' => [
            'general_settings' => 'General Settings',
            'rate_settings' => 'Rate Settings',
            'other_settings' => 'Other Settings',
            'internal_settings' => 'Internal Settings',
            'ip_settings' => 'IP & HWID Settings',
            'shard_id' => 'Shard ID',
            'shard_id_description' => 'Unique identifier of the gameShardID (e.g. VSRO 64, ISRO 323)',
            'max_player' => 'Max Player',
            'cap' => 'Level Cap',
            'exp_sp' => 'EXP & SP Rate',
            'party_exp' => 'Party EXP Rate',
            'gold_drop_rate' => 'Gold Drop Rate',
            'drop_rate' => 'Item Drop Rate',
            'trade_rate' => 'Trade Rate',
            'race' => 'Available Races',
            'hwid_limit' => 'HWID Limit',
            'hwid_limit_description' => 'Maximum accounts per HWID',
            'ip_limit' => 'IP Limit',
            'ip_limit_description' => 'Maximum accounts per IP',
            'fortress_war' => 'Fortress War',
        ],

        'design' => [
            'logo' => 'Logo',
            'favicon' => 'Favicon',
            'favicon_description' => 'Must be at least 512x512 pixels with 1:1 aspect ratio (square)',
            'background_image' => 'Background Image',
        ],

        'features' => [
            'registration_open' => 'Registration Open',
            'registration_open_description' => 'Can new users register?',
            'email_verification_required' => 'Email Verification Required',
            'email_verification_required_description' => 'Require users to verify their email address after registration. Hint: when toggle back on after being disabled, all existing users will be treated as if they have verified their email, so they will not be forced to verify their email until they change their email or password.',
            'maintenance_message' => 'Maintenance Message',
            'maintenance_message_placeholder' => 'Message when website is in maintenance',
            'tos_enabled' => 'Enable Terms of Service',
            'tos_enabled_description' => 'Require users to accept the Terms of Service during registration.',
            'tos_text' => 'Terms of Service Text',
            'tos_text_placeholder' => 'Enter your Terms of Service here...',
            'login_with_name' => 'Login with Name',
            'login_with_name_description' => 'Allow users to log in with their username instead of email address.',
        ],

        'partners' => [
            'partners' => 'Partners',
            'partner_name' => 'Partner Name',
            'partner_logo' => 'Partner Logo',
            'partner_url' => 'Partner Website URL',
            'partner_description' => 'Partner Description',
        ],

        'contact' => [
            'contact_email' => 'Contact Email',
            'contact_phone' => 'Contact Phone',
            'contact_address' => 'Address',
        ],

        'social_media' => [
            'social_facebook' => 'Facebook URL',
            'social_twitter' => 'Twitter/X URL',
            'social_instagram' => 'Instagram URL',
            'social_discord' => 'Discord URL',
            'discord_id' => 'Discord ID',
            'discord_id_description' => 'Your Discord server ID, used for the Discord widget on the homepage. You can get this by right-clicking your server icon in Discord, selecting "Copy ID", and pasting it here.',
        ],

        'referral' => [
            'enabled' => 'Enable Referral System',
            'enabled_description' => 'Allow players to invite others and earn Silk rewards when the referred player reaches the required character level.',
            'min_level' => 'Minimum Character Level',
            'min_level_description' => 'The referred player\'s highest character must reach this level before the referral is counted as valid.',
            'silk_reward' => 'Silk Reward per Referral',
            'silk_reward_description' => 'Amount of Silk to award to the referrer for each valid referral.',
            'silk_type' => 'Silk Type',
        ],

        'ticket_system' => [
            'enabled' => 'Enable Ticket System',
            'enabled_description' => 'Allow users to submit support tickets',
        ],

        'field_types' => [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'email' => 'Email',
            'url' => 'URL',
            'number' => 'Number',
            'toggle' => 'Toggle',
            'select' => 'Select',
            'file' => 'File',
            'repeater' => 'Repeater (Array)',
            'json' => 'JSON',
        ],
    ],

    'table' => [
        'key' => 'Key',
        'label' => 'Label',
        'type' => 'Type',
        'value' => 'Value',
        'updated_at' => 'Last Updated',
        'filter_type' => 'Filter by Type',
    ],

    'notifications' => [
        'created_title' => 'Success',
        'created_message' => 'Setting created successfully.',
        'updated_title' => 'Success',
        'updated_message' => 'Settings updated successfully.',
        'deleted_title' => 'Success',
        'deleted_message' => 'Setting deleted successfully.',
    ],

    'actions' => [
        'save' => 'Save all settings',
    ],
];
