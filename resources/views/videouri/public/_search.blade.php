@extends('app')

@section('content')
<div class="container" id="content">
    <div id="filter-options" class="row">
        <div class="col-xs-7">
            <div class="btn-group">
                <button class="btn btn-white choosen-source">Source: All</button>
                <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                <ul class="dropdown-menu dropdown-inverse">
                    <li>
                        <a href="#" class="video-source" data-filter="*"> All </a>
                    </li>

                    <?php foreach ($apis as $api): ?>
                    <li>
                        <a href="#" class="video-source" data-filter=".<?= $api ?>"> <?= $api ?> </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div id="videos" class="row">
        <?php foreach ($data as $video): ?>
        <div class="col-md-4 <?= $video['source'] ?>">
            <div class="video">
                <div class="image">
                    <a href="<?= $video['url'] ?>">
                        <img src="<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>" class="img-responsive"/>
                    </a>
                    <span class="fui-play" style="position: absolute; top: 35%; left: 45%; color: #fff; font-size: 30px; text-shadow: 0px 0px 20px #000, 1px -3px 0px #45c8a9" data-url="<?= $video['url'] ?>"></span>
                </div>

                <span class="source <?= $video['source'] ?>">
                    <?= $video['source'] ?>
                </span>

                <h1 class="title">
                    <a href="<?= $video['url'] ?>" title="<?= $video['title'] ?>">
                        <?= $video['title'] ?>
                    </a>
                </h1>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <?php // @TODO ?>
    @if (false)
    <div id="page" class="row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <ul class="pagination">
                <li class="previous">
                    <a href="<?= ($page == 1) ? '#' : urlToPage($page, 'previous') ?>">
                        <i class="fa fa-arrow-left"></i>
                        Previous Page
                    </a>
                </li>
                <li class="next">
                    <a href="<?= urlToPage($page, 'next') ?>">
                        Next Page
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ videouri_asset('/js/modules/videosListing.js') }}"></script>
@endsection
