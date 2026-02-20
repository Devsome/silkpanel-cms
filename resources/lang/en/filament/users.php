<?php

return [
    'title' => 'Users',
    'subheading' => 'Manage your users and their permissions.',
    'navigation' => 'Users',
    'navigation_parent' => 'Settings',

    'form' => [
        'name' => 'Name',
        'silkroad_id' => 'Silkroad ID',
        'jid' => 'JID',
        'reflink' => 'Referral Link',
        'acc_play_time' => 'Account Play Time',
        'sec_primary' => 'Primary Security',
        'sec_content' => 'Content Security',
        'email' => 'Email',
        'password' => 'Password',
        'email_verified_at' => 'Email verified at',
        'created_at' => 'Created at',
        'last_modified_at' => 'Last modified at',
        'roles' => 'Roles',
    ],

    'table' => [
        'name' => 'Name',
        'jid' => 'JID',
        'silkroad_id' => 'Silkroad ID',
        'shard_users_count' => 'Characters',
        'email' => 'Email address',
        'roles' => 'Roles',
        'email_verified_at' => 'Email verified at',
    ],

    'edit' => [
        'reset_password' => 'Reset password',
        'modal_heading' => 'Send password reset email',
        'modal_description' => 'Are you sure you want to send a password reset email to this user? This will allow them to set a new password for their account.',
        'success_message' => 'Password reset email sent successfully.',
        'error_message' => 'Failed to send password reset email.',
    ],

    'shard' => [
        'charid' => 'Character ID',
        'charname' => 'Character Name',
        'level' => 'Level',
        'job_nickname' => 'Job Nickname',
    ],
];
