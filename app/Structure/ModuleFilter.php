<?php


namespace App\Structure;

/**
 * Structure for filter field
 * Class ModuleFilter
 * @package App\Structure
 */
class ModuleFilter
{
    /**
     * The string which will be sent to backend once filter is submitted
     * @var string
     */
    public $key;

    /**
     * Api endpoint from where the dependency list will be fetched
     * @var string
     */
    public $apiEndpoint;

    /**
     * Label of the filter
     * @var string
     */
    public $label;

    /**
     * Name of the class which can be used to define grid layout
     * @var string
     */
    public $className = '';

    /**
     * If the field filter is a multi-select
     * @var string
     */
    public $multiple = true;

    /**
     * The elements that needs to be displayed
     * @var array
     */
    public $elements = [];

    /**
     * Sets all the class properties
     * @param string $key
     * @param string $apiEndpoint
     * @param string $label
     * @param string $className
     * @param array $elements
     */
    public function __construct(string $key, string $apiEndpoint, string $label, string $className = '', array $elements = [])
    {
        $this->key = $key;
        $this->apiEndpoint = $apiEndpoint;
        $this->label = $label;
        $this->className = $className;
        $this->elements = $elements;
    }
}
