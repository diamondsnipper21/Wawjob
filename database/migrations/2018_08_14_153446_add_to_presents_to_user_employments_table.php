<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToPresentsToUserEmploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_employments', function (Blueprint $table) {
            //
            $table->tinyInteger('to_present')
                  ->default(0)
                  ->after('to_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_employments', function (Blueprint $table) {
            //
            $table->dropColumn('to_present');
        });
    }
}
