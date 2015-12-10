@extends('app')

@section('content')
<div class="vbg">
    <video id="videoPlayer" src="<?= $video['url'] ?>" class="video-js vjs-default-skin vjs-big-play-centered"
           data-src="<?= strtolower($source) ?>" data-url="<?= $video['url'] ?>"
           controls preload="auto" width="100%" height="530">
        <p>Video Playback Not Supported</p>
    </video>
</div>

<div id="video-info" class="container">
    <div class="row" id="title-share-and-stats">
        <div class="col s7">
            <h4>
                <?= $video['title'] ?>
            </h4>
        </div>

        <div class="col s5 right-align" id="share-and-stats">
            <ul>
                <li>
                    <a href="https://www.facebook.com/sharer.php" id="facebook-share" class="popup facebook-color" title="Share to Facebook">
                        <i class="fa fa-facebook-official fa-2x" style="vertical-align: middle"></i>
                    </a>
                </li>
                <li>
                    <a href="https://twitter.com/share" id="twitter-share" class="popup twitter-color" title="Share to Twitter">
                        <i class="fa fa-twitter fa-2x" style="vertical-align: middle"></i>
                    </a>
                </li>

                <li class="divider">
                    &nbsp;&nbsp;&nbsp;
                </li>

                <li class="chip">
                    <i class="fa fa-eye"></i>
                    {{-- {{ humanizeNumber($video['views']) }} --}}
                    {{ $video['views'] }}
                </li>

                <li class="divider">
                    &nbsp;&nbsp;&nbsp;
                </li>

                <li>
                    <a href="#!favorite" id="favorite-video" class="valign" data-id="{{ $video['origId'] }}">
                        <?php
                            $starClass = 'fa-star-o';
                            if ($video->favorited) {
                                $starClass = 'fa-star';
                            }
                        ?>
                        <i class="fa {{ $starClass }} fa-2x" style="vertical-align: middle"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <hr style="border: 1px dotted #eee;"/>

    <div class="row">
        <div class="col s9">
            <p class="flow-text">
                <?php
                    if (empty($video['description']) ||
                        $video['description'] === $video['title']
                    ) {
                        $video['description'] =  'No description';
                    }

                    // echo parseLinks(nl2br($video['description']));
                    echo nl2br($video['description']);
                ?>
            </p>
        </div>
        @if (!empty($video['tags']))
        <div class="col s3">
            <h6>Tags</h6>
            @foreach ($video['tags'] as $tag)
            <?php
                $url = url('search?query='.$tag);
            ?>
            <div class="chip" style="margin-bottom: 5px;">
                <a title="{{ $tag }}" href="{{ $url }}">
                    {{ $tag }}
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@if (!empty($video['related']))
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h4 style="margin-bottom: 0">
                Recommended
            </h4>
        </div>
    </div>
    {{-- <hr style="border-color: #c0392b" /> --}}
    <div id="related-videos" class="row">
        @foreach ($video['related'] as $relatedVideo)
        <div class="col s4 video <?= $relatedVideo['source'] ?>">
            <div class="card hoverable">
                <div class="card-image">
                    <a href="<?= $relatedVideo['url'] ?>" title="<?= $relatedVideo['title'] ?>">
                        <img src="<?= $relatedVideo['thumbnail'] ?>" alt="<?= $relatedVideo['title'] ?>">
                    </a>
                    <span class="fui-play" style="position: absolute; top: 35%; left: 45%; color: #fff; font-size: 30px; text-shadow: 0px 0px 20px #000, 1px -3px 0px #45c8a9" data-url="{{ $relatedVideo['url'] }}"></span>

                    <span class="video-source {{ $relatedVideo['source'] }}">
                        {{ $relatedVideo['source'] }}
                    </span>
                </div>
                <div class="card-content">
                    <p>
                        <?= $relatedVideo['title'] ?>
                    </p>
                    </h2>
                </div>
                <div class="card-action">
                    <a href="<?= $relatedVideo['url'] ?>" title="<?= $relatedVideo['title'] ?>">
                        Watch video
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@section('scripts')
<script type="text/javascript">
videojs.options.flash.swf = "/dist/misc/video-js.swf";
var $isotopeContainer;

$(document).ready(function($) {

    /**
     * Isotope plugin
     */
    $isotopeContainer = $('#related-videos').isotope({
        itemSelector: '.col-md-3',
        layoutMode: 'masonry'
    });

    var title = encodeURIComponent(document.title),
        url   = encodeURI(window.location.href);

    var facebookUrl = 'http://www.facebook.com/sharer.php?u='+url+'&t='+title,
        tuentiUrl   = 'http://www.tuenti.com/?m=Share&func=index&url='+url+'&suggested-text=',
        twitterUrl  = 'https://twitter.com/intent/tweet?url='+url+'&text='+title+'&via=videouri';

    $('#facebook-share').attr('href', facebookUrl);
    $('#tuenti-share').attr('href', tuentiUrl);
    $('#twitter-share').attr('href', twitterUrl);

    $('.popup').click(function(event) {
        var width  = 575,
            height = 400,
            left   = ($(window).width()  - width)  / 2,
            top    = ($(window).height() - height) / 2,
            url    = this.href,
            title  = $(this).attr('id'),
            opts   = 'status=1' +
                   ',width='  + width  +
                   ',height=' + height +
                   ',top='    + top    +
                   ',left='   + left;

        window.open(url, title, opts);

        return false;
    });

    var videoSource = $('#videoPlayer').data('src'),
        videoUrl    = $('#videoPlayer').data('url');

    videojs('videoPlayer', {"techOrder": [videoSource], "src": videoUrl}).ready(function() {

        // You can use the video.js events even though we use the vimeo controls
        // As you can see here, we change the background to red when the video is paused and set it back when unpaused
        // this.on('pause', function() {
        //     document.body.style.backgroundColor = 'red';
        // });

        // this.on('play', function() {
        //     document.body.style.backgroundColor = '';
        // });

        // You can also change the video when you want
        // Here we cue a second video once the first is done
        // this.one('ended', function() {
        //     this.src('http://vimeo.com/79380715');
        //     this.play();
        // });
    });
});

</script>
@endsection
