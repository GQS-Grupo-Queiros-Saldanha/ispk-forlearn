<div class="top-bar @guest guest @endguest">
    <div class="logo">
        <i class="fas fa-bookmark"></i>
        <div>
            <span>FOR</span>


            @role('admin')
            <span>@lang('header.staff')</span>
            @elserole('superadmin')
            <span>@lang('header.staff')</span>
            @elserole('student')
            <span>@lang('header.students')</span>
            @elserole('teacher')
            <span>@lang('teacher')</span>
            @else
            <span>@lang('learn')</span>
            @endrole
        </div>
    </div>

   
<style>
    #notification_list{
        font-size:12pt;
        color: #333;
        box-shadow:none;
        width:270px;
        height:380px;
        border-radius:4px;
        overflow:auto;
    }
     #notification_list a{
        font-size:10pt;
        color: #333;
        background-color:red;
      
        
    }
    #notification_list li:hover{
   
        background-color:#eee;
        transition:.7s;
        cursor:pointer;
        
    }

    
    #vermais:hover{
        color:blue;
         transition:.5s;
        box-shadow:none;
       
        
    }
    .list-group{
         height:380px;
    }



</style>
@php
  $i=0;
@endphp
    @auth
        <div class="log-out dropdown">
            
             
            
                 <!--<a href="#" style="text-decoration: none;" href="#"  ><i class="fas fa-watch text-white ">Faltam :</i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  ><i class="fas fa-clock text-white "></i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  >dia(s): <i class="fas fa-watch text-white " id="days">00</i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  >hora(s): <i class="fas fa-watch text-white " id="hours">00</i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  >Minuto(s):<i class="fas fa-watch text-white " id="minutes">00</i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  >Segundo(s): <i class="fas fa-watch text-white " id="seconds">00</i>&emsp;</a>-->
                 <!--<a href="#" style="text-decoration: none;" href="#"  ><i class="fas fa-watch text-white " >|</i>&emsp;</a>-->
            
             
        <a href="#" style="text-decoration: none;" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fas fa-bell text-danger "><sub class="text-white">{{ count($count=count_notification())}}  </sub></i>&emsp;</a>
        
         <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left" id="notification_list"aria-labelledby="dropdownMenuLink">
             <div class="list-group-item active">Notificações</div>
              <ul class="list-group list-group-flush">
                  @php
                    $count=0;
                  @endphp

                    @forelse ($notifications=count_notification() as $item)  
                       @php
                          $count=$count+1;
                          $i=$i+1;
                       @endphp
                            <li class="list-group-item" onclick="abrirNotify(this)" data-id="{{$item->id}}"><i class="{{$item->icon}} mr-2"> </i>
                                  <b>{{$item->subject}}</b><br><hr>
                                  <p class="V_sms_{{$i}}">{{ nl2br (mb_strimwidth($item->body_messenge, 0, 59, ' ...')) }}</p>  
                            </li>
                       @if($count>=3)
                            @break
                         @endif
                    @empty
                      <li class="list-group-item"><i class="fas fa-bug mr-2"></i> Não foi encontrada nenhuma notificação</li>             
                   @endforelse  

                     <li class="list-group-item" id="vermais" >Ver todas notificações</li>
                 </ul>
            
              </div>

            <a href="{{ url('/logout') }}">LOG OUT<i class="fas fa-user-circle"></i></a>
        </div>
        
    @endauth
</div>

  @section('scripts')
  @parent
  
  <script>
  
        function abrirNotify(element){
                var id=element.getAttribute("data-id");
                var url = "{{ route('smsSingle', ['id' => ':id']) }}";//isso vai compilar o blade com o id sendo uma string ":id" e, no javascript, atribuir ela a uma variável .
                url = url.replace(":id",id);//isso vai corrigir a string gerada com o id correto.

                $(location).attr('href',url)
        }                  
        
        var btn = document.getElementById('vermais');
        btn.onclick = function() {
             window.location.href = "{{route('central-notification.index')}}";
         } 
         
        
     
  

   
        var count='{{$i}}';
            
        for (let index = 0; index<4;index++) {
                var texto=$(".V_sms_"+index).text();
                $(".V_sms_"+index).text("");
                $(".V_sms_"+index).html(texto)
                //depois tirar o espaço e incluir
                var format= $(".V_sms_"+index).text().replace(/\s/g,' ');
                $(".V_sms_"+index).html(format)
               
        }

    
   
</script>
@endsection


@auth
    <!-- Sidebar Menu -->
    <nav class="left-side-menu">
        <div class="user-account">
            <div class="user-image" style="background-image: url('{{ URL::to('/') }}/storage/attachment/{{ Auth::user()->image }}');">
                <button data-toggle="modal" type="button" data-type="update" data-target="#modal_type_image_avatar" class="btn forlearn-btn add">@icon('fas fa-edit')Editar</button>
            </div>
            <!-- Sidebar user panel (optional) -->
            @if(Auth::user())
                <a href="#" class="user-account"><span class="title-large">{{ Auth::user()->name ?? '' }}</span></a>
            @endif
        </div>

        @include('layouts.backoffice.menu')
    </nav>

    @include('layouts.backoffice.modal_delete_simple')
    @include('layouts.backoffice.modals.modal_image_avatar')
@endauth
