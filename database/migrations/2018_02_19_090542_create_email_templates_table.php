<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_templates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('slug', 100);
			$table->boolean('for')->default(1)->comment('User role. 0: General User');
			$table->text('subject', 65535)->nullable();
			$table->text('content', 65535)->nullable();
			$table->boolean('status')->nullable()->default(1)->comment('0: disable 1: enable');
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
		Schema::dropIfExists('email_templates');
	}

}
