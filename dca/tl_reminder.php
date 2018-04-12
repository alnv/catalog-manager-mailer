<?php

$GLOBALS['TL_DCA']['tl_reminder'] = [

    'config' => [

        'dataContainer' => 'Table',

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
            'fields' => [ 'name', 'first_execution', 'last_execution', 'execution_time', 'interval' ]
        ],

        'operations' => [

            'edit' => [

                'label' => &$GLOBALS['TL_LANG']['tl_reminder']['edit'],
                'href' => 'act=edit',
                'icon' => 'header.gif'
            ],

            'copy' => [

                'label' => &$GLOBALS['TL_LANG']['tl_reminder']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif'
            ],

            'delete' => [

                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'label' => &$GLOBALS['TL_LANG']['tl_reminder']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
            ],

            'show' => [

                'label' => &$GLOBALS['TL_LANG']['tl_reminder']['show'],
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

        '__selector__' => [ 'use_attachment' ],
        'default' => '{general_settings},name,mailer_id;{reminder_settings},first_execution,last_execution,execution_time,interval;{attachment_settings},use_attachment;'
    ],

    'subpalettes' => [

        'use_attachment' => 'dbTable,attachment_template,tableColumns,dbTaxonomy'
    ],

    'fields' => [

        'id' => [

            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],

        'tstamp' => [

            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'name' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['name'],
            'inputType' => 'text',

            'eval' => [

                'tl_class' => 'w50',
                'mandatory' => true
            ],

            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],

        'mailer_id' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['mailer_id'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_reminder', 'getMailerIds' ],

            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'first_execution' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['first_execution'],
            'inputType' => 'text',

            'default' => time(),

            'eval' => [

                'rgxp' => 'date',
                'mandatory' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard'
            ],

            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],


        'last_execution' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['last_execution'],
            'inputType' => 'text',

            'eval' => [

                'readonly' => true,
                'tl_class' => 'w50'
            ],

            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'execution_time' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['execution_time'],
            'inputType' => 'text',

            'default' => time(),

            'eval' => [

                'rgxp' => 'datim',
                'datepicker' => true,
                'tl_class' => 'w50 wizard'
            ],

            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'interval' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['interval'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50'
            ],

            'options' => [ 'once', 'daily', 'weekly', 'semimonthly', 'monthly', 'quarter', 'yearly' ],

            'reference' => &$GLOBALS['TL_LANG']['tl_reminder']['intervalMessages'],

            'exclude' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],

        'use_attachment' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['use_attachment'],
            'inputType' => 'checkbox',

            'eval' => [

                'tl_class' => 'clr',
                'submitOnChange' => true
            ],

            'exclude' => true,
            'sql' => "char(1) NOT NULL default ''"
        ],

        'dbTable' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['dbTable'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_reminder', 'getTables' ],

            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],

        'attachment_template' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['attachment_template'],
            'inputType' => 'select',

            'eval' => [

                'chosen' => true,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_reminder', 'getTemplates' ],

            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],

        'tableColumns' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['tableColumns'],
            'inputType' => 'checkboxWizard',

            'eval' => [

                'multiple' => true,
                'tl_class' => 'clr'
            ],

            'options_callback' => [ 'CatalogManager\Mailer\tl_reminder', 'getColumns' ],

            'exclude' => true,
            'sql' => "blob NULL"
        ],

        'dbTaxonomy' => [

            'label' => &$GLOBALS['TL_LANG']['tl_reminder']['dbTaxonomy'],
            'inputType' => 'catalogTaxonomyWizard',

            'eval' => [

                'tl_class' => 'clr',
                'dcTable' => 'tl_reminder',
                'taxonomyTable' => [ 'CatalogManager\Mailer\tl_reminder', 'getTaxonomyTable' ],
                'taxonomyEntities' => [ 'CatalogManager\Mailer\tl_reminder', 'getTaxonomyFields' ]
            ],

            'exclude' => true,
            'sql' => "blob NULL"
        ]
    ]
];