<?php

namespace CatalogManager\Mailer;

use CatalogManager\Toolkit as Toolkit;
use CatalogManager\SQLQueryBuilder as SQLQueryBuilder;
use CatalogManager\CatalogController as CatalogController;
use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;

class Mailer extends CatalogController {


    protected $arrCatalog = [];
    protected $arrEntities = [];
    protected $arrParameters = [];
    protected $arrCatalogFields = [];


    public function __construct( $arrParameters ) {

        parent::__construct();

        $this->import( 'Database' );
        $this->import( 'SQLQueryBuilder' );
        $this->import( 'CatalogFieldBuilder' );

        $arrParameters['dbTaxonomy'] = deserialize( $arrParameters['dbTaxonomy'], true );
        $arrParameters['post'] = deserialize( $arrParameters['post'], true );

        $this->CatalogFieldBuilder->initialize( $arrParameters['tablename'] );

        $this->arrCatalogFields = $this->CatalogFieldBuilder->getCatalogFields( true, $this );
        $this->arrCatalog = $this->CatalogFieldBuilder->getCatalog();
        $this->arrParameters = $arrParameters;
    }


    public function send() {

        \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" is running', __METHOD__, TL_GENERAL );

        if ( !$this->arrParameters['notification'] ) {

            $this->log( 'No notification is defined' , __METHOD__, TL_ERROR );

            return null;
        }

        $objNotification = \NotificationCenter\Model\Notification::findByPk( $this->arrParameters['notification'] );

        if ( $objNotification === null ) {

            $this->log( 'The notification was not found ID ' . $this->arrParameters['notification'] , __METHOD__, TL_ERROR );

            return null;
        }

        $this->getEntities();

        $intPerRate = 10;
        $intTotal = count( $this->arrEntities );
        $arrRecipients = array_keys( $this->arrEntities );

        if ( !$intTotal ) {

            \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" is empty', __METHOD__, TL_GENERAL );

            return null;
        }

        $intTransit = (int) $this->arrParameters['offset'];

        if ( $this->arrParameters['is_test'] ) {

            \System::log( 'In the Catalog Mailer "' . $this->arrParameters['name'] . '" are ' . $intTotal . ' records.', __METHOD__, TL_GENERAL );
            \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" will send ' . $intPerRate . ' emails per round ['.$intTransit.'/'.$intTotal.']', __METHOD__, TL_GENERAL );
        }

        $strPostType = '';
        $arrPostTokens = [];

        if ( is_array( $this->arrParameters['post'] ) && isset( $this->arrParameters['post']['tokens'] ) ) $arrPostTokens = $this->arrParameters['post']['tokens'];
        if ( is_array( $this->arrParameters['post'] ) && isset( $this->arrParameters['post']['type'] ) ) $strPostType = $this->arrParameters['post']['type'];
        
        if ( $intTransit < $intTotal ) {

            $intOffset = $intTransit ? ( $intTransit + $intPerRate ) : 0;
            $intLimit = min( [ ( $intPerRate + $intOffset ), $intTotal ] );

            for ( $i = $intOffset; $i < $intLimit; $i++ ) {

                if ( !isset( $arrRecipients[ $i ] ) ) {

                    continue;
                }

                $strEmail = $arrRecipients[ $i ];
                $arrEntity = $this->arrEntities[ $strEmail ];
                $arrParsedEntity = Toolkit::parseCatalogValues( $arrEntity, $this->arrCatalogFields, true );

                $arrTokens = [];
                $arrTokens['recipient'] = $strEmail;
                $arrTokens['admin_email'] = \Config::get( 'adminEmail' );

                foreach ( $arrParsedEntity as $strFieldname => $strValue ) {

                    $arrTokens[ 'clean_' . $strFieldname ] = $strValue;
                }

                foreach ( $arrEntity as $strFieldname => $strValue ) {

                    $arrTokens[ 'raw_' . $strFieldname ] = $strValue;
                }

                foreach ( $this->arrCatalog as $strOptionname => $strValue ) {

                    $arrTokens[ 'table_' . $strOptionname ] = is_array( $strOptionname ) ? serialize( $strOptionname ) : $strOptionname;
                }

                foreach ( $arrPostTokens as $strToken => $strValue ) {

                    $arrTokens[ $strToken ] = $strValue;
                }

                if ( $strPostType ) {

                    $arrTokens[ 'post_type' ] = $strPostType;
                }

                foreach ( $this->arrCatalogFields as $strFieldname => $arrField ) {

                    if ( !is_array( $arrField ) ) continue;
                    if ( in_array( $arrField['type'], Toolkit::excludeFromDc() ) ) continue;

                    if ( is_array( $arrField['_dcFormat'] ) && isset( $arrField['_dcFormat']['label'] ) && isset( $arrField['_dcFormat']['label'][0] ) ) {

                        $arrTokens[ 'field_' . $strFieldname .'_label' ] = $arrField['_dcFormat']['label'][0];
                        $arrTokens[ 'field_' . $strFieldname .'_description' ] = $arrField['_dcFormat']['label'][1];
                    }

                    foreach ( $arrField as $strOptionname => $strValue ) {

                        $arrTokens[ 'field_' . $strFieldname .'_'. $strOptionname ] = is_array( $strOptionname ) ? serialize( $strOptionname ) : $strOptionname;
                    }
                }

                $arrTokens['reminder_attachment'] = '';

                if ( $this->arrParameters['reminder_id'] ) {

                    $objReminder = $this->Database->prepare( 'SELECT * FROM tl_reminder WHERE id = ?' )->limit(1)->execute( $this->arrParameters['reminder_id'] );

                    if ( $objReminder->numRows ) {

                        $objAttachmentBuilder = new AttachmentBuilder();
                        $arrTokens['reminder_attachment'] = $objAttachmentBuilder->render( $objReminder, $arrEntity );
                    }
                }

                if ( !$this->arrParameters['is_test'] ) {

                    $objNotification->send( $arrTokens, $GLOBALS['TL_LANGUAGE'] );
                }

                else {

                    \System::log( 'An e-mail has been sent to ' . $strEmail . ' [TEST]', __METHOD__, TL_GENERAL );
                }
            }

            $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

                'offset' => ( $intOffset ? $intOffset : $intLimit )

            ])->execute( $this->arrParameters['id'] );
        }

        else {

            $this->end();
            $this->checkMailerQueue();
        }
    }


    protected function getEntities() {

        $arrQuery = [

            'table' => $this->arrParameters['tablename'],
            'where' => []
        ];

        if ( $this->arrParameters['useFilter'] && is_array( $this->arrParameters['dbTaxonomy'] ) && isset( $this->arrParameters['dbTaxonomy']['query'] ) ) {

            $arrQuery['where'] = Toolkit::parseQueries( $this->arrParameters['dbTaxonomy']['query'], function ( $arrQuery ) {

                $arrRow = [];

                if ( isset( $this->arrParameters['post']['row'] ) && is_array( $this->arrParameters['post']['row'] ) ) {

                    $arrRow = $this->arrParameters['post']['row'];
                }

                $arrQuery['value'] = Toolkit::parsePseudoInserttag( $arrQuery['value'], $arrRow );

                return $arrQuery;
            });
        }

        if ( is_array( $this->arrCatalog['operations'] ) && in_array( 'invisible', $this->arrCatalog['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => '0'
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntities->numRows ) return null;

        while ( $objEntities->next() ) {

            $arrEntity = $objEntities->row();
            $strIdentifier = $this->arrParameters['emailField'] ? $this->arrParameters['emailField'] : 'id';
            $this->arrEntities[ $arrEntity[ $strIdentifier ] ] = $arrEntity;
        }

        if ( $this->arrParameters['is_test'] ) {

            \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" query: ' . $objEntities->query, __METHOD__, TL_GENERAL );
        }
    }


    protected function end() {

        $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

            'post' => serialize( [] ),
            'in_progress' => '',
            'reminder_id' => 0,
            'state' => 'ready',
            'end_at' => time(),
            'offset' => 0

        ])->execute( $this->arrParameters['id'] );

        \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" was successfully completed', __METHOD__, TL_GENERAL );
    }


    protected function checkMailerQueue() {

        $objQueue = $this->Database->prepare( 'SELECT * FROM tl_mailer_queue ORDER BY tstamp' )->limit(1)->execute();

        if ( $objQueue->numRows ) {

            $objMailer = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE id = ?' )->limit(1)->execute( $objQueue->mailer_id );

            if ( !$objMailer->numRows ) return null;

            $this->Database->prepare('UPDATE tl_mailer %s WHERE id = ?')->set([

                'reminder_id' => $objQueue->reminder_id,
                'post' => $objQueue->post,
                'start_at' => time(),
                'in_progress' => '1',
                'state' => 'active',
                'offset' => 0

            ])->execute( $objMailer->id );

            $this->Database->prepare( 'DELETE FROM tl_mailer_queue WHERE id = ?' )->execute( $objQueue->id );
        }
    }
}