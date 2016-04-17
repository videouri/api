<?php

namespace Test\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
// use Illuminate\Contracts\Validation\ValidationException;
// use Mockery as m;

class SaveVideoTest extends \TestCase
{
    use DatabaseTransactions;

    public function testSaveVideo()
    {
        $this->expectsJobs(\App\Jobs\SaveVideo::class);

        $video = factory(\App\Entities\Video::class)->make();
        $job = new \App\Jobs\SaveVideo($video, $video->provider);

        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
}
