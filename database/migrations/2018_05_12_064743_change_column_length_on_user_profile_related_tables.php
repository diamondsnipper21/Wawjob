<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnLengthOnUserProfileRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_experiences', function (Blueprint $table) {
            $table->string('title', 100)->change();
            $table->string('description', 500)->change();
        });

        //
        Schema::table('user_educations', function (Blueprint $table) {
            $table->string('school', 100)->change();
            $table->string('degree', 100)->change();
            $table->string('major', 150)->change();
        });
        //
        Schema::table('user_employments', function (Blueprint $table) {
            $table->string('company', 100)->change();
            $table->string('desc', 1000)->change();
        });
        //
        Schema::table('user_certifications', function (Blueprint $table) {
            $table->string('title', 200)->change();
            $table->string('description', 255)->change();
        });
        //
        Schema::table('user_portfolios', function (Blueprint $table) {
            $table->string('description', 255)->change();
        });
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
