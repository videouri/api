<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserWatchedVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_watched_videos', function ($table) {
            $table->integer('video_id')->unsigned();
            $table->foreign('video_id')->references('id')->on('videos');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            // Set the search term that was used to access a video
            $table->integer('search_term')->unsigned()->nullable();
            $table->foreign('search_term')->references('id')->on('search_history');

            // @TODO: track like/dislike

            $table->timestamp('watched_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_watched_videos');
    }
}
