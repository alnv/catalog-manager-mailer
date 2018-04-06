<?php

namespace CatalogManager\Mailer;

use CatalogManager\Toolkit as Toolkit;
use CatalogManager\CatalogController as CatalogController;
use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;

class FrontendEditingTrigger extends CatalogController {


    protected $arrCatalogFields = [];


    protected $arrPostData = [

        'tokens' => [],
        'type' => null,
        'table' => null
    ];


    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
    }


    public function onCreate( $arrData, $objModule ) {

        $intMailerId = $objModule->catalogMailerInsert ? $objModule->catalogMailerInsert : $objModule->catalogMailerDuplicate;

        if ( $intMailerId ) {

            $strType = $objModule->catalogMailerInsert ? 'create' : 'duplicate';

            $this->setPostData( $strType, $arrData );
            $this->runMailer( $intMailerId );
        }
    }


    public function onUpdate( $arrData, $objModule ) {

        if ( $objModule->catalogMailerUpdate ) {

            $this->setPostData( 'update', $arrData );
            $this->runMailer( $objModule->catalogMailerUpdate );
        }
    }


    public function onDelete( $arrData, $objModule ) {

        if ( $objModule->catalogMailerDelete ) {

            if ( $objModule->catalogMailerDelete ) {

                $this->setPostData( 'delete', $arrData );
                $this->runMailer( $objModule->catalogMailerUpdate );
            }
        }
    }


    protected function runMailer( $intMailerId ) {

        $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

            'post' => serialize( $this->arrPostData ),
            'start_at' => time(),
            'in_progress' => '1',
            'state' => 'active',
            'offset' => 0

        ])->execute( $intMailerId );
    }


    protected function setPostData( $strType, $arrData ) {

        $this->import( 'CatalogFieldBuilder' );

        $this->arrPostData['table'] = $arrData['table'];
        $this->arrPostData['type'] = $strType;

        $this->CatalogFieldBuilder->initialize( $this->arrPostData['table'] );
        $this->arrCatalogFields = $this->CatalogFieldBuilder->getCatalogFields( true, $this );

        $arrClean = Toolkit::parseCatalogValues( $arrData['row'], $this->arrCatalogFields, true );

        Toolkit::setTokens( $arrData['row'], 'post_raw', $this->arrPostData['tokens'] );
        Toolkit::setTokens( $arrClean, 'post_clean', $this->arrPostData['tokens'] );

        if ( in_array( $strType, [ 'duplicate', 'update' ] ) ) {

            if ( $arrData['id'] ) {

                $objEntity = $this->Database->prepare( sprintf( 'SELECT * FROM %s WHERE id = ?', $arrData['table'] ) )->limit(1)->execute( $arrData['id'] );
                $arrOldRaw = $objEntity->row();

                $arrOldClean = Toolkit::parseCatalogValues( $arrOldRaw, $this->arrCatalogFields, true );

                Toolkit::setTokens( $arrOldRaw, 'post_old_raw_', $this->arrPostData['tokens'] );
                Toolkit::setTokens( $arrOldClean, 'post_old_clean_', $this->arrPostData['tokens'] );
            }
        }
    }
}