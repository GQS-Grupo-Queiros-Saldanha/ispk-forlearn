            
            @php
            use App\Modules\Cms\Controllers\MenusController; 
        @endphp
        @php
            $submenu = MenusController::fr_menu2($item_menu->id);
        @endphp
        
        
        @if(MenusController::verify_permission($item_menu->menu_item)==1)
                @if ($submenu["count"]==1)
                       <li class="menu-item ">
                            <a class="menu-link" href="{{ $item_menu->external_link }}" target="_blank">
                                 <span class="">{{ $item_menu->display_name }} </span>
                            </a>
                            <div class="bg-hover-nav"></div>
                        </li>          
                @else
                    
                <li class="menu-item dropdown-sub hidden-submenu @isset($item->grid) {{ $item->grid }} @endisset">
                          <a class="menu-link dropdown-toggle" href="#" role="button">
                            
                            <span class="">{{ $item_menu->display_name }}</span>
                          </a>
                          <div class="bg-hover-nav"></div>
                          
                          <ul class="dropdown-submenu">
                                @foreach($submenu["submenu"] as $item)
                                                
                                                @php
                                                    $submenu2 = MenusController::fr_menu3($item->parent);
                                                    
                                                @endphp
                                                
                                                 @foreach($submenu2["submenu"]  as $item2)
                                                
                                                         
                                                     @if(MenusController::verify_permission($item2->parent)==1) 
                                                            <li>
                                                                  <a class="dropdown-subitem" href="#" style ="color:#222;">
                                                                    <span>{{ $item2->display_name }}  </span>
                                                                  </a>
                                                                  
                                                                  <ul class="sub-inner-menu">
                                                                        @php
                                                                            $submenu3 = MenusController::fr_menu4($item2->parent);
                                                                        @endphp
                                                                        
                                                                         @foreach($submenu3["submenu"] as $item3) 
                                                                         
                                                                                 @if(MenusController::verify_permission($item3->parent)==1) 
                                                                                <li>
                                                                                  <a   target="_blank" href="{{'https://'.$_SERVER['HTTP_HOST'].'/'.$item3->external_link}}">
                                                                                    <sap class="link-signal">+</span> <span style ="color:#0082f2;">{{ $item3->display_name }} </span>
                                                                                  </a>
                                                                                      
                                                                                </li>  
                                                                                
                                                                                 @endif
                                                                         @endforeach 
                                                                        
                                                                  </ul>
                                                            </li>
                                                        
                                                        
                                                   @endif
        
                                                  @endforeach 
                                @endforeach 
                          </ul>
                          
                              
                    </li>
                @endif
        @else
            
        @endif







      