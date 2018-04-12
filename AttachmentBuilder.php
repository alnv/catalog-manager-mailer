<?php

namespace CatalogManager\Mailer;

use CatalogManager\Toolkit as Toolkit;
use CatalogManager\CatalogController as CatalogController;
use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;

class AttachmentBuilder extends CatalogController {

    protected $arrColumns = [];
    protected $arrCatalogFields = [];
    protected $strTemplate = 'reminder_attachment';

    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
        $this->import( 'CatalogFieldBuilder' );
    }


    public function render( $objReminder ) {

        $arrRows = [];

        $this->CatalogFieldBuilder->initialize( $objReminder->dbTable );
        $this->arrCatalogFields = $this->CatalogFieldBuilder->getCatalogFields( true, $this );
        $this->strTemplate = $objReminder->attachment_template ? $objReminder->attachment_template : $this->strTemplate;
        $this->arrColumns = Toolkit::deserialize( $objReminder->tableColumns );

        $objTemplate = new \FrontendTemplate( $this->strTemplate );

        // @todo

        $objTemplate->setData([

            'fields' => [],
            'rows' => $arrRows
        ]);

        return $objTemplate->parse();
    }
}