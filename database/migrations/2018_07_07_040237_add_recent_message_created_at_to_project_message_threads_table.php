<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecentMessageCreatedAtToProjectMessageThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_message_threads', function (Blueprint $table) {
            $table->timestamp('recent_message_created_at')->nullable()->after('receiver_read_at');
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
            $table->dropColumn('recent_message_created_at');
        });
    }
}
