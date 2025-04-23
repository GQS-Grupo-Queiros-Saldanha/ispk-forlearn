@extends('layouts.generic_index_new')
@section('title',__('Gerir Entidades'))
@php
 $url =  "#";
 $method = "GET";
 $title = "Visualizar";
 switch ($action) {
    case 'create':
        $url = route('store.entity');
        $method = "POST";
        $title = "Criar";
    break;    
    case 'edit':
        $url = route('update.scholarship', $entity->id);
        $method = "PUT";
        $title = "Editar";
    break;
 }
 $is_method_get = $method == "GET";
 $tipos = [ "PROTOCOLO" => "Protocolo", "BOLSA" => "Bolsa" ];
@endphp
@section('page-title', $title . ' entidade')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('list.scholarship') }}" class="">
            Gerir Entidades
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
@endsection
@section('body')
    <form action="{{ $url }}" @if(!$is_method_get) method="POST" @endif>
        @csrf
        @method($method)
        <div class="row p-1">
            <div class="col-6 p-1">
                <label for="company">Empresa</label>
                <input type="text" class="form-control" name="company" id="company" value="{{ $entity->company ?? '' }}" required>
            </div>
            <div class="col-6 p-1">
                <label for="code">Código</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ $entity->code ?? '' }}" required>
            </div>
            <div class="col-6 p-1">
                <label for="type">Escolha o tipo</label>
                <select name="type" id="type" class="form-control">
                    <option class="text-muted">Nenhum selecionado</option>
                    @foreach($tipos as $key => $value)
                        <option value="{{ $key }}" @if($key == ($entity->type ?? '')) selected @endif>
                            {{$value}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-1">
                <label for="registered_office">Sede social</label>
                <input type="text" class="form-control" name="registered_office" id="registered_office" value="{{ $entity->registered_office ?? '' }}" required>
            </div>
            <div class="col-6 p-1">
                <label for="office">Escritórios</label>
                <input type="text" class="form-control" name="office" id="office" value="{{ $entity->offices ?? '' }}" required>
            </div>
            <div class="col-6 p-1">
                <label for="nif">NIF</label>
                <input type="text" class="form-control" name="nif" id="nif" value="{{ $entity->NIF ?? '' }}" required>
            </div>
            <div class="col-6 p-1">
                <label for="tel">Telefone</label>
                <input type="number" class="form-control" name="tel" id="tel" min="9" value="{{ $entity->telf ?? '' }}" required>
            </div>
        </div>
        @if(!$is_method_get)
            <button type="submit" class="btn btn-success float-right">{{ $title }}</button>
        @endif
    </form>
@endsection
