<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Criar Tipo de Metrica')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tipo_metrica.index') }}">Tipo de Metricas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
    {!! Form::open(['route' => ['tipo_metrica.store']]) !!}
    @method('post')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-6 p-2">
            <label>Código</label>
            <input type="text" value="" name="codigo"  class="form-control">
            <input type="hidden" value="{{$anoLective}}" name="lectiveYear">
        </div>
        <div class="col-6 p-2">
            <label>Nome</label>
            <input type="text" value="" name="nome" class="form-control">
        </div>
        <div class="col-6 p-2">
            <label>Descrição</label>
            <input type="text" value="" name="descricao"  class="form-control">
        </div>
        <div class="col-6 p-2">
            <label>Abreviatura</label>
            <input type="text" value="" name="abreviatura" class="form-control">
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-success mt-2 mb-3">
        <i class="fas fa-plus-circle"></i>
        Criar
    </button>
    {!! Form::close() !!}
@endsection
