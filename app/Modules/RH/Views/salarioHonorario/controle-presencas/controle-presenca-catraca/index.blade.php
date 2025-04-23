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

<div class="modal fade" id="change-load" tabindex="-1" role="dialog" style="z-index: 9999999" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
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
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">

                
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <div class="col-md-12 d-flex justify-content-between border-bottom">
                            <div class="col-md-6 pr-5">
                                <p class="text-muted p-0 m-0">Infor. Torniquete</p>
                                <div class="row col-md-12 m-0 p-0 border-top pt-1">
                                    <div class="col-md-7 pb-1 p-0">
                                        <h1 class="p-0 m-0" style="font-size: 1pc"> Hora de entrada/saída</h1>
                                        <p class="p-0 m-0">
                                            O preenchimento do turno trabalhado mês a mês.
                                            {{-- , está  sendo gerenciado pela catraca. --}}
                                        </p>
                                    </div>
                                    <div class="col-md-5 p-0">
                                        <button hidden style="font-size: 1.2pc; background-color: #20c7f9; border-color: #20c7f9;" class="col btn btn-sm text-white">Desativar Torniquete <i  style="font-size: 1.3pc;" class="fa-solid fa-unlock-keyhole"></i></button>
                                        <button hidden style="font-size: 1.2pc;" class="col btn btn-sm btn-success ">Ativar gestão pela Torniquete <i style="font-size: 1.3pc;" class="fa-solid fa-lock-open"></i></button>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                            <div>
                                <h6 class="col-md-12 mb-4 text-right text-muted text-uppercase">Controle de presença <b>[Torniquete]</b></h6>
                            </div>
                        </div>

                        
                        
                        <div class="container-fluid mt-4" style="padding-left: 2.5%">
                            <div class="form-row">
                                <div class="form-group col-md-6 pr-3">
                                    <label for="inputEmail4">Funcionário/os</label>
                                    <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="funcionario" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
                                        <option  selected></option>
                                        <optgroup label="Gerar relatório de presença">
                                            <option value="0">Pesquisar todos</option>
                                          </optgroup>
                                        @foreach ($usuarios as $item)
                                            <option  value="{{$item->id_user}}','{{$item->foto}}">{{$item->full_nameEmail}}</option>
                                        @endforeach
                                    </select>
                                </div>                                    
                                <div class="form-group col-md-5 pl-3">
                                    <label for="inputEmail4">Data (mês e ano)</label>
                                    <input required="" type="month" class="form-control" name="dataCatraca" id="dataCatraca">                                                                           
                                </div>   
                                
                                <div class="form-group col-md-1">
                                    <label for="inputEmail4"> </label>
                                    <button  type="button" class="btn btn-sm btn-success p-0 btn-pesquisar"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </div>

                                <div class="form-group col-md-6 pr-3"> </div>

                                <div hidden  class="form-group col-md-6 pl-3">
                                    <p class="text-muted">Infor. totais</p>
                                    <div class="row col-md-12 m-0 p-0 border-top">
                                        <div style="display: flex; align-items: center;" class="col-md-7 border-right">
                                            <table>
                                                <thead>
                                                    <tr><td><i class="fa fa-calendar-check" aria-hidden="true"></i> Hora tatol de trabalho: <samp><b class="data-inicio-contrato"></b></samp>  </td></tr>
                                                    <tr><td><i class="fa-regular fa-clock"></i> Hora trabalhada: <small style="font-size: 1.1pc"><b id="salarioBase"></b></small><small>Kz</small></td></tr>
                                                    <tr><td><i class="fa fa-calendar-xmark" aria-hidden="true"></i> Data fim do contrato: <samp><b class="data-fim-contrato"></b></samp>  </td></tr>
                                                    <tr><td><i class="fa fa-user" aria-hidden="true"></i> Criado por: <small class="criado-por text-muted"></small>  </td></tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="col">
                                            <div class="text-center">
                                                <center class="pb-2 pt-2">
                                                    <div class="fotoUserFunc"></div>
                                                </center> 
                                                <h4 class="mt-1 pb-0 mb-0 profile-username text-center name-user"></h4>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container-fluid ml-2 mr-2 mt-3" >
                            <table  id="table-horas-trabalhadaa-catraca"  class="table table-striped table-hover" hidden>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Funcionário</th>
                                        <th>Data</th>
                                        <th>Hora Entrada</th>
                                        <th>Hora saída</th>
                                        <th>Acção</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <style>
                    #customers {
                      font-family: Arial, Helvetica, sans-serif;
                      border-collapse: collapse;
                      width: 100%;
                    }
                    
                    #customers td, #customers th {
                      border: 1px solid #ddd;
                      padding: 5px;
                    }
                    
                    #customers tr:nth-child(even){background-color: #f2f2f2;}
                    
                    #customers tr:hover {background-color: #ddd;}
                    
                    #customers th {
                      padding-top: 6px;
                      padding-bottom: 6px;
                      text-align: left;
                      background-color: #04AA6D;
                      color: white;
                    }
                </style>
                
                <!-- Modal para mosntrar a hora d feita no dia. -->
                <div class="modal fade" id="modal-detalhe-hora" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Informação</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div   class="form-group col-md-12 pl-3">
                                <p class="text-muted">Infor. totais</p>
                                <div class="row col-md-12 m-0 p-0 border-top">
                                    <div style="display: flex; align-items: center;" class="col-md-7 border-right">
                                        <table>
                                            <thead>
                                                {{-- <tr ><td><i class="fa fa-calendar-check" aria-hidden="true"></i> Hora tatol de trabalho: <samp><b>  </b></samp></td></tr> --}}
                                                <tr><td><i class="fa-regular fa-clock"></i> Hora trabalhada no mês [<small id="total-data-month"></small>] : <b id="total-month"></b></td></tr>
                                                <tr><td><i class="fa-regular fa-calendar"></i> Hora trabalhada no dia [<small id="total-dia"></small>]: <b id="total-dia-hora"></b></td></tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col">
                                        <div class="">
                                            <table>
                                                <thead>
                                                    <tr><td><i class="fa fa-user" aria-hidden="true"></i> Nome: <samp><b class="nome-funcionario"></b></samp></td></tr>
                                                </thead>
                                            </table>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid mt-3" >
                                <table id="customers">
                                    <tr>
                                        <th>#</th>
                                        <th>Data</th>
                                        <th>Hora Entrada</th>
                                        <th>Hora saída</th>
                                        <th>Total hora</th>
                                    </tr>
                                <tbody id="lista-data-day">

                                </tbody>
                                </table>  
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Sair</button>
                            <a target="_blank" href="" class="btn btn-primary rounded link-gerar-pdf" >Gerar PDF</a>
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
   $(document).ready(function() {
        var vetorData=[
            
        ];
       
        
        $(".btn-pesquisar").click(function() {  
            var getData = $("#funcionario").val();
            
            var dataCatraca=$("#dataCatraca").val()
            var explode=getData.split(",") 
            var jabutiImg=null;
            
            jabutiImg=new Image();
           
                                
            if (explode.length === 0){
                
                $("#table-horas-trabalhadaa-catraca").attr('hidden',true)
                $('#table-horas-trabalhadaa-catraca').clear().draw();
                $(".fotoUserFunc").css('background-image',"")
            } 
            else{
                
                jabutiImg.onload=function(){
                    if(explode[1]!=null){
                        $(".fotoUserFunc").attr('style',"background-image: url("+jabutiImg.src+")") 

                    }
                    else{
                        $(".fotoUserFunc").css('background-image',"")
                    }
                }
                var data=dataCatraca;
                var user_id=explode[0]
                $('#change-load').modal('show')
                var vetorObjeto=[];
                $("#table-horas-trabalhadaa-catraca").attr('hidden',false)
                $('#table-horas-trabalhadaa-catraca').DataTable({
                    processing:true,
                    serverSide:true,
                    destroy: true,
                    
                    ajax: 'recurso-humano_ajax_controlePresenca-catraca/'+user_id+'/'+data,
                    columns: [
                        {
                            data: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false
                        },{
                            data:'nome_funcionario',
                            name:'nome_funcionario'
                        },{
                            data: 'data',
                            name: 'data'
                        }, {
                            data: 'hora_entrada',
                            name: 'hora_entrada'
                        }, {
                            data: 'hora_saida',
                            name: 'hora_saida'
                        }
                        ,{
                            data: 'actions', 
                            name: 'actions',
                            orderable: false, 
                            searchable: false
                        } 
                    ],
                    columnDefs: [{
                            targets: [2,,3,4],
                            render: function ( data, type, row,meta) {
                                if(meta.col==2){
                                    $('#change-load').modal('hide');
                                    return data;
                                }  
                                else if(meta.col== 4){
                                    return data == null ?  '<small style="font-size:0.7pc" class="p-1 bg-success text-white">Não marcou a saída</small>' : data;
                                }
                                else if(meta.col== 3){
                                    return data == null ? '<small style="font-size:0.7pc" class="p-1 bg-success text-white">Não marcou a entrada</small>' : data;
                                }
                                // else if(meta.col==5){
                                    
                                //     var id_funcionario=meta.settings.aoData[meta.row]._aData.id_funcionario
                                //     var data=meta.settings.aoData[meta.row]._aData.data
                                //     var idFun_data=id_funcionario+','+data;
                                //     var found=vetorObjeto.find(element=> element ==idFun_data )
                                //     console.log(found+' - '+idFun_data)
                                //     if (found==undefined) {
                                //         vetorObjeto.push(idFun_data)
                                //         return ("<button data-data_idFuncionario='"+idFun_data+"'  class='btn btn-info btn-sm btn-ver-hora-trabalho'><i class='fas fa-eye'></i></button>");
                                //             getData_funcionario()
                                //     } else {
                                //         return '--';
                                //     }

                                // }
                        }
                    }],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    },
                });

                // Delete confirmation modal
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
                 
            }
            

        });
        
    });
</script>
@endsection

