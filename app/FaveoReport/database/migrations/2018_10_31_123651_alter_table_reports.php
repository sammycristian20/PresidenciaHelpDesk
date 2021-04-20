<?php

use App\FaveoReport\Models\ReportDownload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->char('ext', 5)->after('file');
                $table->boolean('is_completed')->default(0)->after('user_id');
            });

            // NOTE: direct migration not possible until version is updated to v2.3.0
            //            $this->migrateData();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('reports', 'ext')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('ext');
            });
        }

        if (Schema::hasColumn('reports', 'is_completed')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('is_completed');
            });
        }
    }
//
//    private function migrateData()
//    {
//        ReportDownload::where('id', '>', 0)->update(['ext' => 'xls', 'is_completed' => 1]);
//
//        DB::update("UPDATE `reports` SET `file` = REPLACE(`file`, '.xls', '') where `id` > 0");
//    }
}
