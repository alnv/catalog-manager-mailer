<?php

use Alnv\CatalogManagerMailerBundle\Classes\tl_module;

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] = str_replace('catalogUseFrontendEditingViewPage;', 'catalogUseFrontendEditingViewPage;{catalog_mailer_settings},catalogMailerInsert,catalogMailerDuplicate,catalogMailerUpdate,catalogMailerDelete;', $GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView']);

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerInsert'] = [
    'inputType' => 'select',
    'eval' => [
        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],
    'options_callback' => [tl_module::class, 'getMailer'],
    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerDuplicate'] = [
    'inputType' => 'select',
    'eval' => [
        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],
    'options_callback' => [tl_module::class, 'getMailer'],
    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerUpdate'] = [
    'inputType' => 'select',
    'eval' => [
        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],
    'options_callback' => [tl_module::class, 'getMailer'],
    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerDelete'] = [
    'inputType' => 'select',
    'eval' => [
        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],
    'options_callback' => [tl_module::class, 'getMailer'],
    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];