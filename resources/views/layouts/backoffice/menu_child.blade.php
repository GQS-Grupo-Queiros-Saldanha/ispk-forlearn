
<li class="nav-item {{ Request::is('*'.$item->code.'*') ? 'active' : '' }}">
    <a href="{{ $item->external_link }}"
       class="nav-link {{ Request::is('*'.$item->code.'*') ? 'active' : '' }}">
        <p>{{ $item->translation->display_name }}</p>
    </a>
</li>
