<div class="row">
    <div class="col-md-12">
        <ul class="tabs">
            {{-- URL is /history/videos => active --}}
            <li class="tab">
                <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'videos']) }}">
                    Watched videos
                </a>
            </li>
            <li class="tab">
                <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'search']) }}">
                    Search terms
                </a>
            </li>
        </ul>
    </div>
</div>
