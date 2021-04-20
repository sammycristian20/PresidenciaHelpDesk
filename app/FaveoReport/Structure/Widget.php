<?php


namespace App\FaveoReport\Structure;


class Widget
{
    /**
     * Id of the widget
     * @var string|int
     */
    public $id;

    /**
     * Text key that is going to be displayed
     * @var string
     */
    public $key;

    /**
     * Value of the key
     * @var string|int
     */
    public $value;

    /**
     * Link where widget should redirect after clicking
     * @var string
     */
    public $redirectTo = null;

    /**
     * Description of the value
     * @var null
     */
    public $description = null;

    /**
     * Icon of the widget
     * @var string
     */
    public $icon_class = null;

    /**
     * Background color of the icon of the widget
     * @var string
     */
    public $icon_color = null;
}