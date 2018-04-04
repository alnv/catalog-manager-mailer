<?php

$GLOBALS['TL_DCA']['tl_mailer'] = [

    'config' => [

        'dataContainer' => 'Table',

        'onload_callback' => [

            [ 'CatalogManager\Mailer\tl_mailer', 'run' ]
        ],

        'sql' => [

            'keys' => [

                'id' => 'primary'
            ]
        ]
    ],

    'list' => [

        'sorting' => [

            'mode' => 2,
            'flag' => 1,
            'fields' => [ 'name' ],
            'panelLayout' => 'filter;sort,search,limit'
        ],

        'label' => [

            'showColumns' => true,
            'fields' => [ 'name', 'tablename', 'start_at', 'end_at', 'state' ]
        ],

        'operations' => [

            'edit' => [

                'label' => &$GLOBALS['TL_LANG']['tl_mailer']['edit'],
                'href' => 'act=edit',
                'icon' => 'header.gif'
            ],

            'send' => [

                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'label' => &$GLOBALS['TL_LANG']['tl_mailer']['send'],
                'href' => 'sendMail=1',
                'icon' => 'member_.gif'
            ],

            'copy' => [

                'label' => &$GLOBALS['TL_LANG']['tl_mailer']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif'
            ],

            'delete' => [

                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'label' => &$GLOBALS['TL_LANG']['tl_mailer']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
            ],

            'show' => [

                'label' => &$GLOBALS['TL_LANG']['tl_mailer']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            ]
        ],

        'global_operations' => [

            'all' => [

                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ]
    ],

    'palettes' => [

        '__selector__' => [ 'useFilter' ],
        'default' => '{general_settings},name,tablename;{notification_settings},notification,emailField;{query_settings},useFilter;'
    ],

    'subpalettes' => [

        'useFilter' => 'dbTaxonomy'
    ],

    'fields' => [

        'id' => [

            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],

        'tstamp' => [

            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'offset' => [

            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'in_progress' => [

            'sql' => "char(1) NOT NULL default ''"
        ],

        'name' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['name'],
            'inputType' => 'text',

            'eval' => [

                'tl_class' => 'w50',
                'mandatory' => true
            ],

            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],

        'tablename' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['tablename'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption'=> true
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_mailer', 'getCatalogs' ],

            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],

        'notification' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['notification'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption'=> true,
                'ncNotificationChoices' => [ 'MAIL_TEMPLATE' ]
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_mailer', 'getNotifications' ],

            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],

        'emailField' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['emailField'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption'=> true
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_mailer', 'getCatalogFields' ],

            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],

        'useFilter' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['useFilter'],
            'inputType' => 'checkbox',

            'eval' => [

                'tl_class' => 'clr',
                'submitOnChange' => true
            ],

            'exclude' => true,
            'sql' => "char(1) NOT NULL default ''"
        ],

        'dbTaxonomy' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['dbTaxonomy'],
            'inputType' => 'catalogTaxonomyWizard',

            'eval' => [

                'tl_class' => 'clr',
                'dcTable' => 'tl_mailer',
                'taxonomyTable' => [ 'CatalogManager\Mailer\tl_mailer', 'getTaxonomyTable' ],
                'taxonomyEntities' => [ 'CatalogManager\Mailer\tl_mailer', 'getTaxonomyFields' ]
            ],

            'exclude' => true,
            'sql' => "blob NULL"
        ],

        'start_at' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['start_at'],
            'inputType' => 'text',

            'eval' => [

                'rgxp'=>'datim',
                'doNotCopy'=>true,
                'disabled' => true,
                'tl_class' => 'w50'
            ],

            'flag' => 6,
            'exclude' => true,
            'sorting' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],

        'end_at' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['end_at'],
            'inputType' => 'text',

            'eval' => [

                'rgxp'=>'datim',
                'doNotCopy'=>true,
                'disabled' => true,
                'tl_class' => 'w50'
            ],

            'flag' => 6,
            'exclude' => true,
            'sorting' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],

        'state' => [

            'label' => &$GLOBALS['TL_LANG']['tl_mailer']['state'],
            'inputType' => 'select',

            'eval' => [

                'disabled' => true,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],

            'reference' => &$GLOBALS['TL_LANG']['tl_mailer']['stateMessages'],
            'options' => [],

            'exclude' => true,
            'sql' => "varchar(12) NOT NULL default ''"
        ]
    ]
];