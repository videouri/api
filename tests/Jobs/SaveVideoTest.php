<?php

namespace Videouri\Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Videouri\Entities\Video;
use Videouri\Jobs\SaveVideo;
use Videouri\Tests\AbstractTestCase;

/**
 * @package Videouri\Tests\Jobs
 */
class SaveVideoTest extends AbstractTestCase
{
    use DatabaseTransactions;

    public function testSaveVideo()
    {
        $this->expectsJobs(SaveVideo::class);

        $video = factory(Video::class)->make();
        $job = new SaveVideo($video, $video->provider);

        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
}
