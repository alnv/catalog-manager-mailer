<?php

namespace Alnv\CatalogManagerMailerBundle\Classes;

use Contao\Backend;

class tl_module extends Backend
{

    public function getMailer(): array
    {

        $arrReturn = [];
        $objMailer = $this->Database->prepare('SELECT * FROM tl_mailer')->execute();

        if (!$objMailer->numRows) {
            return $arrReturn;
        }

        while ($objMailer->next()) {
            $arrReturn[$objMailer->id] = $objMailer->name;
        }

        return $arrReturn;
    }
}