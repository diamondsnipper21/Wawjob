<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueIdToProjectsContractsApplicationsInvitationsThreadsTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    private $tables = ['projects', 'contracts', 'project_applications', 'project_invitations', 'project_message_threads', 'tickets'];
    public function up()
    {
        foreach ($this->tables as $tb_name) {
            Schema::table($tb_name, function (Blueprint $table) {
                //
                $table->string('unique_id', 30)
                      ->nullable()
                      ->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tb_name) {
            Schema::table($tb_name, function (Blueprint $table) {
                //
                $table->dropColumn('unique_id');
            });
        }
    }
}
