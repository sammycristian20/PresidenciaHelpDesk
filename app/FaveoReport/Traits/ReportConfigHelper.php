<?php

namespace App\FaveoReport\Traits;

use App\FaveoReport\Models\Report;
use App\FaveoReport\Models\ReportColumn;
use App\FaveoReport\Models\SubReport;

trait ReportConfigHelper
{
    /**
     * Creates a new report
     * @param $parentReport
     * @param $request
     * @return mixed
     */
    private function createNewReport($parentReport, $request)
    {
        if(!$parentReport){
            throw new \UnexpectedValueException("invalid parent report");
        }

        $newReport = $parentReport->replicate();
        // saving so that id can be generated
        $newReport->save();
        $newReport->name= $request->name;
        $newReport->description= $request->description;
        $newReport->is_public = (bool)$request->is_public;
        $newReport->is_default= false;
        $newReport->user_id= \Auth::user()->id;
        $newReport->view_url = str_replace($parentReport->id, $newReport->id, $parentReport->view_url);
        $newReport->export_url = str_replace($parentReport->id, $newReport->id, $parentReport->export_url);
        $newReport->save();

        return $newReport;
    }

    /**
     * Creates new sub report based on report
     * @param $report
     * @param $parentSubReport
     * @param $subReportFromRequest
     * @return mixed
     */
    private function createNewSubReport($report, $parentSubReport, $subReportFromRequest)
    {
        if(!$parentSubReport || !$report){

            throw new \UnexpectedValueException("invalid report/subreport");
        }

        // get all subreports
        $newSubReport = $parentSubReport->replicate();
        $newSubReport->report_id = $report->id;
        $newSubReport->save();
        $newSubReport->selected_chart_type= $subReportFromRequest["selected_chart_type"];
        $newSubReport->selected_view_by= $subReportFromRequest["selected_view_by"];
        $newSubReport->data_widget_url = str_replace($parentSubReport->report_id, $newSubReport->report_id, $parentSubReport->data_widget_url);
        $newSubReport->data_url = str_replace($parentSubReport->report_id, $newSubReport->report_id, $parentSubReport->data_url);
        $newSubReport->add_custom_column_url = str_replace($parentSubReport->id, $newSubReport->id, $parentSubReport->add_custom_column_url);
        $newSubReport->save();

        // NOTE: there will be no column change in case of create, so can save it as it is
        // get all columns from parent sub report and create those columns for this report
        foreach ($parentSubReport->columns as $column) {
            $newSubReport->columns()->create($column->getOriginal());
        }

        return $newSubReport;
    }

    /**
     * Updates existing report
     * @param $reportToBeUpdated
     * @param $request
     * @return mixed
     */
    private function updateExistingReport($reportToBeUpdated, $request)
    {
        if(!$reportToBeUpdated){
            throw new \UnexpectedValueException("report not found");
        }

        return $reportToBeUpdated->update([
            "name"=> $request->name,
            "description"=> $request->description,
            "is_public" => $request->is_public
        ]);
    }

    /**
     * Updates existing sub report
     * @param $subReportToBeUpdated
     * @param $subReportFromRequest
     * @return mixed
     */
    private function updateExistingSubReport($subReportToBeUpdated, $subReportFromRequest)
    {
        if(!$subReportToBeUpdated){
            throw new \UnexpectedValueException("sub report not found");
        }

        return $subReportToBeUpdated->update([
            "selected_chart_type"=> $subReportFromRequest["selected_chart_type"],
            "selected_view_by"=> $subReportFromRequest["selected_view_by"],
        ]);
    }

    /**
     * Saves report filters
     * @param Report $parentReport
     * @param array $filterArray
     */
    private function saveReportFilters(Report $parentReport, array $filterArray)
    {
        $filterInstance = $parentReport->filter()->firstOrCreate(["status"=> 1]);
        // deleting old filter meta data and then adding them back
        // REASON: if someone removes a filter element, we are not making an API call, so if we simply update the filter
        // we will miss the cases where someone has removed a filter
        $filterInstance->filterMeta()->delete();

        foreach ($filterArray as $filter){
            // deleting all meta data of the filter on each save because once value has been removed, there is no API call to update that
            $filterInstance->filterMeta()->create(["key"=>$filter['key'], "value"=> $filter['value']]);
        }
    }

    /**
     * Associative array of ReportColumn object
     * @param SubReport &$subReport
     * @param array $column
     */
    private function saveSubReportColumn(SubReport &$subReport, array $column)
    {
        // if this key is already present in the DB with some report
        if(ReportColumn::where("key", $column["key"])->count()){
            $subReport->columns()->where("key", $column["key"])
                ->update([
                    "is_visible" => $column["is_visible"],
                    "is_timestamp"=> $column["is_timestamp"],
                    "order"=> $column["order"],
                    "timestamp_format"=> isset($column["timestamp_format"]) ? $column["timestamp_format"] : null
                ]);
        }
    }
}