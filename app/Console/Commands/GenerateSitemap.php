<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use App\Entities\Search;
use App\Entities\Sitemap;
use App\Entities\Video;

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

    // VIDEOURI

    /**
     * @var Video
     */
    protected $videos;

    /**
     * @var Search
     */
    protected $Search;

    /**
     * [$videoDumpPath description]
     * @var string
     */
    protected $videoDumpPath;

    protected $sitemapsDirectory;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Video $videos, Search $searchHistory)
    {
        parent::__construct();

        $this->videos = $videos;
        $this->searchHistory = $searchHistory;

        $this->videoDumpPath = storage_path('app/videoDumpPath.json');
        $this->sitemapsDirectory = public_path('sitemaps');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ///
        $this->info('Initialized sitemap generating tool');
        ///

        $mainSitemapPath = $this->sitemapsDirectory . '/main.xml';

        $xmlHeading = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
    <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    </sitemapindex>
EOF;

        $xml = new \SimpleXMLElement($xmlHeading);

        // $sitemapindex = $xml->sitemapindex;

        self::generateVideoSitemap();

        ///
        $this->info('Appending video sitemap(s) to main sitemap');
        ///

        $sitemaps = Sitemap::all();
        foreach ($sitemaps as $sitemap) {
            $updated_at = explode(' ', $sitemap->updated_at);
            $updated_at = $updated_at[0];

            $filename = $sitemap->filename;

            $sitemap = $xml->addChild('sitemap');
            $sitemap->addChild('loc', videouri_url('/sitemaps/' . $filename));
            $sitemap->addChild('lastmod', $updated_at);
        }

        ///
        $this->info('Saving main sitemap at ' . $mainSitemapPath);
        ///

        $xml->asXML($mainSitemapPath);
    }

    private function generateVideoSitemap()
    {
        ///
        $this->info('Started processing videos into sitemap');
        ///

        $fields = [
            'id', 'original_id', 'custom_id', 'provider',
            'title', 'description',
            'thumbnail', 'duration',
            'updated_at', 'created_at',
        ];

        // Initialize base Video eloquent query
        $videos = $this->videos
                       ->whereNotNull('title')
                       ->where('duration', '>', 0)
                       ->where(function ($query) {
                           $query->where('provider', 'Youtube')
                                 ->orWhere('provider', 'Vimeo');
                       });

        // Default limit value
        $limit = 5000;

        // Retrieve last sitemap create, if there is one
        $lastSitemap = Sitemap::orderBy('id', 'desc')->take(1)->first();

        // Load last video sitemap or create a new one
        if ($lastSitemap &&
            File::exists($lastSitemap['path']) &&
            $lastSitemap->items_count < 50000) {
            $xml = simplexml_load_file($lastSitemap['path']);

            if (($xml->count() + $limit === 50000) && (50000 - $xml->count() < $limit)) {
                $limit = 50000 - $xml->count();
            }

        } else {
            $xmlHeading = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
    <urlset
            xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xhtml="http://www.w3.org/1999/xhtml"
            xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
            xsi:schemaLocation="
                http://www.sitemaps.org/schemas/sitemap/0.9
                http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" />
EOF;
            $xml = new \SimpleXMLElement($xmlHeading);
        }

        // Get videos where id > than last one
        if (File::exists($this->videoDumpPath)) {
            $lastVideoDump = unserialize(File::get($this->videoDumpPath));
            $videos = $videos->where('id', '>', $lastVideoDump['id']);
        }

        $videos = $videos->limit($limit)->get($fields);

        ///
        $this->info('Appending videos to video sitemap');
        ///

        // Add videos to main xml
        // $videos = $videos->toArray();
        foreach ($videos as $video) {
            $xmlUrl = $xml->addChild('url');

            $description = str_limit($video->description, 1020);
            $description = $this->utf8ForXml($description);
            $description = htmlspecialchars($description);

            $created_at = explode(' ', $video->created_at);
            $created_at = $created_at[0];

            $updated_at = explode(' ', $video->updated_at);
            $updated_at = $updated_at[0];

            $xmlUrl->addChild('loc', $video->custom_url);
            $xmlUrl->addChild('lastmod', $updated_at);
            // $xmlUrl->addChild('changefreq', 'monthly');
            // $xmlUrl->addChild('priority', '1.0');

            $videoGroup = $xmlUrl->addChild('video:video', null, 'http://www.google.com/schemas/sitemap-video/1.1');
            $videoGroup->addChild('video:thumbnail_loc', $video->thumbnail);
            $videoGroup->addChild('video:title', htmlspecialchars($video->title));
            $videoGroup->addChild('video:description', $description);
            // $videoGroup->addChild('video:player_loc', $videoUrl);
            $videoGroup->addChild('video:duration', $video->duration);
            $videoGroup->addChild('video:publication_date', $created_at);
            // $videoGroup->addChild('video:tag', $video->tags);
        }

        // Dump last video information into a file
        $videosCount = count($videos);
        $lastVideo = $videos[$videosCount - 1];

        // Save report file
        $sitemapId = 1;

        if ($lastSitemap && $lastSitemap->items_count < 50000) {
            $sitemapId = $lastSitemap->id;
        } elseif ($lastSitemap && $lastSitemap->items_count == 50000) {
            $sitemapId = $lastSitemap->id + 1;
        }

        ///
        $this->info('Sitemap videos count: ' . $xml->count());
        ///

        $videoSitemapName = 'videos-index-' . $sitemapId . '.xml';
        $videoSitemapPath = $this->sitemapsDirectory . '/' . $videoSitemapName;

        $videoDumpPath = File::put($this->videoDumpPath, serialize($lastVideo));

        // Save xml file
        Header('Content-type: text/xml; charset=utf-8');
        $xml->asXML($videoSitemapPath);

        // Save sitemap info into DB
        if ($lastSitemap &&
            File::exists($lastSitemap['path']) &&
            $lastSitemap->items_count < 50000
        ) {
            $lastSitemap->items_count = $xml->count();
            $lastSitemap->save();
        } else {
            Sitemap::create([
                'path'        => $videoSitemapPath,
                'filename'    => $videoSitemapName,
                'items_count' => $xml->count(),
            ]);
        }

        // $this->info('Sitemap create at $this->videoSitemapPath');
        // $this->error('Couldn't save sitemap at $this->videoSitemapPath');
    }

    private function utf8ForXml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }
}
