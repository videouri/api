<ul id="nav-mobile" class="side-nav fixed">
    <li class="logo">
        <a id="logo-container" href="{{ url('/') }}" class="brand-logo">
            <span class="videouri">
                Videouri
            </span>
            <!-- <object id="front-page-logo" type="image/svg+xml" data="res/materialize.svg">Your browser does not support SVG</object> -->
        </a>
    </li>

    <br/>

    <li class="side-menu @if (Route::is('home')) active @endif">
        <a href="{{ route('home') }}">
            <i class="fa fa-home"></i> &nbsp;
            What to watch
        </a>
    </li>

    {{-- <li class="side-menu">
        <a href="{{ route('topic.music') }}" class="waves-effect waves-teal">
            <i class="fa fa-music"></i> &nbsp;
            Music
        </a>
    </li>
    <li class="side-menu">
        <a href="{{ route('topic.sports') }}" class="waves-effect waves-teal">
            <i class="fa fa-futbol-o"></i> &nbsp;
            Sports
        </a>
    </li>
    <li class="side-menu">
        <a href="{{ route('topic.trailers') }}" class="waves-effect waves-teal">
            <i class="fa fa-film"></i> &nbsp;
            Trailers
        </a>
    </li>
    <li class="side-menu">
        <a href="{{ route('topic.news') }}" class="waves-effect waves-teal">
            <i class="fa fa-newspaper-o"></i> &nbsp;
            News
        </a>
    </li>
    <li class="side-menu">
        <a href="{{ route('topic.best-of-week') }}" class="waves-effect waves-teal">
            <i class="fa fa-star-o"></i> &nbsp;
            Best of this week
        </a>
    </li> --}}

    @if ($currentUser !== 'guest')
    <br/>
    <li class="divider"></li>
    <br/>

    <li class="side-menu margin favorites @if (Route::is('user.{name}.favorites.index')) active @endif">
        <a href="{{ route('user.{name}.favorites.index', ['name' => $currentUser]) }}">
            <i class="fa fa-star"></i> &nbsp;
            Favorites
        </a>
    </li>

    <li class="side-menu margin later @if (Route::is('user.{name}.watch-later.index')) active @endif">
        <a href="{{ route('user.{name}.watch-later.index', ['name' => $currentUser]) }}">
            <i class="fa fa-clock-o"></i> &nbsp;
            Watch later
        </a>
    </li>
    <li class="side-menu margin history @if (Route::is('user.{name}.history.show')) active @endif">
        <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'videos']) }}">
            <i class="fa fa-history"></i> &nbsp;
            History
        </a>
    </li>
    @endif
</ul>
