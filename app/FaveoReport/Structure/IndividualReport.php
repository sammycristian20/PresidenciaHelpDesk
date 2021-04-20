<?php


namespace App\FaveoReport\Structure;


use Lang;

/**
 * Reports which do not need to follow the conventional report structure and will not be saved in the DB (for eg. dashboard reports and daily reports)
 */
class IndividualReport
{
    /**
     * Title of the report
     * @var string
     */
    public $title;

    /**
     * Description of the report
     * @var string
     */
    public $description;

    /**
     * Link where report will be explained in details.
     * @var string
     */
    public $helpLink;

    /**
     * Count of the elements inside report (is useful in case of pagination)
     * @var int
     */
    public $total;

    /**
     * Type of the report
     * @var string
     */
    public $type;

    /**
     * Array on records in the report
     * @var array
     */
    public $data = [];

    /**
     * Sets title for the object
     * @param string $langKey
     */
    public function setTitle(string $langKey)
    {
        $this->title = Lang::get("report::lang.$langKey");
    }

    /**
     * Sets title for the object
     * @param string $langKey
     */
    public function setDescription(string $langKey)
    {
        $this->description = Lang::get("report::lang.$langKey");
    }

    /**
     * Injects $element into data property
     * @param IndividualReportElement $element
     */
    public function injectData(IndividualReportElement $element)
    {
        $this->data[] = $element;
    }

    /**
     * Injects $element into data property
     * @param Widget $element
     */
    public function injectWidget(Widget $element)
    {
        $this->data[] = $element;
    }
}