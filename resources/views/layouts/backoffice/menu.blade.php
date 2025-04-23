<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <input type="text" class="form-control" id="search" onkeyup="searchMenu()">
    </li>
    <li class="nav-item">
        <ul id="searchable-list" class="nav nav-treeview" data-widget="tree">

            @php
                use App\Helpers\LanguageHelper;
                use App\Modules\Cms\Models\Menu;

                $menus = Menu::with(['items'])->orderBy('order')->get();
                $user = Auth::user();
            @endphp

            @foreach($menus as $menu)
                @if(count($menu->items) > 0)

                    @php $items = App\Modules\Cms\Models\MenuItem::tree($menu->id); @endphp

                    @if(count($items) > 0)
                        @foreach($items as $item)
                            @if($user->hasAllPermissions($item->permissions->pluck('id')->toArray()))
                                @if(count($item->children) <= 0)
                                    @include('layouts.backoffice.menu_child', ['item' => $item])
                                @else
                                    @include('layouts.backoffice.menu_parent', ['item' => $item, 'user' => $user])
                                @endif
                            @endif
                        @endforeach
                    @endif

                @endif
            @endforeach
        </ul>
    </li>
</ul>

<script>
    function searchMenu() {

        // Declare variables
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('search');
        filter = input.value.toUpperCase();
        ul = document.getElementById("searchable-list");
        li = ul.getElementsByTagName('li');

        // Loop through all list items, and hide those who don't match the search query
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";

                var parentUl = li[i].parentElement;
                var parentLi = parentUl.parentElement;

                parentUl.style.display = "block";
                parentLi.style.display = "block";
                parentLi.classList.add('menu-open');

            } else {
                li[i].style.display = "none";
            }
        }
    }
</script>
