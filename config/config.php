<?php

$GLOBALS['TL_CRON']['minutely'][] = [ 'CatalogManager\Mailer\Cronjob', 'initialize' ];

$GLOBALS['BE_MOD']['catalog-manager-extensions']['catalog-manager-mailer'] = [

    'name' => 'catalog-manager-mailer',
    'icon' => 'system/modules/catalog-manager-mailer/assets/icon.svg',

    'tables' => [

        'tl_mailer'
    ]
];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['CATALOG_MAILER'] = [

    'MAIL_TEMPLATE' => [

        'recipients' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*' ],
        'attachment_tokens' => [ 'raw_*', 'clean_*', 'field_*', 'table_*' ],
        'email_replyTo' => [ 'admin_email','recipient', 'raw_*', 'clean_*' ],
        'email_sender_name' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*' ],
        'email_recipient_cc' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*' ],
        'file_name' => [ 'admin_email', 'raw_*', 'clean_*', 'field_*', 'table_*' ],
        'email_recipient_bcc' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*' ],
        'email_sender_address' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*' ],
        'file_content' => [ 'admin_email', 'raw_*', 'clean_*', 'field_*', 'table_*' ],
        'email_subject' => [ 'admin_email', 'domain', 'recipient', 'raw_*', 'clean_*' ],
        'email_text' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'field_*', 'table_*' ],
        'email_html' => [ 'admin_email', 'recipient', 'raw_*', 'clean_*', 'field_*', 'table_*' ]
    ]
];