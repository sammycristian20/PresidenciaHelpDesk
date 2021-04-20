<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKbArticleToFullTextMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE kb_category ADD FULLTEXT name_index(name)");
        DB::statement("ALTER TABLE kb_category ADD FULLTEXT description_index(description)");
        DB::statement("ALTER TABLE kb_category ADD FULLTEXT name_description_index(name,description)");
        DB::statement("ALTER TABLE kb_article ADD FULLTEXT name_index(name)");
        DB::statement("ALTER TABLE kb_article ADD FULLTEXT description_index(description)");
        DB::statement("ALTER TABLE kb_article ADD FULLTEXT name_description_index(name, description)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('kb_category', function($table) {
        $table->dropIndex('name_index');
        $table->dropIndex('description_index');
        $table->dropIndex('name_description_index');
      });

      Schema::table('kb_article', function($table) {
        $table->dropIndex('name_index');
        $table->dropIndex('description_index');
        $table->dropIndex('name_description_index');
      });
    }
}
