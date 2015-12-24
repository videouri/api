<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Video;
use App\Services\ApiProcessing;

class RefreshVideosData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videouri:refresh-videos-data
                            {--refresh= : Accepts views or duration}
                            {--viewsCondition= : Example: --viewsCondition=">,10"}
                            {--durationCondition= : Example --durationCondition="=,0"}
                            {--videoId= : Refresh just this video\'s data}
                            {--limit= : Limit amount of videos to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the data stored in the db for the videos.';

    /**
     * [$canRefresh description]
     * @var [type]
     */
    // private $canRefresh = ['title', 'description', 'views', 'duration', 'all'];
    private $canRefresh = ['views', 'duration'];

    /**
     * [$apiprocessing description]
     * @var [type]
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
        $this->info("Starting videouri:refresh-videos-data \n");

        $limit = 1000;
        $options = $this->option();

        $toRefresh = explode(',', $options['refresh']);

        // If there is no option set, show error message
        if (!$options['refresh']) {
            $this->error('You must specify at least one parameter to update: ' . implode(', ', $this->canRefresh));
            die;
        } elseif (!in_array_r($toRefresh, $this->canRefresh)) {
            $this->error($options['refresh'] . ' is not a valid option. Available options: ' . implode(', ', $this->canRefresh));
            die;
        }

        if (!($options['videoId'] || $options['viewsCondition'] || $options['durationCondition'])) {
            $this->error('viewsCondition or durationCondition, if not both, must be specified');
            die;
        }

        if ($options['limit']) {
            $limit = (int) $options['limit'];
        }

        $videos = Video::limit($limit);

        // Just this video
        if ($options['videoId']) {
            $videos = $videos->where('original_id', '=', $options['videoId']);
        }

        if ($options['viewsCondition']) {
            $conditions = explode(',', $options['viewsCondition']);
            $videos = $videos->where('views', $conditions[0], intval($conditions[1]));
        }

        if ($options['durationCondition']) {
            $conditions = explode(',', $options['durationCondition']);
            $videos = $videos->where('duration', $conditions[0], $conditions[1]);
        }

        $this->info('Query executed: ' . $videos->getQuery()->toSql());

        $videos = $videos->get();

        $i = 1;
        foreach ($videos as $video) {
            $this->info("\n - Processing video $i out of $limit, from $video->provider and with id $video->original_id");

            try {
                $videoData = $this->apiprocessing->getVideoInfo($video->provider, $video->original_id);

                $videoToUpdate = Video::where('original_id', '=', $video->original_id)->first();

                foreach ($toRefresh as $field) {
                    if (in_array($field, $this->canRefresh)) {
                        $videoToUpdate->{$field} = $videoData[$field];
                        if (is_numeric($videoData[$field])) {
                            $this->info("   \-> Refreshing $field. " . $video->{$field} . " to " . $videoToUpdate->{$field});
                        } else {
                            $this->info("   \-> Refreshing $field. " . strlen($video->{$field}) . " characters to " . strlen($videoToUpdate->{$field}));
                        }

                    }
                }

                // die;
                $videoToUpdate->save();
            } catch (\Exception $e) {
                $this->info("   \-> Error processing this video. ");
                $this->info("   |-> " . get_class($e) . ": " . $e->getMessage() . "\n");
            }

            $i++;
        }
    }
}
