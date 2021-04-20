<?php


namespace App\FaveoReport\Models;

use App\BaseModel;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Traits\Observable;
use DB;
use Lang;
use Auth;
use UnexpectedValueException;

class Report extends BaseModel
{
    use Observable;

    protected $fillable = [

        /**
         * Name of the report
         */
        "name",

        /**
         * Description of the report
         */
        "description",

        /**
         * Type of the report
         */
        "type",

        /**
         * Icon which has to be displayed for the report
         */
        "icon_class",

        /**
         * Category of the report (if it belongs to Helpdesk Analysis, Productivity or Customer Happiness. More categories can be added too)
         */
        "category",

        /**
         * If report is default or custom
         */
        "is_default",

        /**
         * Url where report should render
         */
        "view_url",

        /**
         * Url where report can be exported
         */
        "export_url",

        /**
         * The person who is creating the report
         */
        "user_id",

        /**
         * If report should be publicly available
         */
        "is_public",
    ];

    /**
     * All configuration of the report
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function filter()
    {
        return $this->morphOne(TicketFilter::class, 'parent');
    }

    /**
     * All redirects from report list page
     */
    public function getViewUrlAttribute($value)
    {
        if(!$value){
            $id = $this->getAttribute("id");

            $type = $this->getAttribute("type");

            return "reports/$type/$id";
        }

        return $value;
    }

    public function subReports()
    {
        return $this->hasMany(SubReport::class);
    }

    public function getNameAttribute($value)
    {
        if($this->getAttribute("is_default")){
            return Lang::get("report::lang.$value");
        }
        return $value;
    }

    public function getCategoryAttribute($value)
    {
        return Lang::get("report::lang.$value");
    }

    public function getDescriptionAttribute($value)
    {
        if($this->getAttribute("is_default")){
            return Lang::get("report::lang.$value");
        }
        return $value;
    }

    public function beforeDelete($model)
    {
        if($model->is_default){
            throw new UnexpectedValueException("default report cannot be deleted");
        }

        foreach($model->subReports as $subReport){
            $subReport->delete();
        }

        $model->filter->delete();
    }

    /**
     * If report is accessible or not
     * @param $reportId
     * @return bool
     */
    public static function isReportAccessible($reportId) : bool
    {
        return (bool) Report::where("id", $reportId)->where(function($q){
            $q->where("is_default", 1)
                ->orWhere("user_id", Auth::user()->id)
                ->orWhere("is_public", 1);
        })->count();
    }
}