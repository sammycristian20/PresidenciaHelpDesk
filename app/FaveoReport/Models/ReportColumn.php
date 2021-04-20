<?php

namespace App\FaveoReport\Models;

use App\BaseModel;
use App\Traits\Observable;
use DB;
use Exception;
use Lang;

class ReportColumn extends BaseModel
{
    use Observable;

    protected $fillable = [

        /**
         * key of the column which can be extracted from the API
         */
        'key',

        /**
         * label of the column
         */
        'label',

        /**
         * If column should be visible or not
         */
        'is_visible',

        /**
         * If column is sortable
         * NOTE: this can be changed as code progresses (currently not all columns are sortable
         * but as our API improves, we will keep on make more columns as sortable)
         */
        'is_sortable',

        /**
         * If column is a custom column
         */
        'is_custom',

        /**
         * If a field is a timestamp
         */
        'is_timestamp',

        /**
         * Format of the timestamp
         */
        'timestamp_format',

        /**
         * if a field's value has to be displayed as html
         */
        'is_html',

        /**
         * Equation of the column
         */
        'equation',

        /**
         * Order of the column in the list
         */
        'order',

        /**
         * id of the report with which it is linked
         */
        "sub_report_id",

        /**
         * NOTE: this column isn't in use anymore. Laravel model saves columns which are not fillable but present
         * in the table but while mass-assignment it only considers fillables. Since this column is used at multiple places
         * in old seeders, we are keeping it here too.
         * In coming version, we will have a single seeder for report for fresh installation and old seeders will
         * be removed
         * @author avinash kumar <avinash.kumar@ladybirdweb.com>
         * @since v3.2.0
         */
        "type",
    ];

    /**
     * Updates relation once model is updated
     * @var array
     */
    public $touches = ["subReport"];

    public function getLabelAttribute($value)
    {
        // if it is custom_field or custom columns, label should be the normal label
        // else label should be from language file
        if ($this->hasDefaultLabel()) {
            return $value;
        }
        return Lang::get('report::lang.' . $value);
    }

    /**
     * If column has a label already and not has to be fetched,
     * @return boolean
     */
    private function hasDefaultLabel(): bool
    {
        try {
            if ($this->attributes['is_custom']) {
                return true;
            }

            if (strpos($this->attributes['key'], 'custom_') !== false) {
                return true;
            }
            return false;

        } catch (Exception $e) {
            return true;
        }
    }

    public function getTimestampFormatAttribute($value)
    {
        // give date format only for timestamp columns which are custom
        if (!$value && $this->getAttribute('is_timestamp')) {
            // ["F j, Y g:i  a", "Y-m-d g:i a", "Y-m-d", "F j, Y", "g:i  a"];
            // format corresponding to January 8, 2020 4:40 AM
            return "F j, Y g:i  a";
        }
        return $value;
    }

    public function setTimestampFormatAttribute($value)
    {
        // set value only if column is custom column and its a timestamp
        if ($this->getAttribute('is_custom') && $this->getAttribute('is_timestamp')) {
            $this->attributes["timestamp_format"] = $value;
        }
    }

    public function subReport()
    {
        return $this->belongsTo(SubReport::class);
    }
}
