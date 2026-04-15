<?php

return [
    'navigation_label' => 'Event Timers',
    'model_label' => 'Event Timer',
    'plural_model_label' => 'Event Timers',

    'section' => [
        'details' => 'Timer Details',
        'hourly' => 'Hourly Schedule',
        'hourly_description' => 'Runs at selected hours every day. E.g. Capture the Flag every hour at :30.',
        'weekly' => 'Weekly Schedule',
        'weekly_description' => 'Runs on specific days at a fixed time. E.g. Fortress War on Sunday at 20:00.',
        'static' => 'Static Display',
        'static_description' => 'Shows a fixed text instead of a countdown.',
    ],

    'form' => [
        'name' => 'Name',
        'type' => 'Schedule Type',
        'icon' => 'Icon',
        'image' => 'Image',
        'image_helper' => 'Optional image (50×50) shown instead of icon.',
        'sort_order' => 'Sort Order',
        'days' => 'Days',
        'hours' => 'Hours',
        'hour' => 'Hour (0-23)',
        'hour_helper' => 'The hour of the day the event starts.',
        'min' => 'Minute',
        'min_helper' => 'The minute past the hour the event starts.',
        'time' => 'Display Text',
    ],

    'type' => [
        'hourly' => 'Hourly',
        'weekly' => 'Weekly',
        'static' => 'Static',
    ],

    'days' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],

    'table' => [
        'name' => 'Name',
        'type' => 'Type',
        'icon' => 'Icon',
        'schedule' => 'Schedule',
        'updated_at' => 'Updated',
        'empty' => 'No Event Timers',
        'empty_description' => 'Create your first event timer to get started.',
    ],
];
