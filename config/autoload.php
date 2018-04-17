<?php

ClassLoader::addNamespace( 'CatalogManager\Mailer' );

ClassLoader::addClasses([

    'CatalogManager\Mailer\Mailer' => 'system/modules/catalog-manager-mailer/Mailer.php',
    'CatalogManager\Mailer\Cronjob' => 'system/modules/catalog-manager-mailer/Cronjob.php',
    'CatalogManager\Mailer\tl_mailer' => 'system/modules/catalog-manager-mailer/classes/tl_mailer.php',
    'CatalogManager\Mailer\tl_module' => 'system/modules/catalog-manager-mailer/classes/tl_module.php',
    'CatalogManager\Mailer\tl_reminder' => 'system/modules/catalog-manager-mailer/classes/tl_reminder.php',
    'CatalogManager\Mailer\AttachmentBuilder' => 'system/modules/catalog-manager-mailer/AttachmentBuilder.php',
    'CatalogManager\Mailer\FrontendEditingTrigger' => 'system/modules/catalog-manager-mailer/FrontendEditingTrigger.php'
]);

TemplateLoader::addFiles([

    'reminder_attachment' => 'system/modules/catalog-manager-mailer/templates',
]);