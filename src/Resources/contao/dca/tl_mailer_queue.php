<?php

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_mailer_queue'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'mailer_id' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'post' => [
            'sql' => "blob NULL"
        ],
        'reminder_id' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ]
    ]
];