<title>Editar estado</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Editar estado')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('cards.all_student') }}">Gestão de cartões</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Editar estado</li>
@endsection

@section('body')
    <div class="row">
        <div class="col">
            {!! Form::open([
                'route' => ['cards.verificar'],
                'method' => 'post',
                'required' => 'required',
                'target' => '_blank',
            ]) !!} 
            @csrf
            @method('post')
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            <label>Imprimido?</label>
                            <select class="form-control selectpicker" name="impressao">
                                <option value="0"  @if(isset($cards->impressao) && $cards->impressao==0) selected @endif>Não</option>
                                <option value="1"  @if(isset($cards->impressao) && $cards->impressao==1) selected @endif>Sim</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6"> 
                        <div class="form-group col">
                            <label>Data de impressão</label>
                            @if(isset($cards->data_impressao)) 
                            <input class="form-control" name="data_impressao" type="date" value="{{$cards->data_impressao}}">
                        @else
                            <input class="form-control" name="data_impressao" type="date">
                        @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label>Entregue?</label>
                            <select class="form-control selectpicker" name="entrega">
                                <option value="0" @if(isset($cards->entrega) && $cards->entrega==0) selected @endif>Não</option>
                                <option value="1" @if(isset($cards->entrega) && $cards->entrega==1) selected @endif>Sim</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6"> 
                        <div class="form-group col">
                            <label>Data de entrega</label>
                            @if(isset($cards->data_entrega)) 
                                <input class="form-control" name="data_entrega" type="date" value="{{$cards->data_entrega}}">
                            @else
                                <input class="form-control" name="data_entrega" type="date">
                            @endif
                        </div>
                    </div> 
                    <div class="col-6" hidden> 
                        <div class="form-group col">
                            <label>Data de entrega</label>
                            <input class="form-control" name="matriculation" value="{{$matriculation}}">
                            <input class="form-control" name="lectiveyear" value="{{$lectiveyear}}">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">
        </div>
        <div class="col-12 justify-content-md-end">

            <div class="form-group col-4  justify-content-md-end" style="float:right;">
                <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank"
                    style="width:180px;">
                    <i class="fas fa-file-pdf"></i>
                    Guardar 
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection


@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
   
@endsection
