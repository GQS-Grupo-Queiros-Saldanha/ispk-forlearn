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
<div style="z-index: 999999" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>

  
  <!-- Modal que elimina a taxa   -->
  <div class="modal fade" id="delete_Taxa" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar esta Taxa ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <form method="POST" action="{{ route('recurso.deletetaxaImposto') }}">
            @csrf
              <input type="hidden" name="idTaxa" id="idTaxa_impostoYear">
            <button type="submit" class="btn btn-primary">Ok</button>
          </form>
        </div>
      </div>
    </div>
  </div>

   <!-- Modal para editar taxa  -->
   <div class="modal fade" id="editar_Taxa" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Editar taxa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">
                    {{-- <div class="form-group">
                        <label for="inputAddress">Imposto</label>
                        <input readonly type="text" value="{{$getyear[0]->display_name}} [ {{$getyear[0]->year_month}} ]" class="form-control" name="descricaoImposto" id="inputAddress" placeholder="Descrição">    
                    </div> --}}
                    <form method="POST" action="{{ route('recurso.editarTaxa_imposto') }}" class="pb-4">
                        @csrf
                        <input  type="hidden" class="form-control" value="{{$id_impostoYear}}" name="id_impostoYear" >
                        <input  type="hidden" class="form-control" value="" name="id_taxa"  id="id_taxa">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Valor inicial</label>
                                <input min="0" type="number" class="form-control" name="valorIncial" id="valorIncial" placeholder="">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Valor final</label>
                                <input min="0" type="number" class="form-control" name="valorFinal" id="valorFinal" placeholder="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Parcela fixa</label>
                                <input min="0" type="number" class="form-control" name="parcela" id="parcela" placeholder="">
                            </div>
                            <div class="col-md-6" style="margin-top: -7px">
                                <label class="m-0" for="inputEmail4">Taxa</label>
                                <div class="input-group mt-0">
                                  <div class="input-group-prepend">
                                    <div class="input-group-text">%</div>
                                  </div>
                                  <input min="0" type="number" class="form-control" name="taxa" id="taxa" placeholder="">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label for="inputAddress">Excesso de</label>
                            <input min="0"  type="number" class="form-control" name="exacesso" id="exacesso" placeholder="">
                        </div>
                        <button data-toggle="modal" data-target="#staticBackdrop" type="submint" class="btn btn-primary btn-submintEditar">Gravar</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Ok</button>
        </div> --}}
      </div>
    </div>
  </div>
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
    <div class="content-fluid ml-5 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <a href="{{ route('recurso.plus-imposto', ['id'=>$getyear[0]->id]) }}"  class="ml-3"><i style="font-size: 1.3pc" class="fas fa-chevron-left"></i></a>

                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Criar taxa</h5>
                        {{-- formularios --}}
                        <div class="col-12 mb-4 border-bottom">
                            <div class="form-group">
                                <label for="inputAddress">Imposto</label>
                                <input readonly type="text" value="{{$getyear[0]->display_name}} [ {{$getyear[0]->year_month}} ]" class="form-control" name="descricaoImposto" id="inputAddress" placeholder="Descrição">
                            </div>
                            <form method="POST" action="{{ route('create.taxaImposto') }}" class="pb-4">
                                @csrf
                                <input  type="hidden" class="form-control" value="{{$id_impostoYear}}" name="id_impostoYear" >
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Valor inicial</label>
                                        <input min="0" type="number" class="form-control" name="valorIncial" id="nameImposto" placeholder="">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Valor final</label>
                                        <input min="0" type="number" class="form-control" name="valorFinal" id="nameImposto" placeholder="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Parcela fixa</label>
                                        <input min="0" type="number" class="form-control" name="parcela" id="nameImposto" placeholder="">
                                    </div>
                                   
                                    <div class="col-md-6" style="margin-top: -7px">
                                        <label class="m-0" for="inputEmail4">Taxa</label>
                                        <div class="input-group mt-0">
                                          <div class="input-group-prepend">
                                            <div class="input-group-text">%</div>
                                          </div>
                                          
                                          <input min="0" type="number" class="form-control" name="taxa" id="inlineFormInputGroup" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="inputAddress">Excesso de</label>
                                    <input min="0"  type="number" class="form-control" name="exacesso" id="inputAddress" placeholder="">
                                </div>
                                <button data-toggle="modal" data-target="#staticBackdrop" type="submit" class="btn btn-primary">Gravar</button>
                            </form>
                        </div>
                        <div class="row col-12 m-0 p-0">
                            <div class="col-md-9"></div>
                            <div class="col-md-3">
                                <label for="inputEmail4">Ano do imposto</label>
                                <select class="selectpicker form-control form-control-sm" id="seleYearImposto">
                                    <option selected></option>
                                    @foreach ($getyearImposto as $item)
                                            <option value="{{$item->id_impostoYear}}">{{$item->year_month}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container ml-2 mr-2 col-md-12 row">
        <div class="col-md-2"></div>
        <div class="col-md-10">
            <table class="table table-bordered table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col-1">#</th>
                        <th class="pl-4" colspan="3">GRUPO DE RENDIMENTO</th>
                        <th>PARCELA&nbsp;&nbsp;FIXA</th>
                        <th class="pl-4">TAXA  ADICIONAL</th>
                        <th class="">EXCESSO DE</th>
                        <th class="">ACÇÃO</th>
                    </tr>
                </thead>
                <tbody id="listaTabelaImposto">
                    @php $i=0; @endphp
                    @foreach ($getTaxaYearImposto as $item)
                        @php $i++; @endphp
                        <tr>
                            <th style="width: 2pc" scope="row">{{$i}}</th>
                            <td class="col-md-2 text-center">{{number_format($item->valor_inicial, 0, ',', '.') }}</td>
                            <td style="width: 2pc" class="text-center">A</td>
                            <td class="col-md-2 text-center">{{number_format($item->valor_final, 0, ',', '.') }}</td>
                            <td class="text-center">{{number_format($item->parcela_fixa, 0, ',', '.') }}</td>
                            <td style="width: 10pc" class="text-center">{{number_format($item->taxa, 1, ',', '.') }}%</td>
                            <td class="col-md-1">{{number_format($item->excesso, 0, ',', '.') }}</td>
                            <td class="col-md-1">
                                @if ($item->status == "panding")
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                        <div class="btn-group mr-2" role="group" aria-label="Second group">
                                            <button value="{{$item->id_taxa}}" data-toggle="modal" data-target="#delete_Taxa" type="button" class="btn btn-sm btn-info btn-deleteTaxa"> <i class="fas fa-trash-alt"></i> </button>
                                        </div>
                                        <div class="btn-group" role="group" aria-label="Third group">
                                        <button value="{{$item->id_taxa}}" type="button" data-toggle="modal" data-target="#editar_Taxa" class="btn btn-sm btn-warning btn-editar_Taxa"> <i class="fas fa-edit"></i> </button>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>    
   
</div>

@endsection
@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
    var listaTabelaImposto=$("#listaTabelaImposto");
    var getTaxa = @json($getTaxaYearImposto);
    var getTaxaImposto = null;
    $(".btn-submintEditar").click(function (e) { 
       $("#editar_Taxa").modal('hide')
        
    });
    $("#seleYearImposto").change(function (e) { 
        var getId=$(this).val();
        listaTabelaImposto.empty();
        $.ajax({
        url:'configuracoes-ajaxTaxa_impostos/'+getId,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
        }).done(function(data){
            console.log(data)
            var tr=null;
            var i=0;
            getTaxaImposto=data['data'];
            $.each(data['data'], function (index, item) { 
                i++
                tr+="<tr><th style='width: 2pc' scope='row'>"+i+"</th>"+
                        "<td class='col-md-2 text-center'>"+item.valor_inicial.toLocaleString('pt-br', {minimumFractionDigits: 0})+"</td>"+
                        "<td style='width: 2pc' class='text-center'>A</td>"+
                        "<td class='text-center'>"+item.valor_final.toLocaleString('pt-br', {minimumFractionDigits: 0})+"</td>"+
                        "<td style='width: 10pc' class='text-center'>"+item.parcela_fixa.toLocaleString('pt-br', {minimumFractionDigits: 0})+"</td>"+
                        "<td style='width: 10pc' class='text-center'>"+item.taxa.toLocaleString('pt-br', {minimumFractionDigits: 0})+" %</td>"+
                        "<td class='col-md-1'>"+item.excesso.toLocaleString('pt-br', {minimumFractionDigits: 0})+"</td>";
                    tr+="<td class='col-md-1'>";
                            if (item.status=="panding") {
                                tr+="<div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'>"+
                                    "<div class='btn-group mr-2' role='group' aria-label='Second group'>"+
                                        "<button onClick='deleteTaxaImposto("+item.id_taxa+")'  data-toggle='modal' data-target='#delete_Taxa' type='button' class='btn btn-sm btn-info'> <i class='fas fa-trash-alt'></i></button>"+
                                    "</div>"+ 
                                    "<div class='btn-group' role='group' aria-label='Third group'>"+
                                        "<button onClick='editarTaxaImposto("+item.id_taxa+")' data-toggle='modal' data-target='#editar_Taxa' type='button' class='btn btn-sm btn-warning'> <i class='fas fa-edit'></i> </button>"+
                                    "</div>"+
                                "</div>"
                            }
                    tr+="</td>";
                tr+="</tr>"
            });
            listaTabelaImposto.append(tr);
        })

    });

    $(".btn-deleteTaxa").click(function (e) { 
       var getId_taxa=$(this).val();
       deleteTaxaImposto(getId_taxa);
    });
    $(".btn-editar_Taxa").click(function (e) { 
       var getId_taxa=$(this).val();
       getTaxaImposto=JSON.parse(JSON.stringify(getTaxa))
       editarTaxaImposto(getId_taxa);
    });
    function editarTaxaImposto(getId) {
       console.log(getId);
        $.each(getTaxaImposto, function (index, item) { 
            if (item.id_taxa==getId) {
                $("#valorIncial").val(item.valor_inicial)
                $("#valorFinal").val(item.valor_final)
                $("#parcela").val(item.parcela_fixa)
                $("#taxa").val(item.taxa)
                $("#exacesso").val(item.excesso)
                $("#id_taxa").val(item.id_taxa)
            }
        })
    }
    function deleteTaxaImposto(getId) {
       
        $("#idTaxa_impostoYear").val(getId);
    }
</script>
@endsection 