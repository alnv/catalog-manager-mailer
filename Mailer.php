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

        $this->CatalogFieldBuilder->initialize( $arrParameters['tablename'] );

        $this->arrCatalogFields = $this->CatalogFieldBuilder->getCatalogFields( true, $this );
        $this->arrCatalog = $this->CatalogFieldBuilder->getCatalog();
        $this->arrParameters = $arrParameters;
    }


    public function send() {

        if ( !$this->arrParameters['notification'] ) return null;

        $objNotification = \NotificationCenter\Model\Notification::findByPk( $this->arrParameters['notification'] );

        if ( $objNotification === null ) {

            $this->log( 'The notification was not found ID ' . $this->arrParameters['notification'] , __METHOD__, TL_ERROR );

            return null;
        }

        $this->getEntities();

        $intPerRate = 10;
        $intTotal = count( $this->arrEntities );
        $arrRecipient = array_keys( $this->arrEntities );

        if ( !$intTotal ) return null;

        $intTransit = $this->arrParameters['offset'];
        $intTotalTransits =   max( ceil( $intTotal / $intPerRate ), 1 );

        if ( $intTransit < $intTotalTransits ) {

            $intOffset = $intTransit * $intPerRate;
            $intLimit = min( $intPerRate + $intOffset, $intTotal );

            \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" is running', __METHOD__, TL_GENERAL );

            for ( $i = $intOffset; $i < $intLimit; $i++ ) {

                $strEmail = $arrRecipient[ $i ];
                $arrTokens = $this->arrEntities[ $strEmail ];

                $objNotification->send( $arrTokens, $GLOBALS['TL_LANGUAGE'] );
            }

            $intOffset += 1;

            $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

                'offset' => $intOffset

            ])->execute( $this->arrParameters['id'] );
        }

        else {

            $this->end();
            $this->checkMailerQueue();
        }
    }


    protected function getEntities() {

        $arrPostData = Toolkit::deserialize( $this->arrParameters['post'] );

        $strPostType = '';
        $arrPostTokens = [];

        if ( is_array( $arrPostData ) && isset( $arrPostData['tokens'] ) ) $arrPostTokens = $arrPostData['tokens'];
        if ( is_array( $arrPostData ) && isset( $arrPostData['type'] ) ) $strPostType = $arrPostData['type'];

        $arrQuery = [

            'table' => $this->arrParameters['tablename'],
            'where' => []
        ];

        if ( $this->arrParameters['useFilter'] && is_array( $this->arrParameters['dbTaxonomy'] ) && isset( $this->arrParameters['dbTaxonomy']['query'] ) ) {

            $arrQuery['where'] = Toolkit::parseQueries( $this->arrParameters['dbTaxonomy']['query'], function ( $arrQuery ) use ( $arrPostData ) {

                $arrQuery['value'] = Toolkit::parsePseudoInserttag( $arrQuery['value'], $arrPostData['row'] );

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

            if ( !is_array( $arrEntity ) ) continue;
            if ( !isset( $arrEntity[ $strIdentifier ] ) || Toolkit::isEmpty( $arrEntity[ $strIdentifier ] ) ) continue;

            $arrRecord = Toolkit::parseCatalogValues( $arrEntity, $this->arrCatalogFields, true );

            $arrTokens = [];
            $arrTokens['recipient'] = $arrEntity[ $strIdentifier ];
            $arrTokens['admin_email'] = \Config::get( 'adminEmail' );

            foreach ( $arrRecord as $strFieldname => $strValue ) {

                $arrTokens[ 'clean_' . $strFieldname ] = $strValue;
            }

            foreach ( $arrRecord as $strFieldname => $strValue ) {

                $arrTokens[ 'raw_' . $strFieldname ] = $arrEntity[ $strFieldname ];
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

            $this->arrEntities[ $arrTokens['recipient'] ] = $arrTokens;
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

        \System::log( 'Catalog Mailer "' . $this->arrParameters['name'] . '" is ready', __METHOD__, TL_GENERAL );
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