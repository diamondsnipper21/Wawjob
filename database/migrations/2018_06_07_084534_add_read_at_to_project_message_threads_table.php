<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadAtToProjectMessageThreadsTable extends Migration
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
            $table->timestamp('sender_read_at')->nullable()->after('is_archived');
            $table->timestamp('receiver_read_at')->nullable()->after('sender_read_at');
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
            $table->dropColumn('sender_read_at');
            $table->dropColumn('receiver_read_at');
        });
    }
}
