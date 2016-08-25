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
            $table->string('original_url');

            $table->string('custom_id');
            $table->string('slug')->nullable();

            // json encoded video data as returned from API
            $table->json('data');

            // Determine whether a video has a DMCA claim
            $table->boolean('dmca_claim')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['original_id', 'original_url', 'custom_id']);
            $table->index(['original_id', 'custom_id']);
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
