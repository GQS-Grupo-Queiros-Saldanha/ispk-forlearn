   <style>
    .content-panel {
        padding: 0px;
        background-color: #fff;
        margin-top: 16px;
        box-shadow: 0 0 5px rgb(0 0 0 / 25%);
        margin-bottom: 10px;
    }
    
    .li-nav{
        /* padding-top: 4px;
        padding-bottom: 4px; */
        font-size: 16px;
        text-transform: uppercase;
        font-family: Roboto Slab, serif;
        transition: all 0.5s;
        
    }
    .li-nav .nav-links:hover{
        background: white;
        color: #0060af;
        transition: all 0.5s;
        border-top-left-radius: 5px; 
        border-top-right-radius: 5px; 
    }
    .tirar{
        margin-left: 15px;
        margin-right: 43px;
        padding-left: 7px;
        /* padding-top: 4px;
        padding-bottom: 4px; */
    }
    .nav-links {
        display: block;
        padding: 0.8rem 3rem;
        padding-left: 0.7rem;
        padding-right: 0.7rem ;
        text-decoration: none;
        color: white;
        
    }
    .nav-links:hover{
        text-decoration: none;
    }

    .dt-buttons {
        float: left;
        margin-bottom: 20px;
    }

    .dataTables_filter label {
        float: right;
    }


    .dataTables_length label {
        margin-left: 10px;
    }
   </style>
 <div style="z-index: 1900" class="modal fade modal_loader" id="menu-load" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>


<div class="content-panel">
    <nav style="background-color: #0060af;box-shadow: none " class="navbar navbar-expand-lg navbar-light p-0">
            <a class="navbar-brand navbar-logo inicio-nav text-white" style="margin-left:10px;margin-right:10px;" href="{{route("main.index")}}"><i class="fas fa-home"></i>PAINEL INICIAL
    </a>
        <a style="text-transform: uppercase" class="navbar-brand tirar text-white" href="#"><i class="fas fa-bookmark"></i> Recursos humanos</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ">
                    @if (auth()->user()->hasAnyPermission(['user_colaborador']))
                        <li class="nav-item li-nav">
                            <a class="nav-links m-0" href="{{ route('recurso-humano.folha-pagamento-funcionario')}}"><i class="fa fa-file-text"></i> Salário e honorário</a>
                        </li>
                        
                    @elseif(auth()->user()->hasAnyPermission(['gestorRH']) || auth()->user()->hasRole(['coordenador-curso']) ||  auth()->user()->hasAnyPermission(['secretario_view_RH']))
                        <li class="nav-item  li-nav">
                            <a class="nav-links m-0" href="{{ route('recurso_humano.home') }}"><i class="fas fa-user-group"></i> Gestão de staff <span class="sr-only">(current)</span></a>
                        </li>
                        @if (!auth()->user()->hasRole(['coordenador-curso']) && auth()->user()->hasAnyPermission(['gestorRH']))
                            <li class="nav-item li-nav">
                                <a class="nav-links m-0" href="{{ route('recurso-humano.folha-pagamento-funcionario')}}"><i class="fa fa-file-text"></i> Salário e honorário</a>
                            </li>
                            @if (auth()->user()->hasAnyPermission(['gestorRH']))
                                <li class="nav-item li-nav">
                                    <a class="nav-links m-0" href="{{ route('recurso.funcao') }}"><i class="fas fa-cogs"></i></a>
                                </li>
                            @endif
                           
                            <li class="nav-item li-nav">
                                {{-- <a class="nav-links m-0" href="{{ route('config.recurso-humano-ajuda') }}"><i class="fa fa-question"></i></i> Ajuda</a> --}}
                            </li>
                            {{-- <li class="nav-item dropdown">
                                <a class="nav-link  dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Dropdown link
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </li> --}}
                        @endif
                    @endif
                </ul>
            </div>
    </nav> 

    
    <div class="modal fade" id="alertaRescisaoContrato" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg rounded mt-5" role="document">
        <div class="modal-content rounded" style="background-color: #e9ecef">
            <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLongTitle">Informação</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <p class="lead">Caro utilizador/a @php $nome=Auth::user()->name @endphp
                    {{$nome}}
                    o sistema detectou que aluns contrato /os de trabalho chegou na sua data finial.</p>
                <hr class="my-4">
                <p>Após a formalização deste fim vínculo, as partes envolvidas não estão mais submetidas aos direitos e deveres de uma relação laboral.
                    Para poder reativar o vínculo, por favor click no botão a baixo.
                </p>
                
                <a href="{{ route('recurso_humano.contratoTrabalho') }}" style="border-radius: 6px; background: #20c7f9" type="submit" class="btn btn-lg text-white mt-2">Criar</a>
            </div>
        </div>
        </div>
    </div>
</div>
@section('scripts')
@parent
    <script>
        var cookies = document.cookie;
        var nova = cookies.split(";");
        if (nova[0] == "tela=cheia") {

            $(".left-side-menu,.top-bar").hide();
            $(".btn-logout").show();

            $(".content-wrapper").css({
                margin: '0 auto',
                marginTop: '0px',
                position: 'absolute',
                left: '0',
                top: '0',
                padding: '0',
                width: '100%'
            });

            $(".content-panel").css({
                marginTop: '0px'
            });
        }

        $(".tirar").click(function() {

            var cookies = document.cookie;

            var nova = cookies.split(";");

            if (nova[0] == "tela=cheia") {


                $(".left-side-menu,.top-bar").show();
                $(".btn-logout").hide();
                $(".content-wrapper").css({
                    // margin: '0 auto',
                    // marginTop: '0px',  
                    position: 'absolute',
                    left: '370px',
                    top: '84px',
                    padding: '20px',
                    width: 'calc(100% - 370px)'
                });

                $(".content-panel").css({
                    marginTop: '14px'
                });


                document.cookie = "tela=normal";

            } else if (nova[0] == "tela=normal") {

                $(".btn-logout").show();
                $(".left-side-menu,.top-bar").hide();

                $(".content-wrapper").css({
                    margin: '0 auto',
                    marginTop: '0px',
                    position: 'absolute',
                    left: '0',
                    top: '0',
                    padding: '0',
                    width: '100%'
                });

                $(".content-panel").css({
                    marginTop: '0px'
                });
                document.cookie = "tela=cheia";

            } else {
                document.cookie = "tela=cheia";
            }

        });
            
        var data= new Date()
        var month=data.getMonth()+1
        var dataAtual=data.getFullYear()+'-'+month+'-'+data.getDate();
        getRescisaoAutomatic(dataAtual)
        function getRescisaoAutomatic(dataAtual) {  
            
            const getData=JSON.parse(localStorage.getItem('dataAtual')) || [];
            if (getData==dataAtual) {
            }
            else {
                $.ajax({
                    url: 'recurso_humanoRescisaoContratoAutomatico/'+dataAtual,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(response)  {
                    console.log(response)
                    localStorage.setItem('dataAtual',JSON.stringify(dataAtual));
                    // $("#alertaRescisaoContrato").modal('show')
                })
            }
        }
        $(".subPagina-link").click(function (e) { 
            $("#menu-load").modal('show')
            
        });
    </script>
@endsection
