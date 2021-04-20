<?php


namespace App\Structure;

use Illuminate\Support\Collection;

class Activity
{
    /**
     * Type of the activity
     * @var string
     */
    public $type;


    /**
     * Data of the activity
     * @var Collection
     */
    public $data;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->data = new Collection();
        $this->type = $type;
    }
}
