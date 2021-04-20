<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeSettingsAlertNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::table('settings_alert_notice', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_status',
                'ticket_admin_email',
                'ticket_department_manager',
                'ticket_department_member',
                'ticket_organization_accmanager',
                'message_status',
                'message_last_responder',
                'message_assigned_agent',
                'message_department_manager',
                'message_organization_accmanager',
                'internal_status',
                'internal_last_responder',
                'internal_assigned_agent',
                'internal_department_manager',
                'assignment_status',
                'assignment_assigned_agent',
                'assignment_team_leader',
                'assignment_team_member',
                'transfer_status',
                'transfer_assigned_agent',
                'transfer_department_manager',
                'transfer_department_member',
                'overdue_status',
                'overdue_assigned_agent',
                'overdue_department_manager',
                'overdue_department_member',
                'system_error',
                'sql_error',
                'excessive_failure'
            ]);

            $table->string('key');
            $table->string('value');        
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
