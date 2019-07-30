<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSecurityQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_security_questions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->default(0)->index('user_id');
			$table->integer('question_id')->default(0)->index('question_id');
			$table->string('answer');
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
		Schema::dropIfExists('user_security_questions');
	}

}
