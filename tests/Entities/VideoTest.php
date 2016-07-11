<?php

namespace Videouri\Tests\Entities;

use Videouri\Entities\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Entities
 */
class VideoTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testModelFactory()
    {
        $video = factory(Video::class)->create();
        $videoFromDB = Video::where('original_id', $video->original_id)->first();

        $this->assertEquals($video->original_id, $videoFromDB->original_id);
    }
}
