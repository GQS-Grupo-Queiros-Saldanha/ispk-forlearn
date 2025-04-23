
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
            cursor: pointer;font-size: 15px;transition: all 0.5s; border-bottom: #dfdfdf 1px solid;
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
            height:50%

        }
        .btn-menu{
            border: none;
            background: none;
            outline:none;
            width: 0.8%
            
        }
        .btn-menu:hover{
             cursor: pointer;
        }

    </style>
    <div style="z-index: 1900" class="modal fade modal_loader" id="menu-load" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>
    <div class="divtable table-responsive nav flex-column nav-pills me-2 col-md-2 border-right ml-0 pl-0  mr-0  pr-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <ul class="list-group list-group-flush m-0 p-0">
            <li data-link="criarCategoria" class="list-group-item  m-0 p-0 pl-1  pt-3 pb-3"><button class="m-0 p-0 subMenu">Salários e honorários</button>
                <ul data-checado="false" id="subFolhaPay" style="display:none" class="m-0 p-0 pl-4 pt-2 ">
                    @if (auth()->user()->hasAnyPermission(['user_colaborador']))
                        <li  class="subLink mb-2"><a  href="{{ route('recurso-humano.folha-pagamento-funcionario') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-file-circle-plus"></i> Recibos de vencimentos</a></li>
                   @else
                    <li  class="subLink mb-2"><a href="{{route('recurso-humano.processamentoSalario') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-chart-line"></i> Processar salários</a></li>
                    <li  class="subLink mb-2"><a  href="{{ route('recurso-humano.folha-pagamento-mes') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-file-circle-plus"></i> Folha de salários</a></li>
                    <li  class="subLink mb-2"><a  href="{{ route('recurso-humano.folha-pagamento-funcionario') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-file-circle-plus"></i> Recibos de vencimentos</a></li>
                    @if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))
                        <li  class="subLink mb-2"><a href="{{route('recurso-humano.anular-pagamento-funcionario') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fa fa-receipt"></i> Anular recibo de vencimento</a></li>
                    @endif
                </ul>
            </li>
                @if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))
                    <li data-link="criarCodigo" class="list-group-item  m-0 pr-0 pl-1 pt-3 pb-3"><button
                            class="m-0 p-0 subMenu">Controle de presença</button>
                        <ul data-checado="false" id="subFolha" style="display:none" class="m-0 p-0 pl-4 pt-2 ">
                            <li  class="subLink mb-2"><a href="{{ route('recurso-humanos.controle-presenca') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fa-solid fa-calendar-days"></i> Controle de Ausência [Manual]</a></li>
                        @if (auth()->user()->hasRole(['superadmin']))
                            <li  class="subLink mb-2"><a href="{{ route('recurso-humanos.controle-presenca-catraca') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fa-regular fa-calendar-check"></i> Controle de Presença [Torniquete]</a></li>
                        @endif
                        </ul>
                    </li>
                @endif
                @if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))
                    <li  data-link="associarCodigo" class="list-group-item m-0 pr-0 pl-1 pt-3 pb-3"><button class="m-0 p-0 subMenu">Subsídios</button>
                        <ul data-checado="false" id="subSalario" style="display:none" class="m-0 p-0 pl-4 pt-2 ">
                            <li hidden class="subLink mb-2"><a href="#" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-money-bill"></i>Salário e Contrato</a></li>
                            <li  class="subLink mb-2"><a href="{{route('recurso-humano.add-subsidio-funcionario') }}" style="text-decoration: none" class="m-0 p-0 subPagina-link"><i class="fas fa-money-bill-trend-up"></i> Atribuir subsídio</a></li>
            
                        </ul>
                    </li> 
                @endif
            @endif
        </ul>
    </div>

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
