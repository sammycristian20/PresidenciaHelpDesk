<?php


namespace App\FaveoReport\Structure;


use Lang;

/**
 * Child of IndividualReport class
 */
class IndividualReportElement
{
    /**
     * Title that has to be displayed
     * @var string
     */
    public $title;

    /**
     * Count
     * @var int|float|null
     */
    public $total;

    /**
     * url to a picture of the element
     * @var string
     */
    public $picture;

    /**
     * Array of attributes present in it
     * @var array
     */
    public $attributes;

    /**
     * Any additional data if needed
     * @var array
     */
    public $metaData;

    /**
     * Sets title by appending language logic to it
     * If in case, language not needed, the property can directly be set without using this method
     * @param $title
     */
    public function setTitle(string $title)
    {
        $this->title = Lang::get("report::lang.$title");
    }

    /**
     * Sets attributes to have objects in key value pair
     * @param string|null $key
     * @param string|int $value
     */
    public function setAttribute($key, $value)
    {
        $attribute = (object)[];
        $attribute->key = $key ? Lang::get("report::lang.$key") : null;
        $attribute->value = $value;
        $this->attributes =  $this->attributes ? : [];
        $this->attributes[] = $attribute;
    }
}