<?php

namespace CatalogManager\Mailer;

use CatalogManager\Toolkit as Toolkit;
use CatalogManager\SQLQueryBuilder as SQLQueryBuilder;
use CatalogManager\CatalogController as CatalogController;
use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;

class AttachmentBuilder extends CatalogController {

    protected $arrCatalog = [];
    protected $arrCatalogFields = [];
    protected $strTemplate = 'reminder_attachment';

    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
        $this->import( 'SQLQueryBuilder' );
        $this->import( 'CatalogFieldBuilder' );
    }


    public function render( $objReminder ) {

        $arrHeader = [];
        $arrRows = [];

        if ( !$objReminder->use_attachment ) return '';
        if ( !$objReminder->dbTable ) return '';

        $this->CatalogFieldBuilder->initialize( $objReminder->dbTable );
        $this->arrCatalogFields = $this->CatalogFieldBuilder->getCatalogFields( true, $this );
        $this->arrCatalog = $this->CatalogFieldBuilder->getCatalog();

        $this->strTemplate = $objReminder->attachment_template ? $objReminder->attachment_template : $this->strTemplate;

        $arrTaxonomies = Toolkit::deserialize( $objReminder->dbTaxonomy );
        $arrColumns = Toolkit::deserialize( $objReminder->tableColumns );
        $arrOrderBy = Toolkit::deserialize( $objReminder->dbOrderBy );
        $objTemplate = new \FrontendTemplate( $this->strTemplate );

        $arrQuery = [

            'table' => $objReminder->dbTable,
            'orderBy' => [],
            'where' => []
        ];

        if ( !empty( $arrTaxonomies['query'] ) && is_array( $arrTaxonomies['query'] ) ) {

            $arrQuery['where'] = Toolkit::parseQueries( $arrTaxonomies['query'] );
        }

        if ( is_array( $this->arrCatalog['operations'] ) && in_array( 'invisible', $this->arrCatalog['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => 0
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

        if ( is_array( $arrOrderBy ) ) {

            if ( !empty( $arrOrderBy ) ) {

                foreach ( $arrOrderBy as $arrOrderBy ) {

                    if ( $arrOrderBy['key'] && $arrOrderBy['value'] ) {

                        $arrQuery['orderBy'][] = [

                            'field' => $arrOrderBy['key'],
                            'order' => $arrOrderBy['value']
                        ];
                    }
                }
            }
        }

        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        while ( $objEntities->next() ) {

            $arrEntity = Toolkit::parseCatalogValues( $objEntities->row(), $this->arrCatalogFields, true );
            $arrEntity['origin'] = $objEntities->row();

            // @todo master url

            $arrRows[] = $arrEntity;
        }

        foreach ( $arrColumns as $strColumn ) {

            if ( Toolkit::isEmpty( $this->arrCatalogFields[ $strColumn ] ) ) continue;

            $arrHeader[ $strColumn ] = $this->arrCatalogFields[ $strColumn ];
        }

        $objTemplate->setData([

            'header' => $arrHeader,
            'rows' => $arrRows
        ]);

        return $objTemplate->parse();
    }
}