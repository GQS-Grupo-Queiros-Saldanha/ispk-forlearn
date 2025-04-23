<nav class="navegation-menu">
        <ul class="menu-nav">
               <li class="menu-item dash">
                <a class="menu-link " href="#">
                     <span class="">Dashboard</span>
                </a>
                <div class="bg-hover-nav"></div>
                </li>
                 <li class="menu-item active">
                <a class="menu-link" href="{{route('main.index')}}" target="_blank">
                     <span class="">Painel inicial</span>
                </a>
                <div class="bg-hover-nav"></div>
                </li>
            
            
            @php
                use App\Helpers\LanguageHelper;
                use App\Modules\Cms\Models\Menu;
                use App\Modules\Cms\Controllers\MenusController; 

                $menus = MenusController::fr_Menu();
                
                $user = Auth::user();
                
 
                
                
            @endphp
     
             @if(count($menus) > 0)
                @foreach($menus as $item)
                     @include('layouts.backoffice_new.menu_children', ['item_menu' => $item])
                @endforeach 
            @endif

        </ul>
    </nav>