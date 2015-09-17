<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Videouri\Entities\Video;
use Videouri\Entities\SearchHistory;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videouri:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap based on data registered in the database';

    protected $videos, $searchHistory;

    protected $videosDumpFilePath = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Video $videos, SearchHistory $searchHistory)
    {
        parent::__construct();

        $this->videos = $videos;
        $this->searchHistory = $searchHistory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! File::exists($this->cacheFilePath)) {
            $videos = $this->videos->whereNotNull('title')->select(['id', 'title', 'description', 'original_id', 'updated_at'])->get();
            $videos = $videos->toArray();

            $videosDump = File::put($this->videosDumpFilePath, serialize($videos));
        } else {
            $previousVideosDump = File::get($this->videosDumpFilePath);
        }


        $this->info('Generating sitemap.xml at public/sitemap.xml');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><xml/>');

        Header('Content-type: text/xml');
        $this->info($xml->asXML());
    }
}

