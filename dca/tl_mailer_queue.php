<?php

$GLOBALS['TL_DCA']['tl_mailer_queue'] = [

    'config' => [

        'dataContainer' => 'Table',

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
        ]
    ]
];