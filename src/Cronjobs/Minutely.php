<?php

namespace Alnv\CatalogManagerMailerBundle\Cronjobs;

use Alnv\CatalogManagerMailerBundle\Cronjob;
use Contao\CoreBundle\Framework\ContaoFramework;

class Minutely
{
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function __invoke()
    {

        $this->framework->initialize();

        $cronjob = new Cronjob();
        $cronjob->reminder();
        $cronjob->mailer();
    }
}