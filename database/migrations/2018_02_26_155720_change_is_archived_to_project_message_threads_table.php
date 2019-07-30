<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIsArchivedToProjectMessageThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_message_threads', function (Blueprint $table) {
            //
            $table->string('is_archived', 20)
                  ->comment('[user_id][...]...')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_message_threads', function (Blueprint $table) {
            //
        });
    }
}
