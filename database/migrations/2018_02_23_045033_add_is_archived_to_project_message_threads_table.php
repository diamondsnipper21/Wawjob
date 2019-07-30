<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsArchivedToProjectMessageThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_message_threads', function (Blueprint $table) {
            // add is_archived column
            $table->tinyInteger('is_archived', false, true)
                  ->nullable()
                  ->comment('1: archived')
                  ->after('is_favourite');
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
            $table->dropColumn('is_archived');
        });
    }
}
