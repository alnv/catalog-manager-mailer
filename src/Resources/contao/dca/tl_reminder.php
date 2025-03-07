<?php

use Contao\DC_Table;
use Alnv\CatalogManagerBundle\OrderByHelper;
use Alnv\CatalogManagerMailerBundle\Classes\tl_reminder;

$GLOBALS['TL_DCA']['tl_reminder'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
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
            'fields' => ['name', 'first_execution', 'last_execution', 'interval']
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit&table=tl_reminder',
                'icon' => 'header.svg'
            ],
            'copy' => [
                'href' => 'act=copy&table=tl_reminder',
                'icon' => 'copy.svg'
            ],
            'delete' => [
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
                'href' => 'act=delete&table=tl_reminder',
                'icon' => 'delete.svg',
            ],
            'show' => [
                'href' => 'act=show&table=tl_reminder',
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
        '__selector__' => ['use_attachment'],
        'default' => '{general_settings},name,mailer_id;{reminder_settings},first_execution,interval,last_execution;{attachment_settings},use_attachment;'
    ],
    'subpalettes' => [
        'use_attachment' => 'dbTable,attachment_template,tableColumns,dbOrderBy,dbTaxonomy'
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
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
        'mailer_id' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => [tl_reminder::class, 'getMailerIds'],
            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'first_execution' => [
            'inputType' => 'text',
            'default' => time(),
            'eval' => [
                'rgxp' => 'datim',
                'mandatory' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard'
            ],
            'flag' => 6,
            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'last_execution' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'datim',
                'readonly' => true,
                'tl_class' => 'w50'
            ],
            'flag' => 6,
            'exclude' => true,
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'interval' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50'
            ],
            'options' => ['once', 'daily', 'weekly', 'semimonthly', 'monthly', 'quarter', 'yearly'],
            'exclude' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],
        'use_attachment' => [
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'clr',
                'submitOnChange' => true
            ],
            'exclude' => true,
            'sql' => "char(1) NOT NULL default ''"
        ],
        'dbTable' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => [tl_reminder::class, 'getTables'],
            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],
        'attachment_template' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => [tl_reminder::class, 'getTemplates'],
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'tableColumns' => [
            'inputType' => 'checkboxWizard',
            'eval' => [
                'multiple' => true,
                'tl_class' => 'clr'
            ],
            'options_callback' => [tl_reminder::class, 'getColumns'],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'dbTaxonomy' => [
            'inputType' => 'catalogTaxonomyWizard',
            'eval' => [
                'tl_class' => 'clr',
                'dcTable' => 'tl_reminder',
                'taxonomyTable' => [tl_reminder::class, 'getTaxonomyTable'],
                'taxonomyEntities' => [tl_reminder::class, 'getTaxonomyFields']
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'dbOrderBy' => [
            'inputType' => 'catalogDuplexSelectWizard',
            'eval' => [
                'chosen' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true,
                'mainLabel' => 'catalogManagerFields',
                'dependedLabel' => 'catalogManagerOrder',
                'mainOptions' => [OrderByHelper::class, 'getSortableFields'],
                'dependedOptions' => [OrderByHelper::class, 'getOrderByItems']
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ]
    ]
];