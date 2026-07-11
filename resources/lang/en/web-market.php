<?php

return [
    'character' => 'Character',
    'purchase_as_character' => 'Purchase as Character',
    'online' => 'Online',
    'inventory' => 'Inventory',
    'storage_label' => 'Storage',
    'web_storage' => 'Web Storage',
    'unknown_item' => 'Unknown Item',
    'buy' => 'Buy',
    'sell' => 'Sell',
    'cancel' => 'Cancel',
    'listed' => 'Listed',
    'hours' => 'hours',
    'select_character' => 'Select a character',
    'loading' => 'Loading...',
    'transfer_to_storage' => 'Transfer to Web Storage',
    'return' => 'Return',
    'return_to_inventory' => 'Return to Inventory',
    'return_to_storage' => 'Return to Storage',
    'cancel_listing' => 'Cancel Listing',

    'tabs' => [
        'marketplace' => 'Marketplace',
        'storage' => 'Web Storage',
        'my_listings' => 'My Listings',
    ],

    'filter' => [
        'search' => 'Search item...',
        'all_currencies' => 'All currencies',
        'gold' => 'Gold only',
        'silk' => 'Silk only',
        'seller' => 'Filter by seller...',
    ],

    'sort' => [
        'newest' => 'Newest first',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'expires_soon' => 'Expires soon',
    ],

    'marketplace' => [
        'empty' => 'No items are listed yet. Be the first to sell something!',
    ],

    'storage' => [
        'empty' => 'Your web storage is empty.',
        'no_items' => 'No items found in inventory or storage.',
    ],

    'my_listings' => [
        'empty' => 'You have no listings yet.',
    ],

    'table' => [
        'item' => 'Item',
        'price' => 'Price',
        'status' => 'Status',
        'seller' => 'Seller',
        'buyer' => 'Buyer',
        'expires' => 'Expires',
        'actions' => 'Actions',
    ],

    'modal' => [
        'buy' => [
            'title' => 'Confirm Purchase',
            'confirm' => 'Buy Now',
            'balance' => 'Your Balance',
            'delivered_to_storage' => 'Item will be delivered to your Web Storage.',
        ],
        'transfer' => [
            'title' => 'Transfer to Web Storage',
            'body' => 'Transfer this item from your :source to Web Storage?',
            'confirm' => 'Transfer',
        ],
        'return' => [
            'title' => 'Return Item',
            'body' => 'Item will be returned to your inventory. If your inventory is full, it will go to storage instead.',
            'confirm' => 'Return',
        ],
        'sell' => [
            'title' => 'Create Listing',
            'price_type' => 'Currency',
            'price' => 'Price',
            'price_placeholder' => 'Enter price...',
            'duration' => 'Duration',
            'max_duration' => 'Maximum: :max hours',
            'description' => 'Description (optional)',
            'description_placeholder' => 'Describe the item...',
            'confirm' => 'List for Sale',
        ],
        'cancel' => [
            'title' => 'Cancel Listing',
            'body' => 'Are you sure you want to cancel this listing? The item will be returned to your Web Storage.',
            'confirm' => 'Cancel Listing',
        ],
    ],

    'success' => [
        'purchased' => 'Successfully purchased :item! Check your Web Storage.',
        'transferred_to_storage' => 'Item transferred to Web Storage.',
        'transferred_from_storage' => 'Item returned to your inventory.',
        'listing_created' => 'Your listing is now active on the Marketplace.',
        'listing_cancelled' => 'Listing cancelled. Item returned to Web Storage.',
    ],

    'error' => [
        'unexpected_error' => 'An unexpected error occurred. Please try again.',
        'web_storage_disabled' => 'Web Storage is currently disabled.',
        'marketplace_disabled' => 'The Marketplace is currently disabled.',
        'character_not_owned' => 'This character does not belong to your account.',
        'character_must_be_offline' => 'Your character must be offline to perform this action.',
        'item_not_found' => 'Item not found.',
        'item_not_tradeable' => 'This item cannot be traded.',
        'item_not_owned' => 'This item does not belong to you.',
        'item_already_listed' => 'This item is already listed on the Marketplace.',
        'item_is_listed' => 'This item is currently listed. Cancel the listing first.',
        'listing_not_found' => 'Listing not found.',
        'listing_not_active' => 'This listing is no longer active.',
        'listing_expired' => 'This listing has expired.',
        'cannot_buy_own' => 'You cannot buy your own listing.',
        'cannot_buy_own_listing' => 'You cannot purchase your own listing.',
        'insufficient_balance' => 'Insufficient balance to complete this purchase.',
        'price_type_not_allowed' => 'This currency type is not allowed.',
        'price_exceeds_maximum' => 'Price exceeds the maximum allowed value.',
        'price_too_low' => 'Price must be at least 1.',
        'price_invalid' => 'Please enter a valid price.',
        'duration_invalid' => 'Invalid listing duration.',
        'storage_limit_reached' => 'Your Web Storage is full. Transfer or sell some items first.',
        'account_listing_limit_reached' => 'You have reached the maximum number of active listings for your account.',
        'character_listing_limit_reached' => 'This character has reached the maximum number of active listings.',
        'no_empty_slot' => 'Your inventory and storage are both full. Please make space before returning this item.',
        'item_slot_changed' => 'The item slot has changed. Please try again.',
        'procedure_failed' => 'The item transfer procedure failed. Please contact an administrator.',
        'seller_not_found' => 'The seller account could not be found.',
    ],
];
