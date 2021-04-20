<?php


namespace App\FaveoReport\Models;


use App\BaseModel;
use App\Traits\Observable;
use DB;

class SubReport extends BaseModel
{
    use Observable;

    protected $fillable = [
        /**
         * Id of the parent report
         */
        "report_id",

        /**
         * the identifier with which uniqueness of sub reports inside report can be identified
         */
        "identifier",

        /**
         * Type of the report data. Available types are datatable, time-series-chart and category-chart
         */
        "data_type",

        /**
         * url of the data widget which will be displayed as overall summary of the report
         */
        "data_widget_url",

        /**
         * Url from where data will be fetched
         */
        "data_url",

        /**
         * Chart type which has been selected
         */
        "selected_chart_type",

        /**
         * List of options by which a chart can be viewed. Will be a JSON
         */
        "list_view_by",

        /**
         * Selected View By
         */
        "selected_view_by",

        /**
         * Api endpoint on which custom columns can be added
         */
        "add_custom_column_url",

        /**
         * The layout which graph follows. for eg. 1*1, 2*1 in a page
         */
        "layout",
    ];

    /**
     * Updates relation once model is updated
     * @var array
     */
    public $touches = ["report"];


    public function setListViewByAttribute($value)
    {
        if($value){
            $this->attributes["list_view_by"] = json_encode($value);
        }
    }

    public function getListViewByAttribute($value)
    {
        return json_decode($value);
    }

    public function columns()
    {
        return $this->hasMany(ReportColumn::class);
    }

    public function beforeDelete($model)
    {
        foreach($model->columns as $column){
            $column->delete();
        }
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}