<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeOfTitleDescProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            //
            $table->string('subject', 200)
                  ->nullable()
                  ->change();
            //
            $table->string('desc', 5000)
                  ->nullable()
                  ->change();
            //
            $table->integer('category_id')
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
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
}
