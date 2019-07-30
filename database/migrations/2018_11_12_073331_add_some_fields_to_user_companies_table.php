<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldsToUserCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_companies', function (Blueprint $table) {
            //
            $table->string('phone', 50)
                  ->nullable()
                  ->after('description');

            $table->string('address1', 255)
                  ->nullable()
                  ->after('phone');

            $table->string('address2', 255)
                  ->nullable()
                  ->after('address1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_companies', function (Blueprint $table) {
            //
            $table->dropColumn('phone');
            $table->dropColumn('address1');
            $table->dropColumn('address2');
        });
    }
}
