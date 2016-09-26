<?php

namespace Test\Services\Scout\Actions;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Test\Services\Scout\ScoutTestCase;
use Videouri\Entities\Video;
use Videouri\Services\Scout\Actions\GetVideo;

/**
 * @package Test\Services\Scout\Actions
 */
class GetVideoTest extends ScoutTestCase
{
    use DatabaseMigrations;

    /**
     * @var GetVideo
     */
    private $getVideo;

    /**
     * @var Video
     */
    private $video;

    /**
     * @var Video
     */
    private $dmcaVideo;

    /**
     * Setup targeted class to test
     */
    public function setUp()
    {
        parent::setUp();

        $this->getVideo = $this->getMock(GetVideo::class, null);

        $this->video = factory(Video::class)->create();
        $this->dmcaVideo = factory(Video::class)->create([
            'dmca_claim' => true
        ]);
    }

    /**
     * Fetching non-indexed videos should return null
     */
    public function testFetchingUnexistingVideoFromDbReturnsNull()
    {
        $video = $this->getVideo->fetchFromDb('asd1352');

        $this->assertNull($video);
    }

    /**
     * Test that video being fetched matches the stub
     */
    public function testFetchingExistingVideoFromDbReturnsArray()
    {
        $originalId = $this->video->getAttributeValue('original_id');
        $result = $this->getVideo->fetchFromDb($originalId);

        $this->assertEquals($originalId, $result['original_id']);
    }

    /**
     * Videos with DMCA claim, cannot be displayed.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Video cannot be displayed due to DMCA claim.
     */
    public function testFetchingVideoWithDmcaFromDbThrowsException()
    {
        // Persisted Video fixture with dmca
        $video = factory(Video::class)->create([
            'dmca_claim' => true
        ]);
        $this->getVideo->fetchFromDb($video->original_id);

        // see annotation
    }

    /**
     *
     */
    // public function testFetchingVideoFromApiReturnsArray()
    // {
    //     $video = $this->getVideo->fetchFromSource();
    // }
}
