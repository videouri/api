<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('provider');
            $table->string('original_id')->unique();
            $table->string('videouri_url');

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
