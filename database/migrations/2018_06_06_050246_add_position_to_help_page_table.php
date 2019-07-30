<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToHelpPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_pages', function (Blueprint $table) {
            //
            $table->integer('second_parent_id')
                  ->nullable()
                  ->after('parent_id');

            $table->integer('order')
                  ->nullable()
                  ->after('second_parent_id');

            $table->integer('second_order')
                  ->nullable()
                  ->after('order');

            $table->string('slug', 255)
                  ->nullable()
                  ->after('second_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_pages', function (Blueprint $table) {
            //
            $table->dropColumn('second_parent_id');
            $table->dropColumn('order');
            $table->dropColumn('second_order');
            $table->dropColumn('slug');
        });
    }
}
