@if (!auth()->user()->hasAnyPermission(['user-colabotador']))
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button{
            border: none; background: none; outline-style: none;transition: all 0.5s;
        }
        .list-group li button:hover{cursor: pointer;font-size: 15px;transition: all 0.5s; font-weight: bold }
        .subLink{
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }
        .subLink:hover{
            font-size: 15px;transition: all 0.5s; border-bottom: #dfdfdf 1px solid;
        }

        .divtable::-webkit-scrollbar {
            width: 7px;            
            height: 2px;  
            border-radius: 30px;
            box-shadow: inset 20px 20px 60px #bebebe,
            inset -20px -20px 60px #ffffff;            
        }
        .divtable::-webkit-scrollbar-track {
            background: #e0e0e0;   
            box-shadow: inset 20px 20px 60px #bebebe,
            inset -20px -20px 60px #ffffff; 
            border-radius: 30px; 
            height: 2px
           
        }
        .divtable::-webkit-scrollbar-thumb {
            background-color: #343a40;   
            border-radius: 30px;       
            border: none; 
            height: 2px
        }
        .divtable{
            height:auto

        }
        .btn-menu{
            border: none;
            background: none;
            outline:none;  
            width: 0.8%;
        }
        .btn-menu:hover{
             cursor: pointer;
        }
    </style>
    
        <div class="divtable table-responsive nav flex-column nav-pills me-2 col-md-2 border-right ml-0 pl-0  mr-0  pr-0" id="v-pills-tab"role="tablist" aria-orientation="vertical">
            
           <ul class="list-group list-group-flush m-0 p-0">
                <li data-link="criarCategoria" class="list-group-item  m-0 p-0 pl-1  pt-3 pb-3"><button  class="m-0 p-0 subMenu">Staff</button>
                    <ul data-checado="false" id="subFolhaPay" style="display:none" class="m-0 p-0 pl-4 pt-2 ">                                                
                        @if(auth()->user()->hasRole(['coordenador-curso']) || auth()->user()->hasAnyPermission(['gestorRH']))
                            <li  class="subLink mb-2"><a href="/users/getDocente" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-person-chalkboard"></i> Docentes</a></li>
                        @endif
                        @if(auth()->user()->hasAnyPermission(['gestorRH']) && !auth()->user()->hasRole(['coordenador-curso']))
                            <li  class="subLink mb-2"><a  href="{{ route('add_funcionario') }}"  style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-user-plus "></i> Staff</a></li>
                            <li  class="subLink mb-2"><a href="{{ route('recurso_humano.gestaoStaff') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-l"></i> Lista de acesso a plataforma</a></li>
                            <li  class="subLink mb-2"><a href="{{ route('recurso_humano.home') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-user-group"></i> Listagem de funcionários</a></li>
                            {{-- <li  class="subLink mb-2"><a href="{{ route('add_colaborador') }}" style="text-decoration: none" class="m-0 p-0"><del><i class="fas fa-user-tie"></i> Colaboradores</del></a></li> --}}
                        @endif
                    </ul>
                </li>
                @if(auth()->user()->hasAnyPermission(['gestorRH']) && !auth()->user()->hasRole(['coordenador-curso']) && !auth()->user()->hasAnyPermission(['secretario_view_RH'])) 
                    <li data-link="criarCodigo" class="list-group-item  m-0 pr-0 pl-1 pt-3 pb-3"><button class="m-0 p-0 subMenu">Contrato</button>
                        <ul data-checado="false" id="subFolha" style="display:none" class="m-0 p-0 pl-4 pt-2 ">
                            <li  class="subLink mb-2"><a href="{{ route('recurso_humano.contratoTrabalho') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-handshake-simple"></i> Contrato de Trabalho</a></li>
                            <li  class="subLink mb-2"><a href="{{ route('recurso_humano.rescisoses') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-user-large-slash"></i> Rescisões</a></li>
                        </ul>
                    </li>
                    <li hidden data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><button class="m-0 p-0 subMenu">Conta Corrente</button>
                        <ul data-checado="false" id="subDetalhe" style="display:none" class="m-0 p-0 pl-4 pt-2 ">
                            <li  class="subLink mb-2"><a href="#" style="text-decoration:none;" class="m-0 p-0 subPagina-link">Valores em dívida</a></li>
                            <li  class="subLink mb-2"><a href="#" style="text-decoration:none;" class="m-0 p-0 subPagina-link">Valor pagos</a></li>
                            <li  class="subLink mb-2"><a href="#" style="text-decoration:none;" class="m-0 p-0 subPagina-link">Facturações</a></li>
                        </ul>
                    </li>
                    <li class="list-group-item  m-0 pr-0 pl-1 pt-3 pb-3">                        
                        <a href="{{ route('recurso-humano.user-banco') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link">
                           Associar banco ao funcionário
                        </a>                        
                    </li>
                @endif
            </ul>
        </div>
@endif
@section('scripts')
@parent
    <script>
        var menu=true;
        $(".subMenu").click(function (e) { 
            var getThis=$(this).parent().eq(0);
            var getUl=getThis.children().eq(1)[0].id;
            var getVisivel=$("#"+getUl+"").attr('data-checado');
            if(getVisivel == "false"){
                $("#"+getUl+"").css('display','block'); 
                $("#"+getUl+"").attr('data-checado','true');
            }else{
            $("#"+getUl+"").css('display','none'); 
            $("#"+getUl+"").attr('data-checado','false');
            } 
        });
        $(".btn-menu").click(function (e) { 
            console.log(menu)
            if (menu==true) {
                menu=false
                $(".divtable").attr('hidden',true)
            } else {
                menu=true
                $(".divtable").attr('hidden',false)
                
            } 
        });
    </script>
@endsection