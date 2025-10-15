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
                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">-e-e-r-</h5>
                            <div class="col-12 mb-4 border-bottom">
                                <form method="POST" action="" class="pb-4">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Nome do funcionário</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="funcionario" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
                                                <option  selected></option>
                                                {{-- @php $getUser=[]; @endphp
                                                @foreach ($users as $element)
                                                    @foreach ($getcontratos as $item)
                                                        @if ($element->id==$item->id_user && !in_array($element->id,$getUser)) 
                                                        @php $getUser[]=$item->id_user @endphp
                                                            <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option>    
                                                        @endif
                                                    @endforeach
                                                @endforeach --}}
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
