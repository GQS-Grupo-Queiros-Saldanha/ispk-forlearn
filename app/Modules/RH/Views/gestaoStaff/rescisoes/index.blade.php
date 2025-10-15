@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

<style>
    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }
    .fotoUserFunc {
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
</style>

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>

 


<!-- CRIAR -->
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
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Rescisão do contrato</h5>
                            
                            <div class="col-12 mb-4 border-bottom">
                                <form method="POST" action="{{ route('recurso-humanos.create-rescisao') }}" enctype="multipart/form-data" accept-charset="UTF-8"  class="pb-4">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Nome do funcionário</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" name="funcionario" id="funcionario-contrato" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
                                                <option  selected></option>
                                                @php $getUser=[]; @endphp
                                                @foreach ($users as $element)
                                                    @foreach ($getcontratos as $item)
                                                        @if ($element->id==$item->id_user && !in_array($element->id,$getUser)) 
                                                        @php $getUser[]=$item->id_user @endphp
                                                            <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option>    
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword5">Cargo</label>
                                            <select required name="roles"  id="roles" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                                {{--  --}}
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="custom-file-upload mb-2">
                                                <div class="file-upload-wrapper">
                                                    <input type="file" class="attachment custom-file-upload-hidden" id="arquivo" name="arquivo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                                    
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlTextarea1">Nota</label>
                                                <textarea class="form-control" id="nota" name="nota" rows="3"></textarea>
                                              </div>
                                            <div>
                                                <button hidden  data-toggle="modal" data-target="#alertaRescisoes" type="button" class="btn btn-primary btn-rescisao"><i class="fas fa-user-large-slash" aria-hidden="true"></i> Realizar rescisão do contrato de trabalho</button>  
                                            </div>                                            
                                        </div>
                                        <div class="form-group col-md-6">
                                            <p class="text-muted">Infor. do contrato</p>
                                            <div class="row col-md-12 m-0 p-0 border-top">
                                                <div style="display: flex; align-items: center;"  class="col-md-7 border-right">
                                                    <table>
                                                        <thead>
                                                            <tr><td><i class="fa fa-calendar-xmark"></i> Salário base: <small style="font-size: 1.1pc" ><b id="salarioBase"></b></small><small>Kz</small></td></tr>
                                                            <tr><td><i class="fa fa-u"></i> Ultimo vencimento: <samp><b class="last-data-proce-salario"></b></samp>  </td></tr>
                                                            <tr><td><i class="fa fa-calendar-check"></i> Data início do contrato: <samp><b class="data-inicio-contrato"></b></samp>  </td></tr>
                                                            <tr><td><i class="fa fa-calendar-xmark"></i> Data fim do contrato: <samp><b class="data-fim-contrato"></b></samp>  </td></tr>
                                                            <tr><td><i class="fa fa-user"></i> Criado por: <small  class="criado-por text-muted"></small>  </td></tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <div class="col">
                                                    <div  class="text-center">
                                                        <center class="pb-2 pt-2">
                                                            <div class="fotoUserFunc" ></div>
                                                        </center> 
                                                        <h4 class="mt-1 pb-0 mb-0 profile-username text-center name-user"></h4>
                                                        {{-- <p class="text-muted p-0 m-0 text-center">Estado do contrato: <strong class="user-contrato">Activo</strong></p> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <!-- Modal  alerta rescisoes-->
                                    <div class="modal fade" id="alertaRescisoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-lg rounded mt-5" role="document">
                                        <div class="modal-content rounded" style="background-color: #e9ecef">
                                            <div class="modal-header">
                                            <h3 class="modal-title" id="exampleModalLongTitle">Informação</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="lead">Caro utilizador/a @php Auth::user()->name @endphp pretende formalizar o fim do vínculo com o empregador. Ou seja, o fim da relação de trabalho entre empregado e empregador.</p>
                                                <hr class="my-4">
                                                <p>Após a formalização deste fim de vínculo, as partes envolvidas não estão mais submetidas aos direitos e deveres de uma relação laboral.</p>
                                                
                                                <button style="border-radius: 6px; background: #20c7f9" type="submit" class="btn btn-lg text-white mt-2">Submeter</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            {{-- tabela que lista os funcionario que sofrerão rescisão do contrato de trabalho --}}
                            <div class="container-fluid ml-2  mr-2 mt-3">
                                <table id="rescisao-contrato"  class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Funcionário</th>
                                            <th>Email</th>
                                            <th>Acções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                           
                            {{-- modal Funcionário com o cargo rescindido --}}
                            <div  class="modal fade" id="funCargoRescindido" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content rounded">
                                    <div style="background:#20c7f9;width: 100%;border-top-left-radius: 2px;border-top-right-radius: 2px;height: 6px;" class="m-0"></div>
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Funcionário com o cargo rescindido</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{-- tabela que lista os cargos que foram rescindido o funcionario --}}
                                        <div class="container-fluid ml-2  mr-2 mt-3">
                                            <table id="cargo-rescisao-contrato"
                                                class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cargo</th>
                                                        <th>Data inicio do contrato</th>
                                                        <th>Data fim do contrato</th>
                                                        <th>Contrato criado por</th>
                                                        <th>Contrato rescindido por</th>
                                                        <th>Documento</th>
                                                        <th>Rescindido ao</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br><br><br><br>
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
     // variaves
        var getUsers= @json($users);
        var getUsers_Role=JSON.parse(JSON.stringify(getUsers))
        
        var getfuncoesFuncionario=@json($getfuncaoUsers); 
        var getfuncoes_Funcionario=JSON.parse(JSON.stringify(getfuncoesFuncionario));
        
        var getcontratos=@json($getcontratos);
        var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));

        var getSalariofuncionario=@json($getSalariofuncionario);
        var getSalario_fun=JSON.parse(JSON.stringify(getSalariofuncionario));
        var getIdUser=null;
        var getVetorCargo=[];
        var salarioBase=$("#salarioBase");
        var roles=$("#roles")
    // variaves
    // console.log(getcontratos_user)
    $("#funcionario-contrato").change(function () { 
        getIdUser= $("#funcionario-contrato").val()
        if(getIdUser.length==0) {
            roles.empty();
            roles.append('<option ></option>')
            roles.selectpicker('refresh');
        } else{
            roles.empty();
            roles.append('<option ></option>')
            $.each(getUsers_Role, function (index, item) { 
                if (item.id==getIdUser) { 
                    $(".name-user").text(item.name);  
                    $(".fotoUserFunc").attr('style',"background-image: url('//{{$_SERVER['HTTP_HOST']}}/users/avatar/"+item.fotografia+"')") 
                    $.each(getcontratos_user, function (chave, value) { 
                        if (value.id_user == getIdUser) {
                            $.each(item.roles, function (key, element) {
                                if (element.id == value.id_cargo) {
                                    roles.append('<option value="'+element.id+'">' +element['current_translation'].display_name+ '</option>')                                    
                                }
                            }); 
                        }
                    }); 
                    
                }
            });
            roles.selectpicker('refresh');
            $(".btn-rescisao").attr('hidden',true)

        }
    });

    $("#roles").change(function (e) { 
        var getId_role=$("#roles").val()
        var pesquiLastSalario=false;
        var getIdRole_idFun=getId_role+','+getIdUser
         $.each(getSalario_fun, function (index, item) { 
             if (item.id_user==getIdUser && pesquiLastSalario==false && getId_role==item.id_cargo) {
                 pesquiLastSalario=true;
                 salarioBase.text(item.salarioBase.toLocaleString('pt-br', {minimumFractionDigits: 2}))
             }
        });
        $.each(getcontratos_user, function (chave, value) { 
                if (value.id_user == getIdUser && value.id_cargo == getId_role ) {
                    $(".data-inicio-contrato").text(value.data_inicio_conrato)
                    $(".data-fim-contrato").text(value.data_fim_contrato)
                    $('.criado-por').text(value.value)
                }
        }); 
        if (pesquiLastSalario==true) {
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
                if (response.length>0) {
                    $(".last-data-proce-salario").text(response[0].year_month)
                }else{
                    $(".last-data-proce-salario").text('')
                }
            })
            
        }
        $(".btn-rescisao").attr('hidden',false)
    });

    $('#rescisao-contrato').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('recurso.ajaxRescisao-contrato') !!}',
            columns: [
                {
                    data: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false
                },{
                    data: 'nome_funcionario',
                    name: 'full.value'
                },{
                    data: 'email',
                    name: 'user.email'
                },{
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                } 
            ],
            "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                ],
            language: {
                url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
            },
    });
</script>
@endsection
