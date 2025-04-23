<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Exibir Tipo de avaliação')
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
@endsection
