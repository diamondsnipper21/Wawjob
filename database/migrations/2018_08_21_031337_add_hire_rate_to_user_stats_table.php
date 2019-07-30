<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHireRateToUserStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_stats', function (Blueprint $table) {
            $table->float('hire_rate', 8, 2)->after('job_success');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_stats', function (Blueprint $table) {
            $table->dropColumn('hire_rate');
        });
    }
}
