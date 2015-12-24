<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Video;
use App\Services\ApiProcessing;

class UpdateCustomId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videouri:refresh-custom-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh those empty custom_id';

    /**
     * @var App\Services\ApiProcessing
     */
    private $apiprocessing;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ApiProcessing $apiprocessing)
    {
        parent::__construct();

        $this->apiprocessing = $apiprocessing;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $videos = Video::where('custom_id', null)
            ->limit(100)
            ->get(['provider', 'original_id', 'custom_id']);

        $videosCount = $videos->count();

        $i = 1;
        foreach ($videos as $video) {
            $this->info('Processing video ' . $i . ' of ' . $videosCount);

            switch ($video->provider) {
                case 'Dailymotion':
                    $api = 'd';
                    break;

                case 'Vimeo':
                    $api = 'v';
                    break;

                case 'Youtube':
                    $api = 'y';
                    break;

                case 'Metacafe':
                    $api = 'm';
                    break;
            }

            $custom_id = substr($video->original_id, 0, 1) . $api . substr($video->original_id, 1);

            Video::where('original_id', $video->original_id)->update(['custom_id' => $custom_id]);

            $i++;
        }
    }
}
