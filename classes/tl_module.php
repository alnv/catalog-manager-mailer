<?php

namespace CatalogManager\Mailer;

class tl_module extends \Backend {


    public function getMailer() {

        $arrReturn = [];
        $objMailer = $this->Database->prepare('SELECT * FROM tl_mailer')->execute();

        if ( $objMailer->next() ) {

            $arrReturn[ $objMailer->id ] = $objMailer->name;
        }

        return $arrReturn;
    }
}