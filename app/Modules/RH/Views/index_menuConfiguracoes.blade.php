
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        /* .list-group li button{
            border: none; background: none; outline-style: none;transition: all 0.5s;
        } */
        .list-group li{
           background: none;transition: all 0.7s;
        }
        .list-group li:hover{
           background: #e5f3f9;transition: all 0.5s;
        }
        .list-group li a:hover{cursor: pointer;font-size: 15px;transition: all 0.5s; font-weight: bold }
        /* .subLink{
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }
        .subLink:hover{
            cursor: pointer;font-size: 15px;transition: all 0.5s; border-bottom: #dfdfdf 1px solid;
        } */

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
            height:50%

        }
        .btn-menu{
            border: none;
            background: none;
            outline:none;  
        }
        .btn-menu:hover{
             cursor: pointer;
        }

    </style>
    <div class="divtable table-responsive nav flex-column nav-pills me-2 col-md-2 border-right ml-0 pl-0  mr-0  pr-0" id="v-pills-tab"role="tablist" aria-orientation="vertical">
        <ul class="list-group list-group-flush m-0 p-0">            
            <li data-link="criarCategoria" class="list-group-item  m-0 p-0 pl-1  pt-3 pb-3"><a href="{{ route('recurso-humano.create-banco') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-b"></i> Bancos</a></li>
            <li data-link="criarCategoria" class="list-group-item  m-0 p-0 pl-1  pt-3 pb-3"><a href="{{ route('recurso.funcao') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-f"></i> Funções</a></li>
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="/users/professions" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-user-tie"></i> Profissões</a></li>
            <li data-link="criarDepartamento" class="list-group-item  m-0 pr-0 pl-1 pt-3 pb-3"><a href="/users/roles" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-c"></i> Cargos</a></li>
            <li data-link="criarCodigo" class="list-group-item  m-0 pr-0 pl-1 pt-3 pb-3"><a href="/gestao-academica/courses/departments" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-building-user"></i> Departamentos</a></li>
            {{-- <li data-link="criarCategoria" class="list-group-item  m-0 p-0 pl-1  pt-3 pb-3"><a href="/gestao-academica/events" style="text-decoration:none;" class="m-0 p-0"><i class="fa fa-e"></i> Eventos</a></li> --}}
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="/users/permissions" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-person-circle-check"></i>   Permissões</a></li>
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="{{ route('config.recurso_humanoImposto') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-building-columns"></i> Impostos</a></li>
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="{{ route('config.recurso_humanoSubsidio') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-money-bill-trend-up"></i> Subsídios</a></li>
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="{{ route('config.create_horas_laroral') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-calendar"></i> Horário laboral</a></li>
            <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="/users/professional-states" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-id-card"></i> Situações Profissionais</a></li>
            {{-- <li data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><a href="{{ route('config.recurso-humano-ajuda') }}" style="text-decoration:none;" class="m-0 p-0 subPagina-link"><i class="fa fa-question"></i> Ajuda</a></li> --}}
        </ul>
    </div>
 
    
@section('scripts')
@parent
    <script>
        var menu=true
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