<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStaticPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->default('');
			$table->string('keyword')->default('');
			$table->string('slug')->default('');
			$table->text('desc', 65535)->nullable();
			$table->text('content', 65535)->nullable();
			$table->boolean('is_publish')->default(1);
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
		Schema::dropIfExists('static_pages');
	}

}
