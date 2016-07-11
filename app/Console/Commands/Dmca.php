<?php

namespace Videouri\Console\Commands;

use Illuminate\Console\Command;
use Videouri\Entities\Video;

/**
 * @package Videouri\Console\Commands
 */
class Dmca extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videouri:dmca {original_id}
                            {--status=true : True or false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Block video from being accessed';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $originalId = $this->argument('original_id');

        $status = true;
        if ($this->option('status') === 'false') {
            $status = false;
        }

        if ($video = Video::where('original_id', $originalId)->first()) {
            $video->dmca_claim = $status;
            $video->save();

            $this->info('Video\'s dmca_claim set to: ' . json_encode($status));
            return;
        }

        $this->error('No video found with original_id of: ' . $originalId);
        return;
    }
}
