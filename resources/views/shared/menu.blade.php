<ul class="nav nav-pills">
    @foreach($menu as $item)
        <li class="nav-item">
            <a class="nav-link @if($item->active()) active @endif" aria-current="page" href="{{ $item->url() }}">
                {{ $item->title() }}
            </a>
        </li>
    @endforeach
</ul>
