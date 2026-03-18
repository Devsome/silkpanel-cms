<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * Get a setting by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a setting by key
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $label = null, ?string $description = null): Setting
    {
        return Setting::set($key, $value, $type, $label, $description);
    }

    /**
     * Get all settings
     */
    public static function all(): array
    {
        return Setting::getAllSettings();
    }

    /**
     * Check if setting exists
     */
    public static function has(string $key): bool
    {
        return Setting::where('key', $key)->exists();
    }

    /**
     * Delete a setting by key
     */
    public static function delete(string $key): bool
    {
        return Setting::deleteByKey($key);
    }

    /**
     * Seed default settings
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // Page Info
            [
                'key' => 'site_title',
                'value' => 'My Website',
                'type' => 'text',
                'label' => 'Site Title',
                'description' => 'Main title of the website',
            ],
            [
                'key' => 'site_description',
                'value' => 'Welcome to my website',
                'type' => 'textarea',
                'label' => 'Site Description',
                'description' => 'Meta description for search engines',
            ],
            [
                'key' => 'site_keywords',
                'value' => 'website, cms, keywords',
                'type' => 'text',
                'label' => 'SEO Keywords',
                'description' => 'Keywords for SEO',
            ],
            // Silkroad Online
            [
                'key' => 'sro_max_player',
                'value' => 500,
                'type' => 'number',
                'label' => 'Max Player',
                'description' => 'Maximum concurrent players',
            ],
            [
                'key' => 'sro_cap',
                'value' => 110,
                'type' => 'number',
                'label' => 'Level Cap',
                'description' => 'Maximum character level',
            ],
            [
                'key' => 'sro_exp_sp',
                'value' => 1,
                'type' => 'number',
                'label' => 'EXP & SP Rate',
                'description' => 'Experience and Skill Point rate',
            ],
            [
                'key' => 'sro_party_exp',
                'value' => 1,
                'type' => 'number',
                'label' => 'Party EXP Rate',
                'description' => 'Party experience rate',
            ],
            [
                'key' => 'sro_gold_drop_rate',
                'value' => 1.0,
                'type' => 'number',
                'label' => 'Gold Drop Rate',
                'description' => 'Gold drop rate multiplier',
            ],
            [
                'key' => 'sro_drop_rate',
                'value' => 1.0,
                'type' => 'number',
                'label' => 'Item Drop Rate',
                'description' => 'Item drop rate multiplier',
            ],
            [
                'key' => 'sro_trade_rate',
                'value' => 1.0,
                'type' => 'number',
                'label' => 'Trade Rate',
                'description' => 'Trade rate multiplier',
            ],
            [
                'key' => 'sro_race',
                'value' => ['china', 'europe'],
                'type' => 'json',
                'label' => 'Available Races',
                'description' => 'Enabled character races',
            ],
            [
                'key' => 'sro_hwid_limit',
                'value' => 3,
                'type' => 'number',
                'label' => 'HWID Limit',
                'description' => 'Maximum accounts per HWID',
            ],
            [
                'key' => 'sro_ip_limit',
                'value' => 3,
                'type' => 'number',
                'label' => 'IP Limit',
                'description' => 'Maximum accounts per IP',
            ],
            [
                'key' => 'sro_fortress_war',
                'value' => ['bandit', 'hotan', 'jangan'],
                'type' => 'json',
                'label' => 'Fortress War',
                'description' => 'Enabled fortress wars',
            ],
            // Features
            [
                'key' => 'registration_open',
                'value' => true,
                'type' => 'toggle',
                'label' => 'Registration Open',
                'description' => 'Can new users register?',
            ],
            [
                'key' => 'email_verification_required',
                'value' => true,
                'type' => 'toggle',
                'label' => 'Email Verification Required',
                'description' => 'Require users to verify their email address',
            ],
            [
                'key' => 'maintenance_message',
                'value' => null,
                'type' => 'textarea',
                'label' => 'Maintenance Message',
                'description' => 'Message during maintenance',
            ],
            [
                'key' => 'tos_enabled',
                'value' => false,
                'type' => 'toggle',
                'label' => 'Enable Terms of Service',
                'description' => 'Require users to accept the Terms of Service during registration.',
            ],
            [
                'key' => 'tos_text',
                'value' => null,
                'type' => 'textarea',
                'label' => 'Terms of Service Text',
                'description' => 'Enter your Terms of Service here...',
            ],
            // Contact
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'email',
                'label' => 'Contact Email',
                'description' => 'Public contact email',
            ],
            [
                'key' => 'contact_phone',
                'value' => null,
                'type' => 'text',
                'label' => 'Contact Phone',
                'description' => 'Public phone number',
            ],
            [
                'key' => 'contact_address',
                'value' => null,
                'type' => 'text',
                'label' => 'Address',
                'description' => 'Company address',
            ],
            // Social Media
            [
                'key' => 'social_facebook',
                'value' => null,
                'type' => 'url',
                'label' => 'Facebook',
                'description' => 'Facebook profile URL',
            ],
            [
                'key' => 'social_twitter',
                'value' => null,
                'type' => 'url',
                'label' => 'Twitter/X',
                'description' => 'Twitter/X profile URL',
            ],
            [
                'key' => 'social_instagram',
                'value' => null,
                'type' => 'url',
                'label' => 'Instagram',
                'description' => 'Instagram profile URL',
            ],
            [
                'key' => 'social_discord',
                'value' => null,
                'type' => 'url',
                'label' => 'Discord',
                'description' => 'Discord profile URL',
            ],
            // Partners
            [
                'key' => 'partners',
                'value' => [
                    [
                        'name' => 'elitepvpers.com',
                        'logo' => null,
                        'url' => 'https://elitepvpers.com',
                        'description' => 'Elitepvpers.com',
                    ],
                    [
                        'name' => 'vote4rewards.de',
                        'logo' => null,
                        'url' => 'https://vote4rewards.de',
                        'description' => 'vote4rewards.de',
                    ],
                ],
                'type' => 'repeater',
                'label' => 'Partners',
                'description' => 'List of partners',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'] ?? null,
                $setting['label'] ?? null,
                $setting['description'] ?? null
            );
        }
    }
}
