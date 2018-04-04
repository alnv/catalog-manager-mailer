<?php

namespace CatalogManager\Mailer;

use CatalogManager\CatalogController as CatalogController;

class Cronjob extends CatalogController {


    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
    }


    public function initialize() {

        if ( !$this->Database->tableExists( 'tl_mailer' ) ) return null;

        $objMailers = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE in_progress = "1"' )->limit(1)->execute();

        if ( !$objMailers->numRows ) return null;

        while ( $objMailers->next() ) {

            $arrParameters = $objMailers->row();

            if ( !$this->Database->tableExists( $objMailers->tablename ) ) {

                $this->setState( 'failed', $arrParameters['id'] );

                continue;
            }

            $arrParameters['dbTaxonomy'] = deserialize( $arrParameters['dbTaxonomy'] );

            $objMailer = new Mailer( $arrParameters );
            $objMailer->send();
        }
    }


    protected function setState( $strState, $strId ) {

        $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

            'state' => $strState,
            'in_progress' => '',
            'start_at' => '',
            'end_at' => '',
            'offset' => 0

        ])->execute( $strId );
    }
}