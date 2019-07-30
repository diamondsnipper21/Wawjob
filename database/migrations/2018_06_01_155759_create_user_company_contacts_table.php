<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCompanyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_company_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->integer('timezone_id')->unsigned()->default(0)->index();
            $table->string('address', 128)->nullable();
            $table->string('city', 48)->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('state', 48)->nullable();
            $table->string('phone', 24)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_company_contacts');
    }
}
