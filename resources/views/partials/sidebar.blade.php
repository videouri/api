<ul id="nav-mobile" class="side-nav fixed">
    <li class="logo">
        <a id="logo-container" href="{{ url('/') }}" class="brand-logo">
            <span class="videouri">
                Videouri
            </span>
        </a>
    </li>

    <br/>

    <li class="side-menu @if (Route::is('home')) active @endif">
        <a href="{{ route('home') }}">
            <i class="fa fa-home"></i>
            What to watch
        </a>
    </li>

    @if ($currentUser !== 'guest')
        <br/>
        <li class="divider"></li>
        <br/>

        <li class="side-menu margin favorites @if (Route::is('user.{name}.favorites.index')) active @endif">
            <a href="{{ route('user.{name}.favorites.index', ['name' => $currentUser]) }}">
                <i class="fa fa-star"></i>
                Favorites
            </a>
        </li>

        <li class="side-menu margin later @if (Route::is('user.{name}.watch-later.index')) active @endif">
            <a href="{{ route('user.{name}.watch-later.index', ['name' => $currentUser]) }}">
                <i class="fa fa-clock-o"></i>
                Watch later
            </a>
        </li>
        <li class="side-menu margin history @if (Route::is('user.{name}.history.show')) active @endif">
            <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'videos']) }}">
                <i class="fa fa-history"></i>
                History
            </a>
        </li>
    @endif
</ul>
