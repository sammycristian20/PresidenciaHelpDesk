<?php


namespace App\FaveoReport\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class ReportConfigRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        return [
            "name"=> !$this->id ? "required|string|unique:reports,name" : "required|string",
            "description"=>"required|string",
            "save_column_only"=>"boolean",

            "filters"=>"sometimes|array",
            "filters.*.key"=>"required|string",
            "filters.*.value"=>"required",

            "sub_reports"=>"required|array",
            "sub_reports.*.identifier"=>"required|string",
            "sub_reports.*.selected_chart_type"=>"sometimes",
            "sub_reports.*.selected_view_by"=>"sometimes",

            "sub_reports.*.columns"=>"array|required_if:sub_reports.*.type,==,datatable",
            "sub_reports.*.columns.*.key" => "required|string",
            "sub_reports.*.columns.*.is_visible" => "required|boolean",
            "sub_reports.*.columns.*.is_custom" => "required|boolean",
            "sub_reports.*.columns.*.order" => "required|int",
        ];
    }
}