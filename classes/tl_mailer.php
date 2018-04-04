<?php

namespace CatalogManager\Mailer;

use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;
use CatalogManager\Toolkit as Toolkit;

class tl_mailer extends \Backend {


    public function getCatalogs() {

        $arrReturn = [];
        $objModules = $this->Database->prepare('SELECT * FROM tl_catalog WHERE tstamp > 0')->execute( \Input::get('id') );

        if ( !$objModules->numRows ) return $arrReturn;

        while ( $objModules->next() ) {

            if ( !$objModules->tablename ) continue;

            $arrReturn[ $objModules->tablename ] = $objModules->name ? $objModules->name : $objModules->tablename;
        }

        return $arrReturn;
    }


    public function getCatalogFields( \DataContainer $dc ) {

        $arrReturn = [];
        $arrForbiddenTypes = [ 'upload' ];

        if ( !$dc->activeRecord->tablename ) return $arrReturn;

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize( $dc->activeRecord->tablename );
        $arrFields = $objCatalogFieldBuilder->getCatalogFields( true, null );

        foreach ( $arrFields as $strFieldname => $arrField ) {

            if ( in_array( $arrField['type'], Toolkit::excludeFromDc() ) ) continue;
            if ( in_array( $arrField['type'], $arrForbiddenTypes ) ) continue;

            $arrReturn[ $strFieldname ] = $arrField['_dcFormat']['label'][0] ? $arrField['_dcFormat']['label'][0] : $strFieldname;
        }

        return $arrReturn;
    }


    public function getTaxonomyTable( \DataContainer $dc ) {

        return $dc->activeRecord->tablename ? $dc->activeRecord->tablename : '';
    }


    public function getTaxonomyFields( \DataContainer $dc, $strTablename ) {

        $arrReturn = [];
        $arrForbiddenTypes = [ 'upload', 'textarea' ];

        if ( !$strTablename ) return $arrReturn;

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize( $strTablename );
        $arrFields = $objCatalogFieldBuilder->getCatalogFields( true, null );

        foreach ( $arrFields as $strFieldname => $arrField ) {

            if ( !$this->Database->fieldExists( $strFieldname, $strTablename ) ) continue;
            if ( in_array( $arrField['type'], Toolkit::columnOnlyFields() ) ) continue;
            if ( in_array( $arrField['type'], Toolkit::excludeFromDc() ) ) continue;
            if ( in_array( $arrField['type'], $arrForbiddenTypes ) ) continue;

            $arrReturn[ $strFieldname ] = $arrField['_dcFormat'];
        }

        return $arrReturn;
    }


    public function run() {

        if ( \Input::get('sendMail') ) {

            $objMailer = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE id = ?' )->limit(1)->execute( \Input::get('id') );

            if ( !$objMailer->in_progress ) {

                $this->Database->prepare( 'UPDATE tl_mailer %s WHERE id = ?' )->set([

                    'start_at' => time(),
                    'in_progress' => '1',
                    'state' => 'active',
                    'offset' => 0

                ])->execute( \Input::get('id') );
            }

            /*
            $objMail = $this->Database->prepare( 'SELECT * FROM tl_mailer WHERE id = ?' )->limit(1)->execute( \Input::get('id') );
            $arrParameters = $objMail->row();
            $arrParameters['dbTaxonomy'] = deserialize( $arrParameters['dbTaxonomy'] );
            $objMailer = new Mailer( $arrParameters );
            $objMailer->send();
            */

            $this->redirect( preg_replace( '/&(amp;)?sendMail=[^&]*/i', '', preg_replace( '/&(amp;)?' . preg_quote( "1", '/' ) . '=[^&]*/i', '', \Environment::get('request') ) ) );
        }
    }


    public function getNotifications( \DataContainer $dc ) {

        $strWhere = '';
        $arrValues = [];
        $arrChoices = [];

        if ( !$this->Database->tableExists( 'tl_nc_notification' ) ) return [];

        $arrTypes = $GLOBALS['TL_DCA']['tl_mailer']['fields'][ $dc->field ]['eval']['ncNotificationChoices'];

        if ( !empty( $arrTypes ) && is_array( $arrTypes ) ) {

            $strWhere = ' WHERE ' . implode( ' OR ', array_fill(0, count( $arrTypes ), 'type=?' ) );
            $arrValues = $arrTypes;
        }

        $objNotifications = $this->Database->prepare( 'SELECT id, title FROM tl_nc_notification' . $strWhere . ' ORDER BY title' )->execute( $arrValues );

        while ( $objNotifications->next() ) {

            $arrChoices[ $objNotifications->id ] = $objNotifications->title;
        }

        return $arrChoices;
    }
}