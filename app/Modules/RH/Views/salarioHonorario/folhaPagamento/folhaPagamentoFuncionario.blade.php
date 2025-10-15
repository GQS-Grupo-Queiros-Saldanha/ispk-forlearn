@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
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
        .fotoUserFunc{
            margin: 0px;
            padding: 0px;
            shape-outside: circle();
            clip-path: circle();
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #fff;
        }
        .modal-body span {
            font-size: 13px;
            color: black;
        }
    </style>
    <div class="content-panel">
        @include('RH::index_menu')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                      <h1>{{$action}}</h1> 
                    </div>
                    <div class="col-sm-6">
                      
                    </div>
                </div>
            </div>
        </div>
            
        <p class="btn-menu col-md-0 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                @include('RH::index_menuSalario')
                <div style="background-color: #f8f9fa" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div  class="criarCodigo ">
                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                            <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3 "></div>
                           
                           
                            <div class="col-md-12 align-items-end ">
                                <div class="float-right  d-flex flex-row-reverse bd-highlight">
                                    <div class="p-2 bd-highlight"><h5 class="text-muted text-uppercase"> Recibo de vencimentos</h5></div>
                                    {{-- <div class="pr-1 pl-1 pt-0 mt-0 bd-highlight"><button data-toggle="modal" data-type="processoSalario-geral" data-target="#processoSalario-geral" type="button" type="button" style="background: #2b9fc2 " class="p-2 pr-3 pl-3 btn btn-sm text-white"><i class="fa-solid fa-file-invoice"></i> <i class="fa-solid fa-g"></i></button></div> --}}
                                </div>
                            </div>
                            
                        <div class="col-md-12 row mb-4 pr-0">
                            <div class="mr-0 pr-0 col-md-8 border-bottom">
                                {{--formularios--}}
                                <form method="POST" action="{{ route('recurso-humanos.pesquisaFolha-salario', ['id'=>1]) }}" class="pb-4" target="_blank">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4">Funcionario/os</label>
                                            <select data-live-search="true"   class="selectpicker form-control"  id="funcionario-contrato" data-actions-box="true" data-selected-text-format="values" data-selected-text-format="count > 3" name="funcionario[]" tabindex="-98">

                                                @if (auth()->user()->hasAnyPermission(['user_colaborador']))
                                                
                                                    <option></option>
                                                    @foreach ($users as $element)
                                                        @if ($element->id==Auth::user()->id)
                                                            <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option> 
                                                        @endif
                                                    @endforeach
                                                @else
                                                <option></option>
                                                    @foreach ($users as $element)
                                                            @foreach ($userContrato as $item)
                                                                @if ($item->id_user==$element->id)
                                                                    <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option> 
                                                                @endif
                                                            @endforeach
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        {{-- <div hidden class="form-group col-md-12">
                                            <label class="m-0 p-0" for="inputPassword4">Contratado/a com o cargo</label>
                                            <select data-live-search="true" required   class="selectpicker form-control"  id="roles" data-actions-box="false" data-selected-text-format="values" name="roles" tabindex="-98">
                                                
                                            </select>
                                        </div>   --}}
                                        <div class="form-group col-md-12">
                                            <label class="m-0 p-0" for="inputPassword4">Referente a</label>
                                            <select  data-live-search="true" required autocomplete="" multiple=""   class="selectpicker form-control"  name="referencia[]"  id="referencia" data-actions-box="true" data-selected-text-format="count > 3" data-selected-text-format="values"  tabindex="-98">
                                                
                                            </select>
                                        </div>  
                                    </div>
                                    <div  class="form-row ml-0 mt-1 pl-0">
                                        <div class="form-group mr-3">
                                            <button  type="submit"  style="background: #2b9fc2"  class="btn text-white btn-gerarPDF"><i class="fas fa-receipt"></i> Gerar PDF</button>
                                        </div>                            
                                    </div>
                                </form> 
                            </div>
                            <div hidden class="col infor-user">
                                <div class="card m-0 p-0 col card-primary card-outline rounded">
                                    <div style="background: #f8f9fa" class="card-body m-0 p-0  box-profile rounded">
                                        <div style="margin-top: -1pc;background: #f8f9fa"  class="text-center mb-2  rounded">
                                            <center class="pb-2 pt-2">
                                                <div class="fotoUserFunc"></div>
                                            </center> 
                                            <h3 class="mt-1 pb-0 mb-0 profile-username text-center name-user"></h3>
                                            <p class="text-muted p-0 m-0 text-center">Estado do contrato: <strong class="user-contrato"></strong></p>
                                        </div>
                                        
                                        <ul style="background: #20c7f9" class="m-0 p-0 mb-5 list-group-unbordered  rounded">
                                            <li style="background: #20c7f9" class="list-group-item rounded">
                                                <b>Ultima data proc. salário</b> <a class="float-right text-white last-data-proce-salario"></a>
                                            </li>
                                            <li style="background: #20c7f9" class="list-group-item rounded">
                                                <b>Contactos:</b>  <table class="float-right text-white contatos-funcionario"><tr> <td class="telefone"></td> <td class="whatApp"></td></tr></table>
                                            </li>
                                            <li style="background: #20c7f9" class="list-group-item rounded">
                                                <b>Endereço</b> <a class="float-right text-white endereco"></a>
                                            </li>
                                            
                                        </ul>
                                        <a target="_blank" style="background: #2b9fc2" href="" class="btn  btn-block text-white mt-4 rounded link-perfil-fun"><b><i class="fa fa-eye"></i></b></a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@parent
<script>
    var getUsers= @json($users);
    var getUsers_Role=JSON.parse(JSON.stringify(getUsers))

    var getcontratos=@json($getcontratos);
    var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));

    var getSalariofuncionario=@json($getSalariofuncionario);
    var getSalario_fun=JSON.parse(JSON.stringify(getSalariofuncionario));
    var getIdUser=null;
    
    var salarioBase = $("#salarioBase");
    // var roles=$("#roles");
    var referencia=$("#referencia");

    // console.log(getcontratos_user)
    // console.log(getUsers_Role)
    // console.log(getSalario_fun)

    $("#funcionario-contrato").change(function () { 
        getIdUser= $("#funcionario-contrato").val()
        $(".link-perfil-fun").attr('href', '')
        let routePerfil_user=("{{ route('users.show','id_fun') }}").replace('id_fun', getIdUser);
        $(".link-perfil-fun").attr('href', routePerfil_user)
            
         

        referencia.empty();
        referencia.append('<option></option>')
        referencia.selectpicker('refresh');

        $(".name-user").text(); 
        var contrato=false; 
        if(getIdUser=="") {
            $(".infor-user").slideUp(900);
            $(".fotoUserFunc").attr('style','')
        } else {
            $(".infor-user").attr('hidden',false)
            $(".infor-user").slideUp(0);
            $(".infor-user").fadeIn(1380);
            $.each(getcontratos_user, function (key, item) { 
                if (item.id_user==getIdUser) {
                contrato=true
                }
            });
            if (contrato==true) {
                $(".user-contrato").text()  
                $(".user-contrato").text("activo")
            } else {
                $(".user-contrato").text()  
                $(".user-contrato").text("n/a")
            }
            // roles.empty();
            // roles.append('<option></option>')

            console.log(getUsers_Role);
            $.each(getUsers_Role, function (index, item) { 
                if (item.id==getIdUser) {
                    $(".name-user").text(item.name);  
                    $(".fotoUserFunc").attr('style',"background-image: url('//{{$_SERVER['HTTP_HOST']}}/users/avatar/"+item.fotografia+"')") 
                    // $.each(getcontratos_user, function (chave, value) { 
                    //     if (value.id_user == getIdUser) {
                    //         $.each(item.roles, function (key, element) { 
                    //             if (element.id == value.id_cargo) {
                    //                 roles.append('<option value="'+element.id+'">' +element['current_translation'].display_name+ '</option>')  
                    //             }
                    //         }); 
                    //     }
                    // }); 
                    // $(".telefone").text(item['parameters'][4]['pivot'].value)
                    // $(".whatApp").text(" / "+item['parameters'][5]['pivot'].value)
                    // $(".endereco").text(item['parameters'][6]['pivot'].value)
                    // $(".last-data-proce-salario").text()
                    
                    
                }
           });
        //    roles.selectpicker('refresh');




            // COLOCAR EM UMA FUNÇÃO SEPARADA
            salario_processado(getIdUser, getUsers_Role, getcontratos_user)
            
        }        

    });

    function salario_processado(getIdUser, getUsers_Role, getcontratos_user) {

        referencia.empty();
        referencia.append('<option></option>')
        var getIdRole_idFun=getIdUser
        $.ajax({
            url: 'recuso-humano-ajaxGetReciboSalario/'+getIdRole_idFun,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function(data)  {
            var response=data['data'];
            var cargo_existe = [];
            console.log(response)
            if (response.length>0) {
                console.log(response[0].year_month)
                $(".last-data-proce-salario").text(response[0].year_month)
            }
            $.each(getUsers_Role, function (index, item) { 
                if (item.id==getIdUser) {
                    
                    $.each(getcontratos_user, function (chave, value) { 
                        if (value.id_user == getIdUser) {

                            $.each(item.roles, function (key, element) { 

                                $.each(response, function (key, item1) {  
                                    if (element.id==item1.id_cargo) {
                                       var encontra = cargo_existe.find(a => a == item1.id_processam_sl)
                                        if (encontra == undefined) {
                                            cargo_existe.push(item1.id_processam_sl)
                                            referencia.append('<option value="'+item1.id_processam_sl+'">'+item1.year_month+' [nº recibo - 00'+item1.recibo_num+'] | '+element['current_translation'].display_name+'</option>')
                                        }
                                    }                   
                                });
                            }); 

                        }
                    });                         
                    
                }
            }); 
                    
            referencia.selectpicker('refresh');
        })

    }

    
</script>
@endsection
