<?php

$GLOBALS['TL_CRON']['minutely'][] = ['CatalogManager\Mailer\Cronjob', 'mailer'];
$GLOBALS['TL_CRON']['minutely'][] = ['CatalogManager\Mailer\Cronjob', 'reminder'];

$GLOBALS['BE_MOD']['catalog-manager-extensions']['cm-mailer'] = [

    'name' => 'catalog-manager-mailer',
    'icon' => 'system/modules/catalog-manager-mailer/assets/icon.svg',

    'tables' => [

        'tl_mailer',
        'tl_mailer_queue'
    ]
];

$GLOBALS['BE_MOD']['catalog-manager-extensions']['cm-reminder'] = [

    'name' => 'catalog-manager-reminder',
    'icon' => 'system/modules/catalog-manager-mailer/assets/icon.svg',

    'tables' => [

        'tl_reminder'
    ]
];

$GLOBALS['TL_HOOKS']['catalogManagerEntityOnCreate'][] = [ 'CatalogManager\Mailer\FrontendEditingTrigger', 'onCreate' ];
$GLOBALS['TL_HOOKS']['catalogManagerEntityOnUpdate'][] = [ 'CatalogManager\Mailer\FrontendEditingTrigger', 'onUpdate' ];
$GLOBALS['TL_HOOKS']['catalogManagerEntityOnDelete'][] = [ 'CatalogManager\Mailer\FrontendEditingTrigger', 'onDelete' ];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['CATALOG_MAILER'] = [

    'DEFAULT_MAILER' => [

        'recipients' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'attachment_tokens' => [ 'raw_*', 'clean_*', 'field_*', 'table_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_replyTo' => [ 'admin_email','recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_sender_name' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_recipient_cc' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'file_name' => [ 'admin_email', 'raw_*', 'clean_*', 'field_*', 'table_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_recipient_bcc' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_sender_address' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'file_content' => [ 'admin_email', 'raw_*', 'clean_*', 'field_*', 'table_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type', 'reminder_attachment' ],
        'email_subject' => [ 'admin_email', 'domain', 'recipient', 'raw_*', 'clean_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type' ],
        'email_text' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'field_*', 'table_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type', 'reminder_attachment' ],
        'email_html' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'field_*', 'table_*', 'post_raw_*', 'post_clean_*', 'post_old_raw_*', 'post_old_clean_*', 'post_type', 'reminder_attachment' ]
    ]
];

// CRONJOB DEBUG REMINDER
// $objCronJob = new CatalogManager\Mailer\Cronjob();
// $objCronJob->reminder();