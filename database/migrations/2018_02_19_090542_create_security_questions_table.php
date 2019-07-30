<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSecurityQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('security_questions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('question', 256);
			$table->integer('category_id')->unsigned()->nullable();
			$table->boolean('is_active')->default(1);
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('security_questions');
	}

}
