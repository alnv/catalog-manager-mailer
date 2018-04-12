<?php

namespace CatalogManager\Mailer;

use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;
use CatalogManager\Toolkit as Toolkit;

class tl_reminder extends \Backend {


    public function getTaxonomyTable( \DataContainer $dc ) {

        return $dc->activeRecord->dbTable ? $dc->activeRecord->dbTable : '';
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


    public function getMailerIds() {

        $arrReturn = [];
        $objMailer = $this->Database->prepare( 'SELECT * FROM tl_mailer' )->execute();

        if ( !$objMailer->numRows ) return $arrReturn;

        while ( $objMailer->next() ) {

            $arrReturn[ $objMailer->id ] = $objMailer->name;
        }

        return $arrReturn;
    }


    public function getTables() {

        return $this->Database->listTables( null );
    }


    public function getTemplates() {

        return $this->getTemplateGroup( 'reminder_attachment' );
    }


    public function getColumns( \DataContainer $dc ) {

        $arrReturn = [];

        if ( !$dc->activeRecord->dbTable ) return $arrReturn;

        $arrColumns = $this->getTaxonomyFields( $dc, $dc->activeRecord->dbTable, [] );

        if ( !empty( $arrColumns ) && is_array( $arrColumns ) ) {

            foreach ( $arrColumns as $strFieldname => $arrField ) {

                $arrReturn[ $strFieldname ] = Toolkit::getLabelValue( $arrField['label'], $strFieldname ) . '['. $strFieldname .']';
            }
        }

        return $arrReturn;
    }
}