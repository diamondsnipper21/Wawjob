<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHelpPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('content', 65535)->nullable();
            $table->integer('parent_id')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('help_pages');
    }
}
