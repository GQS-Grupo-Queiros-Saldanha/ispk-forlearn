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
            @include('RH::index_menuSalario')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        
                        @if ($section=="criar")
                            <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Atribuir subsídios</h5>
                            
                            <div class="col-12 mb-4 border-bottom">
                                <form method="POST" action="{{ route('recurso-humano.create-subsidio-contrato-func') }}" class="pb-4">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Nome do funcionário</label>
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
                                            <label for="inputPassword5">Cargo</label>
                                            <select name="funcionario_contrato"  id="funcionario_contrato" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                                {{--  --}}
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword6">Associar subsídios</label>
                                            <select name="subsidio[]"  multiple class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98"  autocomplete="associar_subsidio" id="funcionario_subsidio">
                                                @foreach ($getSubsidio as $element)
                                                    <option value={{$element->subsidio_id}}>{{$element->display_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword7">Valor do subsídios</label>
                                            <input required min="0" type="number" class="form-control" name="valor" id="valor" placeholder="Valor do subsídio">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gravar</button>
                                </form>
                            </div>
                            <div class="container-fluid ml-2  mr-2 mt-3">
                                <table id="add-subsidioFuncionario"  class="table table-striped table-hover">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>



<!-- Modal  que apresenta a opção de eliminar -->
<div style="z-index: 9999999" class="modal fade" id="delete_subsidio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog " role="document">
      <div class="modal-content mt-3">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar este?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button  class="btn btn-primary btn-delete-subsidio">Ok</button>
          </form>
        </div>
      </div>
    </div>
</div>

{{-- ver os cargo do funcionario que tem subsidios --}}
<div style="z-index: 999999" class="modal fade table-responsive col-md-12" id="verSubsidio-funcionario" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"aria-hidden="true">
    <div  class="modal-dialog modal-xl">
        <div  class="modal-content rounded ">
            <div style="background:#20c7f9;width: 100%;border-top-left-radius: 2px;border-top-right-radius: 2px;height: 6px;" class="m-0" ></div>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Subsídio/s do funcionário</h5>
                <button type="button" class="close btn-choseModal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div hidden class="alert alert-warning col-md-10 mr-2 ml-3 mb-0 rounded" role="alert">
                    Este subsídio foi processado ao salário do funcionário.!
                  </div>
                <div class="col">
                    <div class="container-fluid ml-2  mr-2 mt-3">
                        <table id="cargo-subsidioFuncionario"  class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cargo</th>
                                    <th>Subsídios</th>
                                    <th>Estado Contrato</th>
                                    {{-- <th>Acções</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br><br><br>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
    $(document).ready(function() {
        var getusers_role=@json($users);                
        var getcontratos=@json($getcontratos);
        var getSubsidio=@json($getSubsidio);
        var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));
        var roles = $("#funcionario_contrato");
        var id_user =null;
        $("#funcionario").change(function() {             
            id_user = $("#funcionario").val();                           
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
            getSubsidiosContrato()
        });

        function getSubsidiosContrato() {
            $.ajax({
                url: "getSubsidiosContrato/"+id_user,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function (response) {}
            }).done(function (data) {
                console.log(data);
                $("#funcionario_subsidio").empty();
                $("#funcionario_subsidio").append('<option></option>')
                var display_nameCargo='';
                var CargoAtivo=false
                var cargo=null;
                $.each(getSubsidio, function (index, item) {
                    CargoAtivo=false
                    cargo=null
                    $.each(data, function (key, element) { 
                        if (item.subsidio_id==element.id_subsidio && cargo==null) {
                            CargoAtivo=true;
                            cargo=element.id_funcionario_cargo
                            display_nameCargo=element.display_name  
                        }
                    });

                    if (CargoAtivo==true) {
                        $("#funcionario_subsidio").append('<option style="font-weight: bold; font-size: 1pc;color:blue" value="'+item.subsidio_id+'">'+item.display_name+' [Ativo no cargo - '+display_nameCargo+']'+'</option>')
                    }else{
                        $("#funcionario_subsidio").append('<option value="'+item.subsidio_id+'">'+item.display_name+'</option>') 
                    } 
                });
                $("#funcionario_subsidio").selectpicker('refresh');
                
            })
        }

      
        $('#add-subsidioFuncionario').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('recurso.ajaxContratoSubsidioFuncionario') !!}',
                    columns: [
                        {
                            data: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false
                        },{
                            data: 'name_funcionario',
                            name: 'full_name.value'
                        },{
                            data: 'email_funcionario',
                            name: 'use.email'
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
     

        
        
           
    });

</script>
@endsection
