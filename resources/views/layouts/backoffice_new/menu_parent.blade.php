<li class="nav-item has-treeview {{ Request::is('*'.$item->code.'*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ false ? 'active' : '' }}">
        <i class="fas fa-caret-right"></i>
        <p>{{ $item->translation->display_name }}</p>
    </a>
    <ul class="nav nav-treeview" style="display: {{ Request::is('*'.$item->code.'*') ? 'block' : 'none' }};">
        @foreach($item->children as $child)
            @if($user->hasAllPermissions($child->permissions->pluck('id')->toArray()))
                @if(count($child->children) <= 0)
                    @include('layouts.backoffice.menu_child', ['item' => $child])
                @else
                    @include('layouts.backoffice.menu_parent', ['item' => $child, 'user' => $user])
                @endif
            @endif
        @endforeach
    </ul>
</li>
