<ul id="nav-mobile" class="side-nav fixed">
    <li class="logo">
        <a id="logo-container" href="{{ url('/') }}" class="brand-logo">
            <span class="videouri">
                Videouri
            </span>
            <!-- <object id="front-page-logo" type="image/svg+xml" data="res/materialize.svg">Your browser does not support SVG</object> -->
        </a>
    </li>

    <li class="bold"><a href="about.html" class="waves-effect waves-teal">About</a></li>
    <li class="bold"><a href="getting-started.html" class="waves-effect waves-teal">Getting Started</a></li>

    <li class="nav-header"> Explore </li>
    <li class="@if (Route::is('home')) active @endif">
        <a href="{{ route('home') }}">
            <i class="fa fa-home"></i>
            What to watch
        </a>
    </li>

    @if ($currentUser !== 'guest')
    <li class="@if (Route::is('user-favorites')) active @endif">
        <a href="{{ route('user.{name}.favorites.index', ['name' => $currentUser]) }}">
            <i class="fa fa-star"></i>
            Favorites
        </a>
    </li>
    <li class="@if (Route::is('user-history')) active @endif">
        <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'videos']) }}">
            <i class="fa fa-history"></i>
            History
        </a>
    </li>
    @endif
</ul>
