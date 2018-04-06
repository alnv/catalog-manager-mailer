<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] = str_replace( 'catalogUseFrontendEditingViewPage;', 'catalogUseFrontendEditingViewPage;{catalog_mailer_settings},catalogMailerInsert,catalogMailerDuplicate,catalogMailerUpdate,catalogMailerDelete;', $GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] );

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerInsert'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['catalogMailerInsert'],
    'inputType' => 'select',

    'eval' => [

        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],

    'options_callback' => [ 'CatalogManager\Mailer\tl_module', 'getMailer' ],

    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerDuplicate'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['catalogMailerDuplicate'],
    'inputType' => 'select',

    'eval' => [

        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],

    'options_callback' => [ 'CatalogManager\Mailer\tl_module', 'getMailer' ],

    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerUpdate'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['catalogMailerUpdate'],
    'inputType' => 'select',

    'eval' => [

        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],

    'options_callback' => [ 'CatalogManager\Mailer\tl_module', 'getMailer' ],

    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['catalogMailerDelete'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['catalogMailerDelete'],
    'inputType' => 'select',

    'eval' => [

        'chosen' => true,
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ],

    'options_callback' => [ 'CatalogManager\Mailer\tl_module', 'getMailer' ],

    'exclude' => true,
    'sql' => "int(10) unsigned NOT NULL default '0'"
];