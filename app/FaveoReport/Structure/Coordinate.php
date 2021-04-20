<?php


namespace App\FaveoReport\Structure;

/**
 * Reports chart data structure
 */
class Coordinate
{
    /**
     * Id of the coordinate
     * @var int?
     */
    public $id;

    /**
     * Label of the coordinate
     * @var string
     */
    public $label;

    /**
     * Url where it should be redirected once clicked on the coordinate
     * @var string
     */
    public $redirectTo;

    /**
     * Value of the coordinate
     * @var string
     */
    public $value;
}