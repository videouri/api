<div id="filter-options" class="row">
        <div class="col s5">
            <a href='#' data-activates='api-sources-dropdown' class="dropdown-button btn choosen-source">
                Source: All
                <span class="caret"></span>
            </a>

            <ul id="api-sources-dropdown" class="dropdown-content">
                <li>
                    <a href="#!" class="video-source" data-filter="*"> All </a>
                </li>

                @foreach ($apis as $api)
                <li>
                    <a href="#!" class="video-source" data-filter=".<?= $api ?>"> <?= $api ?> </a>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- <div class="col-md-7 text-right">
            <h3 style="color: white; margin: 0; text-shadow: 5px 3px 1px #c0392b">Today's most viewed videos</h3>
        </div> --}}

        <?php if (false): // @TODO ?>
        <div class="col s5 text-right" id="options-block">
            <div class="btn-group">
                <button class="btn btn-white">Sort</button>
                <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                <ul class="dropdown-menu dropdown-inverse">
                    <li>
                        <a href="#" class="video-sort" data-source="popular"> <?= lang('popular_videos') ?> </a>
                    </li>
                    <li>
                        <a href="#" class="video-sort" data-source="top_rated"> <?= lang('toprated_videos') ?> </a>
                    </li>
                    <li>
                        <a href="#" class="video-sort" data-source="most_viewed"> <?= lang('mostviewed_videos') ?> </a>
                    </li>
                </ul>
            </div>

            <div class="btn-group">
                <button class="btn btn-white">Period</button>
                <button class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                <ul class="dropdown-menu dropdown-inverse">
                    @foreach ($time as $name => $attr)
                    <li>
                        <a href="#" class="video-period" data-source="<?= $attr ?>">
                            <?= ucfirst($name) ?>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div> <!-- Options block -->
        <?php endif; ?>
    </div>
