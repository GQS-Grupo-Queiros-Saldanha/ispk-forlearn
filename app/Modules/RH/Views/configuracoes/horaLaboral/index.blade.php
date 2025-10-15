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

                    <form id="formRoute-Edita-horasLaboral" method="POST" action="" class="pb-4">
                        @csrf
                        <div hidden id="editarhorasLaboral">

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputEmail4">Dias de trabalho no mês</label>                                    
                                    <input required type="number" class="form-control" name="dias_trabalho"  id="dias_trabalho">
                                    
                                    <input  type="hidden" class="form-control" name="idHorasLaboral" id="idHorasLaboral" placeholder="">
                                </div>                                    
                            </div>

                            <div class="form-row">                                  

                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">1º Período - Hora de Entrada</label>
                                    <input required type="time" class="form-control" name="entrada_1" id="entrada_1" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">1º Período - Hora de Saída</label>
                                    <input required type="time" class="form-control" name="saida_1"  id="saida_1" >
                                </div>  

                                <div class="form-group col-md-12 text-center" style=" padding-top: 1.5px">
                                    <label for="inputPassword4 "> </label>
                                    <h4 class="m-0" style="background-color: rgb(243, 242, 242); padding-top:4.2px;">Pausa para almoço</h5>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">2º Período - Hora de Entrada</label>
                                    <input required type="time" class="form-control" name="entrada_2" id="entrada_2" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">2º Período - Hora de Saída</label>
                                    <input required type="time" class="form-control" name="saida_2"  id="saida_2" >
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
                        {{$action}}
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
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <h6 class="col-md-12 mb-4 text-right text-muted text-uppercase">Criar horário laboral</h6>
                        <div class="container-fluid ml-2 mr-2 mt-3" style="padding-left: 2.5%">
                            <form method="POST" action="{{ route('config.store_horas_laroral')}}" class="mb-3 pb-1 border-bottom">
                                @csrf

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4">Dias de trabalho no mês</label>
                                        {{-- <select data-live-search="true"  required class="selectpicker form-control" required="" id="funcionario-contrato" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
                                            <option  selected></option>
                                           
                                        </select> --}}
                                        
                                        <input required type="number" class="form-control" name="dias_trabalho"  id="dias_trabalho" >
                                    </div>                                    
                                </div>

                                <div class="form-row">                                  

                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">1º Período - Hora de Entrada</label>
                                        <input required type="time" class="form-control" name="entrada_1" id="entrada_1" >
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">1º Período - Hora de Saída</label>
                                        <input required type="time" class="form-control" name="saida_1"  id="saida_1" >
                                    </div>  

                                    <div class="form-group col-md-12 text-center" style=" padding-top: 1.5px">
                                        <label for="inputPassword4 "> </label>
                                        <h4 class="m-0" style="background-color: rgb(243, 242, 242); padding-top:4.2px;">Pausa para almoço</h5>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">2º Período - Hora de Entrada</label>
                                        <input required type="time" class="form-control" name="entrada_2" id="entrada_2" >
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">2º Período - Hora de Saída</label>
                                        <input required type="time" class="form-control" name="saida_2"  id="saida_2" >
                                    </div>
                                </div>

                                <div class="form-group  col-md-2 pl-0 pb-0 mb-0">
                                    <button type="submit" type="button"   style="background: #2b9fc2 "  class="btn text-white">Gravar</button>
                                </div>
                            </form>
                        </div>

                        <div class="container-fluid ml-2 mr-2 mt-3">
                            <table id="horas_trabalho"  class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Dias de trabalho mês</th>
                                        <th>1º Período - Entrada</th>
                                        <th>1º Período - Saida</th>
                                        <th>2º Período - Entrada</th>
                                        <th>2º Período - Entrada</th>
                                        <th>Total de horas por dia</th>
                                        {{-- <th>Total de minutos por dia</th> --}}
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
    var a = 1;
    console.log(a)

        // $.ajax({
        //     url: "/RH/ajax-horas-laboral",
        //     type: "GET",
        //     data: {
        //         _token: '{{ csrf_token() }}'
        //     },
        //     cache: false,
        //     dataType: 'json',
        // }).done( function (data)  
        // { 
        //     console.log(data);
        // });
    

    $('#horas_trabalho').DataTable({
        processing:true,
        serverSide:true,
        ajax: '{!! route('config.ajaxHoraLaboral') !!}',
        columns: [
            {
                data: 'DT_RowIndex', 
                orderable: false, 
                searchable: false
            }, 
            {
                data: 'dias_trabalho',
                name: 'dias_trabalho'
            }, {
                data: 'entrada_1',
                name: 'entrada_1'
            }, {
                data: 'saida_1',
                name: 'saida_1'
            }, {
                data: 'entrada_2',
                name: 'entrada_2'
            }, {
                data: 'saida_2',
                name: 'saida_2'
            }, 
            {
                data: 'time',
                name: 'time'
            },
            // {
            //     data: 'total_horas_dia',
            //     name: 'total_horas_dia'
            // }, {
            //     data: 'total_minutos_dia',
            //     name: 'total_minutos_dia'
            // }, 
            
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
        language: {
            url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
        },
    });

</script>
@endsection

