<nav role="navigation">
    <ul class="nav nav-list">
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
            <a href="{{ route('user.{name}.history.index', ['name' => 1]) }}">
                <i class="fa fa-history"></i>
                History
            </a>
        </li>
        @endif

        @if (false)
        <li class="divider"></li>

        <li class="nav-header"> Filters </li>
        <li>
            <a href="#">Overview <span class="sr-only">(current)</span></a>
        </li>
        <li><a href="#">Reports</a></li>
        <li><a href="#">Analytics</a></li>
        <li><a href="#">Export</a></li>
        @endif
    </ul>
</nav>
