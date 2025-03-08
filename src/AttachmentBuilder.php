<?php

namespace Alnv\CatalogManagerMailerBundle;

use Alnv\CatalogManagerBundle\CatalogController;
use Alnv\CatalogManagerBundle\Toolkit;
use Alnv\CatalogManagerBundle\SQLQueryBuilder;
use Alnv\CatalogManagerBundle\CatalogFieldBuilder;
use Contao\FrontendTemplate;
use Contao\Date;

class AttachmentBuilder extends CatalogController
{

    protected array $arrCatalog = [];
    protected array $arrCatalogFields = [];
    protected string $strTemplate = 'reminder_attachment';

    public function __construct()
    {
        parent::__construct();
    }

    public function render($objReminder, $arrActiveRecord = []): string
    {

        $arrHeader = [];
        $arrRows = [];

        if (!$objReminder->use_attachment) return '';
        if (!$objReminder->dbTable) return '';

        (new CatalogFieldBuilder())->initialize($objReminder->dbTable);
        $this->arrCatalogFields = (new CatalogFieldBuilder())->getCatalogFields(true, $this);
        $this->arrCatalog = (new CatalogFieldBuilder())->getCatalog();

        $this->strTemplate = $objReminder->attachment_template ? $objReminder->attachment_template : $this->strTemplate;

        $arrTaxonomies = Toolkit::deserialize($objReminder->dbTaxonomy);
        $arrColumns = Toolkit::deserialize($objReminder->tableColumns);
        $arrOrderBy = Toolkit::deserialize($objReminder->dbOrderBy);
        $objTemplate = new FrontendTemplate($this->strTemplate);

        $arrQuery = [
            'table' => $objReminder->dbTable,
            'orderBy' => [],
            'where' => []
        ];

        if (!empty($arrTaxonomies['query']) && \is_array($arrTaxonomies['query'])) {
            $arrQuery['where'] = Toolkit::parseQueries($arrTaxonomies['query'], function ($arrQuery) use ($arrActiveRecord) {
                $arrQuery['value'] = Toolkit::parsePseudoInserttag($arrQuery['value'], $arrActiveRecord);
                return $arrQuery;
            });
        }

        if (\is_array($this->arrCatalog['operations']) && in_array('invisible', $this->arrCatalog['operations'])) {

            $dteTime = Date::floorToMinute();

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

        if (!empty($arrOrderBy)) {
            foreach ($arrOrderBy as $arrOrderByParameter) {
                if ($arrOrderByParameter['key'] && $arrOrderByParameter['value']) {
                    $arrQuery['orderBy'][] = [
                        'field' => $arrOrderByParameter['key'],
                        'order' => $arrOrderByParameter['value']
                    ];
                }
            }
        }

        $objEntities = (new SQLQueryBuilder())->execute($arrQuery);

        while ($objEntities->next()) {

            $arrEntity = Toolkit::parseCatalogValues($objEntities->row(), $this->arrCatalogFields, true);
            $arrEntity['origin'] = $objEntities->row();

            // @todo master url

            $arrRows[] = $arrEntity;
        }

        foreach ($arrColumns as $strColumn) {

            if (Toolkit::isEmpty($this->arrCatalogFields[$strColumn])) continue;

            $arrHeader[$strColumn] = $this->arrCatalogFields[$strColumn];
        }

        $objTemplate->setData([
            'header' => $arrHeader,
            'rows' => $arrRows
        ]);

        return $objTemplate->parse();
    }
}