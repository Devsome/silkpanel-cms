<?php

return [
    'navigation' => 'Templates',
    'title' => 'Manage Templates',

    'blade' => [
        'section' => 'Upload New Template',
        'download_starter' => 'Download Starter Template',
        'download_helper' => 'Download a skeleton template to get started with custom template development.',
        'installed_templates' => 'Installed Templates',
        'no_templates_found' => 'No templates found',
        'no_templates_description' => 'There are no templates to display. Create the basic template first or upload a custom template.',

        'version' => 'v :version',
        'by' => 'by :author',

        'active_badge' => 'Active',
        'activate_button' => 'Activate',
        'deactivate_button' => 'Deactivate',
        'delete_button' => 'Delete',
    ],

    'form' => [
        'template_zip' => 'Template ZIP File',
        'template_zip_helper' => 'Upload a ZIP file containing your template. The ZIP should have a single root folder with the template files inside.',
        'upload_button' => 'Upload & Install Template',
    ],

    'notifications' => [
        'title_error' => 'Error',
        'title_success' => 'Success',
        'please_select_file' => 'Please select a ZIP file to upload.',
        'upload_error' => 'Could not find the uploaded file.',
        'upload_success' => 'Template uploaded and installed successfully.',
        'activate_success' => "Template ':slug' is now active.",
        'deactivate_success' => 'Template deactivated. Using default views.',
        'delete_success' => "Template ':slug' has been deleted.",
    ],

];
