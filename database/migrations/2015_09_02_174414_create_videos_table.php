<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');

            $table->string('provider');
            $table->string('original_id')->unique();

            // TODO: remove nullable once populated on LIVE DB
            $table->string('custom_id')->unique()->nullable();

            // Maybe remove this and build URL from custom_id on the go?
            $table->string('videouri_url');
            $table->string('slug')->nullable();

            $table->string('author')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail');
            $table->integer('views')->default(0);
            $table->integer('duration')->default(0); // Value will be in seconds
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();

            $table->softDeletes();
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
        Schema::drop('videos');
    }
}
