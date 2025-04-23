<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Editar Tipo de Avaliação')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tipo_avaliacao.index') }}">Tipo de Avaliação</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $tipoAvaliacao->nome }}</li>
@endsection
@section('body')
    {!! Form::open(['route' => ['tipo_avaliacao.update', $tipoAvaliacao->id]]) !!}
    @method('PUT')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-6 p-2">
            <label>Código</label>
            <input name="codigo" type="text" value="{{ $tipoAvaliacao->codigo }}" class="form-control" required/>
        </div>
        <div class="col-6 p-2">
            <label>Nome</label>
            <input name="nome" type="text" value="{{ $tipoAvaliacao->nome }}" class="form-control" required/>
        </div>
        <div class="col-6 p-2">
            <label>Descrição</label>
            <input name="descricao" type="text" value="{{ $tipoAvaliacao->descricao }}" class="form-control" required/>
        </div>
        <div class="col-6 p-2">
            <label>Abreviatura</label>
            <input name="abreviatura" type="text" value="{{ $tipoAvaliacao->abreviatura }}" class="form-control" required/>
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-success mt-2 mb-3">
        <i class="fas fa-plus-circle"></i>
        <span>Editar</span>
    </button>
    {!! Form::close() !!}
@endsection
