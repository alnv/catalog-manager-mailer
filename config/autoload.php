<?php

ClassLoader::addNamespace( 'CatalogManager\Mailer' );

ClassLoader::addClasses([

    'CatalogManager\Mailer\Mailer' => 'system/modules/catalog-manager-mailer/Mailer.php',
    'CatalogManager\Mailer\Cronjob' => 'system/modules/catalog-manager-mailer/Cronjob.php',
    'CatalogManager\Mailer\tl_mailer' => 'system/modules/catalog-manager-mailer/classes/tl_mailer.php'
]);