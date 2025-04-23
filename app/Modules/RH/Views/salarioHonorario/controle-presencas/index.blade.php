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

    .tamanho {
        width: 10%;
    }
    #checked label{
        font-size: 14px;
        color: black;
        font-weight: 900;
    }
</style>

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>

<!-- Modal que elimina a taxa   -->
<div class="modal fade" id="delete_horasLaboral" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar esta?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <form id="formRoute_delete-horasLaboral" method="POST" action="">
            @csrf
              <input type="hidden" name="getId" id="getId">
            <button type="submit" class="btn btn-primary">Ok</button>
          </form>
        </div>
      </div>
    </div>
</div>

<!-- Modal para editar imposto  -->
<div class="modal fade" id="editar_horasLaboral" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Editar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">

                    <form id="formRoute-Edita-horasLaboral" method="POST" enctype="multipart/form-data" accept-charset="UTF-8" action="" class="pb-4">
                        @csrf
                        <div hidden id="editarhorasLaboral">

                            <div class="form-row">
                                <div hidden class="form-group col-md-4">
                                    <label for="inputPassword4">Presença</label>
                                    <input required type="text" class="form-control" name="id_presence" id="id_presence" >
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="inputPassword4">Data</label>
                                    <input required type="date" class="form-control" name="data" id="data" >
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="inputPassword4">Entrada oficial</label>
                                    <input required type="time" class="form-control" name="entrada" id="entrada" >
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="inputPassword4">Entrada real</label>
                                    <input required type="time" class="form-control" name="saida"  id="saida" >
                                </div>
                            </div>

                            <div class="form-row"> 
                                <div class="form-group col-md-6" hidden>
                                    <label for="inputPassword4">Funcionário id</label>
                                    <input type="number" class="form-control tamanho" name="funcionario_id"  id="funcionario_id" >
                                </div>

                                <div class="form-group col-md-6">                                    
                                    <label for="inputPassword4">Justificar falta</label>
                                    <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="falta" data-actions-box="false" data-selected-text-format="values" name="falta" tabindex="-98">                                        
                                        <option value="Não justificada">Não Justificar</option>
                                        <option value="Justificada">Justificar</option>
                                    </select>
                                </div>
                                
                                <div class="form-group col custom-file-upload mb-2">
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="attachment custom-file-upload-hidden" id="arquivo" name="arquivo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                        
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Gravar</button>
                        </div>
                    </form>

                </div>               
            </div>
        </div>
       
      </div>
    </div>
</div>



<div class="content-panel">
    @include('RH::index_menu')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>
                        {{-- {{$action}} --}}
                    </h1>
                </div>
                <div class="col-sm-6">
                    
                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuSalario')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <h6 class="col-md-12 mb-4 text-right text-muted text-uppercase">Controle de ausência [ Manual ]</h6>
                        <div class="container-fluid ml-2 mr-2 mt-3" style="padding-left: 2.5%">
                            <form method="POST" action="{{ route('config.store_controlePresenca')}}" class="mb-3 pb-1 border-bottom">
                                @csrf

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Funcionario</label>
                                        <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="funcionario" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
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
                                        <label for="inputEmail4">Contrato</label>
                                        <select name="funcionario_contrato"  id="funcionario_contrato" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                            {{--  --}}
                                        </select>                                                                               
                                    </div>                                    
                                </div>

                                <div class="form-row">                                  

                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Data</label>
                                        <input required type="date" class="form-control" name="data" id="data" >
                                    </div>

                                    <div class="form-group col-md-4" id="div-saida">    
                                    </div>
                                    <div class="form-group col-md-4" id="div-entrada">
                                    </div>
                                    <br>


                                  
                                    <br>

                                </div>

                                <div id="checked">
                                    <label for="check">Ausência diária</label>
                                    <input  type="checkbox" class="" name="check" id="check">
                                </div>
                                <br>
                                <div class="form-group  col-md-2 pl-0 pb-0 mb-0">
                                    <button type="submit" type="button"   style="background: #2b9fc2 "  class="btn text-white">Gravar</button>
                                </div>
                            </form>
                        </div>

                        <div class="container-fluid ml-2 mr-2 mt-3">
                            <table hidden id="horas_trabalho"  class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Funcionário</th>
                                        <th>Contrato</th>
                                        <th>Data</th>
                                        <th>Hora - entrada</th>
                                        <th>Hora - atraso</th>                      
                                        <th>Justificada</th>                                        
                                        <th>Documento</th>                                        
                                        <th>Criado por</th>
                                        <th>Criado aos</th>
                                        <th>Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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


    $(document).ready(function() {
        var getusers_role=@json($users);                
        var getcontratos=@json($getcontratos);
        var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));
        var roles = $("#funcionario_contrato");
        let entrada ="";
        let saida ="";
        // var table_presence = $('#horas_trabalho').DataTable();

        
        $("#funcionario").change(function() {  
            $("#div-entrada,#div-saida").html('');
                             
            var id_user = $("#funcionario").val();                           
            roles.empty();
            roles.append('<option selected></option>')
            $.each(getusers_role, function (key, item) { 
                if (item.id==id_user) {
                    $.each(item.roles, function (index, element) { 

                        $.each(getcontratos_user, function (chave, value) { 
                             if (value.id_user==id_user && element.id==value.id_cargo) {
                                roles.append('<option value="'+element.id+'">'+element.current_translation.display_name+'</option>')         
                             }
                        });                        
                    });
                }
            }); 
            roles.selectpicker('refresh');

            if (id_user.length === 0){
                
                $("#horas_trabalho").attr('hidden',true)
                // config.ajaxcontrolePresenca
                            
                $('#horas_trabalho').clear().draw();

            } 
            else{

                $("#horas_trabalho").attr('hidden',false)

                $('#horas_trabalho').DataTable({
                    processing:true,
                    serverSide:true,
                    destroy: true,
                    ajax: 'ajax-controlePresenca/'+id_user,
                    columns: [
                        {
                            data: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false
                        }, 
                        {
                            data: 'fullName',
                            name: 'fullName'
                        }, {
                            data: 'contrato',
                            name: 'contrato'
                        }, {
                            data: 'data',
                            name: 'data'
                        }, {
                            data: 'saida',
                            name: 'saida'
                        }, {
                            data: 'time',
                            name: 'time'
                        }, {
                            data: 'falta',
                            name: 'falta'
                        },
                        {
                            data: 'arquivo',
                            name: 'arquivo'
                        },                       
                        {
                            data: 'created_by',
                            name: 'created_by'
                        }, {
                            data: 'created_at', 
                            name: 'created_a'
                        }, {
                            data: 'actions', 
                            name: 'actions'
                        } 
                    ],
                    columnDefs: [{
                        targets: [8,5],
                            render: function ( data, type, row,meta ) {
                                if (meta.col==5) {
                                    if (data[0] > 0) {
                                        return data[0]+'h:'+data[1]+'m';
                                    }
                                    else {
                                        return data[1]+'m';
                                    }
                                }
                                if (meta.col==8) {
                                    if (data !=null){
                                        return '<a href="recurso_rescisaoBaixando_arquivos/'+data+'"  class="btn btn-sm btn-info link-arquivo"><i class="fas fa-file-upload" aria-hidden="true"></i></a>';            
                                    }else{
                                        return '<p>N/A</p>';
                                    }
                                }
                                
                                
                            }
                    }],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    },
                });

                // Delete confirmation modal
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
                

                
            }
            roles.change(function () {
                var contrato = $(this).val();
                $.ajax({
                        url: "/RH/ajax-horas-laboral-contrato/"+$("#funcionario").val()+","+contrato,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        }).done( function (data)  
                        {   
                            entrada = data[0]["entrada"];
                            saida = data[0]["saida"];
                            $("#div-entrada").html('<label for="inputPassword4" hidden>Hora Laboral<input required="" type="time" class="form-control" name="entrada" id="entrada" value="'+data[0]["entrada"]+'" min="'+data[0]["entrada"]+'" max="'+data[0]["saida"]+'" readonly></label>');
                            $("#div-saida").html('<label for="inputpassword4" >Hora entrada<input required="" type="time" value="" class="form-control" name="saida" id="saida" min="'+data[0]["entrada"]+'" max="'+data[0]["saida"]+'"  ></label>');
                            
                            
                           
                        });
            });

            $("#check").change(function () {
                var isChecked = $("#check").prop("checked");
                    if (isChecked) {
                        $("#div-saida").html('<label for="inputpassword4" hidden >Hora de entrada<input required="" type="time" class="form-control" value="'+saida+'" name="saida" id="saida" min="'+entrada+'" max="'+saida+'"  ></label>');
                    } else {
                        $("#div-saida").html('<label for="inputpassword4" >Hora de entrada<input required="" type="time" value="" class="form-control" name="saida" id="saida" min="'+entrada+'" max="'+saida+'"  ></label>');
                    }
            });
            

        });
    });





    
   

</script>
@endsection

