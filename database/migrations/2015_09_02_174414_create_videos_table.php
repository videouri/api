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
            $table->string('original_id');
            $table->string('custom_id');

            $table->string('author');
            $table->integer('duration')->default(0);
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);

            $table->text('data');

            // Determine whether a video has a DMCA claim
            $table->boolean('dmca_claim')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['original_id', 'author', 'duration', 'views', 'likes', 'dislikes']);
            $table->unique(['original_id']);
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
