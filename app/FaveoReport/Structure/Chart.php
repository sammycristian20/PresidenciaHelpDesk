<?php

namespace App\FaveoReport\Structure;

use Illuminate\Database\Eloquent\Collection;

/**
 * Reports chart data structure
 */

class Chart
{
    /**
     * Id of the chart (will be used at frontend to identify)
     * @var int|string|null
     */
    public $id;

    /**
     * Name of the chart
     * @var string
     */
    public $name;

    /**
     * list of coordinates
     * @var array
     */
    public $data;

    /**
     * label which will be displayed as a measuring parameter
     * y-axis label in a normal bar chart
     * @var string
     */
    public $dataLabel;

    /**
     * label which will be displayed as a measuring parameter
     * x-axis label in a normal bar chart
     * @var string
     */
    public $categoryLabel;

    public function __construct()
    {
        $this->data = new Collection;
    }

    /**
     * Injects a coordinate into data of the graph
     * @param Coordinate $graphCoordinate
     */
    public function injectData(Coordinate $graphCoordinate)
    {
        $this->data->push($graphCoordinate);
    }

    /**
     * Inject collection of chart in data property
     * @param array $charts
     */
    public function injectChart(Chart $charts)
    {
        $this->data->push($charts);
    }
}