<?php

use Alnv\CatalogManagerMailerBundle\Cronjob;
use Alnv\CatalogManagerMailerBundle\FrontendEditingTrigger;

$GLOBALS['TL_CRON']['minutely'][] = [Cronjob::class, 'mailer'];
$GLOBALS['TL_CRON']['minutely'][] = [Cronjob::class, 'reminder'];

$GLOBALS['BE_MOD']['catalog-manager-extensions']['cm-mailer'] = [
    'name' => 'catalog-manager-mailer',
    'tables' => [
        'tl_mailer',
        'tl_mailer_queue'
    ]
];

$GLOBALS['BE_MOD']['catalog-manager-extensions']['cm-reminder'] = [
    'name' => 'catalog-manager-reminder',
    'tables' => [
        'tl_reminder'
    ]
];

$GLOBALS['TL_HOOKS']['catalogManagerEntityOnCreate'][] = [FrontendEditingTrigger::class, 'onCreate'];
$GLOBALS['TL_HOOKS']['catalogManagerEntityOnUpdate'][] = [FrontendEditingTrigger::class, 'onUpdate'];
$GLOBALS['TL_HOOKS']['catalogManagerEntityOnDelete'][] = [FrontendEditingTrigger::class, 'onDelete'];