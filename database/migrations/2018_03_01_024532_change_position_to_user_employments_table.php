<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePositionToUserEmploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_employments', function (Blueprint $table) {
            //

            $table->string('company', 512)
                  ->nullable()
                  ->change();

            $table->string('desc', 1024)
                  ->nullable()
                  ->change();

            $table->string('position', 128)
                  ->nullable()
                  ->change();
        });

        //
        Schema::table('user_educations', function (Blueprint $table) {
            //
            $table->string('school', 512)
                  ->nullable()
                  ->change();

            $table->string('degree', 128)
                  ->nullable()
                  ->change();

            $table->string('major', 128)
                  ->nullable()
                  ->change();

            $table->string('desc', 1024)
                  ->nullable()
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
        Schema::table('user_employments', function (Blueprint $table) {
            //
        });
    }
}
