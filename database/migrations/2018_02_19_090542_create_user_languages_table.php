<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLanguagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_languages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('lang_id');
			$table->softDeletes();
			$table->unique(['user_id','lang_id'], 'users_languages_user_id_lang_id_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_languages');
	}

}
