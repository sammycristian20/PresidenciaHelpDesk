<?php

namespace database\seeds\v_2_1_8;

use database\seeds\DatabaseSeeder as Seeder;
use App\FaveoReport\Models\ReportColumn;

class ManagementReportSeeder extends Seeder
{

  /**
   * method to execute database seeds
   * @return void
   */
  public function run()
  {
    // seed default columns that are required
    $this->createReportColumn('ticket_number', 'ticket_number', true, false, true);
    $this->createReportColumn('subject', 'subject', false, false, true);
    $this->createReportColumn('statuses.name', 'status', false, false, true);
    $this->createReportColumn('department.name', 'department', false, false, true);
    $this->createReportColumn('helptopic.name', 'helptopic', false, false, true);
    $this->createReportColumn('types.name', 'type', false, false, true);
    $this->createReportColumn('priority.name', 'priority', false, false, true);
    $this->createReportColumn('user.name', 'owner', false, false, true);
    $this->createReportColumn('organizations', 'organizations', false, false, true);
    $this->createReportColumn('assigned.name', 'assigned_agent', false, false, true);
    $this->createReportColumn('sources.name', 'source', false, false, true);
    $this->createReportColumn('assigned_team.name', 'assigned_team', false, false, true);
    $this->createReportColumn('creator.name', 'creator', false, false, true);
    $this->createReportColumn('location.name', 'location', false, false, true);
    $this->createReportColumn('time_tracked', 'time_tracked', false, false, false);
    $this->createReportColumn('overdue', 'is_overdue', false, false, false);
    $this->createReportColumn('is_response_sla', 'has_response_sla_met', true, false, false);
    $this->createReportColumn('is_resolution_sla', 'has_resolution_sla_met', true, false, false);
    $this->createReportColumn('labels', 'labels', false, false, true);
    $this->createReportColumn('tags', 'tags', false, false, true);
    $this->createReportColumn('resolution_time', 'resolved_in', true, false, false);
    $this->createReportColumn('first_response_time', 'first_replied_at', true, true, false);
    $this->createReportColumn('created_at', 'created_at', true, true, false);
    $this->createReportColumn('updated_at', 'updated_at', true, true, false);
    $this->createReportColumn('closed_at', 'closed_at', true, true, false);
    $this->createReportColumn('description', 'description', false, false, false, false);
  }

  private function createReportColumn($key, $label, $isSortable, $isTimestamp, $isHtml, $isVisible = true)
  {
    ReportColumn::updateOrCreate(['key'=>$key], ['key'=>$key, 'label'=>$label,
      'is_sortable'=>$isSortable, 'is_timestamp'=>$isTimestamp, 'is_html'=>$isHtml, 'is_visible'=>$isVisible]);
  }
}
