<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('target_id')->unsigned();
			$table->string('name', 128);
			$table->boolean('type')->default(0)->comment('0: Project, 1: Message');
			$table->string('mime_type', 50);
			$table->string('ext', 5);
			$table->bigInteger('size');
			$table->string('path');
			$table->boolean('is_approved')->nullable();
			$table->string('hash');
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
		Schema::dropIfExists('files');
	}

}
