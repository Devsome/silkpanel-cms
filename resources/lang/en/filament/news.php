<?php

return [
    'navigation' => 'News',

    'form' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'content' => 'Content',
        'thumbnail' => 'Thumbnail',
        'thumbnail_helper' => 'Upload an image file (max 5MB) to be used as the thumbnail for this news item.',
        'published_at' => 'Published At',
        'published_at_helper' => 'Select the date and time when this news item should be published. If left blank, it will be published immediately.',
    ],

    'notifications' => [],

    'section' => [
        'news_details_title' => 'News Details',
        'news_details_description' => 'Provide detailed information about the news.',
    ],

    'table' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'published_at' => 'Published At',
        'thumbnail' => 'Thumbnail',
        'deleted_at' => 'Deleted At',
        'empty' => 'No news found',
        'empty_description' => 'There are no news to display.',
    ],

    'edit' => [],
];
