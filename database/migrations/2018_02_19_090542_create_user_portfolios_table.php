<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPortfoliosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_portfolios', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('title', 50);
			$table->string('url');
			$table->integer('cat_id')->nullable();
			$table->string('keyword', 100)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('imgurl')->nullable();
			$table->string('category', 100)->nullable();
			$table->integer('block_num')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_portfolios');
	}

}
