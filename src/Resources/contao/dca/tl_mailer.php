<?php

use Alnv\CatalogManagerMailerBundle\Classes\tl_mailer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_mailer'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'onload_callback' => [
            [tl_mailer::class, 'run']
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
            'fields' => ['name'],
            'panelLayout' => 'filter;sort,search,limit'
        ],
        'label' => [
            'showColumns' => true,
            'fields' => ['name', 'tablename', 'start_at', 'end_at', 'state']
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'header.svg'
            ],
            'send' => [
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['startMailerConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
                'href' => 'sendMail=1',
                'icon' => 'member_.svg'
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg'
            ],
            'delete' => [
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
                'href' => 'act=delete',
                'icon' => 'delete.svg',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg'
            ]
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ]
    ],
    'palettes' => [
        '__selector__' => ['useFilter'],
        'default' => '{general_settings},name,tablename;{notification_settings},notification,emailField,is_test;{query_settings},useFilter;'
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
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
                'mandatory' => true
            ],
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'tablename' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => [tl_mailer::class, 'getCatalogs'],
            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],
        'notification' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true,
                'ncNotificationChoices' => ['DEFAULT_MAILER']
            ],
            'options_callback' => [tl_mailer::class, 'getNotifications'],
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'emailField' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => [tl_mailer::class, 'getCatalogFields'],
            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],
        'is_test' => [
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'w50 m12'
            ],
            'exclude' => true,
            'sql' => "char(1) NOT NULL default ''"
        ],
        'useFilter' => [
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'clr',
                'submitOnChange' => true
            ],
            'exclude' => true,
            'sql' => "char(1) NOT NULL default ''"
        ],
        'dbTaxonomy' => [
            'inputType' => 'catalogTaxonomyWizard',
            'eval' => [
                'tl_class' => 'clr',
                'dcTable' => 'tl_mailer',
                'taxonomyTable' => [tl_mailer::class, 'getTaxonomyTable'],
                'taxonomyEntities' => [tl_mailer::class, 'getTaxonomyFields']
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'start_at' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'datim',
                'doNotCopy' => true,
                'disabled' => true,
                'tl_class' => 'w50'
            ],
            'flag' => 6,
            'exclude' => true,
            'sorting' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],
        'end_at' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'datim',
                'doNotCopy' => true,
                'disabled' => true,
                'tl_class' => 'w50'
            ],
            'flag' => 6,
            'exclude' => true,
            'sorting' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],
        'state' => [
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
        ],
        'post' => [
            'sql' => "blob NULL"
        ],
        'reminder_id' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ]
    ]
];