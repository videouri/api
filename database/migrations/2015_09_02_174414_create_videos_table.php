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
            $table->string('custom_id')->unique()->nullable(); // TODO: remove nullable once populated on LIVE DB

            $table->string('original_url')->unique();
            $table->string('slug')->nullable();

            $table->string('author')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail');
            $table->integer('views')->default(0);
            $table->integer('duration')->default(0); // Value will be in seconds
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();

            $table->boolean('dmca_claim')->default(false);

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
