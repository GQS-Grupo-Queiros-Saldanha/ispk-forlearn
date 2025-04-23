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
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: contain;
            /* background-repeat: no-repeat; */
            background-position: 50%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #fff;
            -webkit-transition: all .5s ease-in-out;
            transition: all .5s ease-in-out;
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
                                    <div class="p-2 bd-highlight"><h5 class="text-muted text-uppercase"> Gerar recibo Bancos</h5></div>                                    
                                </div>
                            </div>
                            
                            <div class="col-md-12 row mb-4 pr-0">
                                <div class="mr-0 pr-0 col-md-8 border-bottom">
                                    {{--formularios--}}
                                    <form method="POST" action="{{ route('recurso-humanos.anular_reciboVencimento', ['id'=>1]) }}" class="pb-4" target="_blank">
                                        @csrf
                                        <div class="form-row">
                                            
                                            <div class="form-group col-md-12">
                                                <label for="inputEmail4">Banco</label>
                                                <select data-live-search="true"   class="selectpicker form-control"  id="funcionario-contrato" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">                                                    
                                                    <option></option>
                                                    @foreach ($bancos as $element)
                                                            <option value="{{$element->id}}">{{$element->id}} - {{$element->id}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                             
                                        </div>
                                        <div  class="form-row ml-0 mt-1 pl-0">
                                            <div class="form-group mr-3">
                                                <button  type="submit"  style="background: #2b9fc2"  class="btn text-white btn-gerarPDF"><i class="fas fa-receipt"></i> Gerar Recibo</button>
                                            </div>                            
                                        </div>
                                    </form> 
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

@endsection
