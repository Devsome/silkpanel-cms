<?php

return [
    // Navigation
    'navigation_group' => 'Donation',
    'navigation_payment_providers' => 'Payment Providers',
    'navigation_packages' => 'Packages',
    'navigation_transactions' => 'Transactions',

    // Payment Provider Form
    'provider_section_settings' => 'Provider Settings',
    'provider_section_settings_description' => 'Configure the payment provider. API keys are stored in the .env file.',
    'provider_field_provider' => 'Provider',
    'provider_field_display_name' => 'Display Name',
    'provider_field_description' => 'Description',
    'provider_field_active' => 'Active',
    'provider_field_inactive' => 'Inactive',
    'provider_field_active_helper' => 'Enable or disable this payment provider for donations.',
    'provider_field_sort_order' => 'Sort Order',
    'provider_section_assigned_packages' => 'Assigned Packages',
    'provider_section_assigned_packages_description' => 'Select which donation packages are available for this provider.',
    'provider_field_packages' => 'Packages',
    'provider_section_api_config' => 'API Configuration',
    'provider_section_api_config_description' => 'API keys are stored in your .env file for security. The values below are read-only.',
    'provider_env_notice' => 'Configure this provider\'s keys in your .env file.',
    'provider_env_not_configured' => '(not configured)',

    // HipoCard Denomination Mapping
    'hipocard_denomination_section' => 'Denomination → Silk Mapping',
    'hipocard_denomination_section_description' => 'Map ePin card face values to custom silk amounts. If no entry matches the redeemed card value, the global silk-per-unit rate is used as fallback.',

    // Payment Provider Table
    'provider_table_provider' => 'Provider',
    'provider_table_slug' => 'Slug',
    'provider_table_active' => 'Active',
    'provider_table_order' => 'Order',
    'provider_table_last_updated' => 'Last Updated',
    'provider_table_empty_heading' => 'No payment providers',
    'provider_table_empty_description' => 'Payment providers will appear here after seeding.',

    // Donation Package Form
    'package_section_details' => 'Package Details',
    'package_field_name' => 'Name',
    'package_field_description' => 'Description',
    'package_field_image' => 'Image',
    'package_field_sort_order' => 'Sort Order',
    'package_field_active' => 'Active',
    'package_section_pricing' => 'Pricing & Silk',
    'package_field_price' => 'Price',
    'package_field_currency' => 'Currency',
    'package_field_silk_amount' => 'Silk Amount',
    'package_field_silk_type' => 'Silk Type',
    'package_field_payment_providers' => 'Payment Providers',

    // Donation Package Table
    'package_table_name' => 'Name',
    'package_table_silk' => 'Silk',
    'package_table_price' => 'Price',
    'package_table_active' => 'Active',
    'package_table_order' => 'Order',
    'package_table_image' => 'Image',
    'package_table_empty_heading' => 'No donation packages',
    'package_table_empty_description' => 'Create your first donation package to start accepting donations.',

    // Donation Table
    'donation_table_id' => '#',
    'donation_table_user' => 'User',
    'donation_table_package' => 'Package',
    'donation_table_provider' => 'Provider',
    'donation_table_amount' => 'Amount',
    'donation_table_silk' => 'Silk',
    'donation_table_status' => 'Status',
    'donation_table_transaction_id' => 'Transaction ID',
    'donation_table_ip' => 'IP',
    'donation_table_date' => 'Date',
    'donation_table_empty_heading' => 'No donations yet',
    'donation_table_empty_description' => 'Donation transactions will appear here.',
    'donation_filter_provider' => 'Provider',

    // View Donation
    'view_section_transaction' => 'Transaction Details',
    'view_section_payment' => 'Payment',
    'view_section_timestamps' => 'Timestamps',
    'view_label_id' => '#',
    'view_label_user' => 'User',
    'view_label_package' => 'Package',
    'view_label_provider' => 'Provider',
    'view_label_transaction_id' => 'Transaction ID',
    'view_label_status' => 'Status',
    'view_label_amount' => 'Amount',
    'view_label_currency' => 'Currency',
    'view_label_silk_amount' => 'Silk Amount',
    'view_label_silk_type' => 'Silk Type',
    'view_label_ip_address' => 'IP Address',
    'view_label_created' => 'Created',
    'view_label_completed' => 'Completed',
    'view_label_updated' => 'Updated',
];
