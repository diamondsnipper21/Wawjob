<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLengthOfContentToStaticPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('static_pages', function (Blueprint $table) {
            //
            // $table->mediumText('content')
            //       ->change();

            DB::statement('ALTER TABLE `static_pages` CHANGE COLUMN `content` `content` MEDIUMTEXT NULL COLLATE \'utf8_unicode_ci\' AFTER `desc`;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('static_pages', function (Blueprint $table) {
            //
        });
    }
}
