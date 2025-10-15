@extends('layouts.generic_index_new')
@section('title', 'Visualizar entidade ')
@section('page-title', 'Visualizar entidade ')
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
<li class="breadcrumb-item active" aria-current="page">Visualizar</li>
@endsection
@section('body')
<div>
    <div class="row">
        <div class="form-group col-6">
            <label for="code">Código</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $entity->code }}" readonly>
        </div>
        <div class="form-group col-6">
            <label for="company">Nome da empresa</label>
            <input type="text" class="form-control" id="company" name="company" value="{{ $entity->company }}" readonly>
        </div>
        <div class="form-group col-6">
            <label for="registered_office">Sede social</label>
            <input type="text" class="form-control" id="registered_office" name="registered_office"
                value="{{ $entity->registered_office }}" readonly>
        </div>
        <div class="form-group col-6">
            <label for="nif">NIF</label>
            <input type="text" class="form-control" id="nif" name="nif" value="{{ $entity->NIF }}" readonly>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-6">
            <label for="tel">Telefone</label>
            <input type="number" class="form-control" id="tel" name="tel" value="{{ $entity->telf }}" min="9" readonly>
        </div>
        <div class="form-group col-6">
            <label for="phone_person">Pessoa de contacto</label>
            <input type="text" class="form-control" id="phone_person" value="{{ $entity->phone_person }}" readonly>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-12">
        <form action="{{ route('edit.scholarship', $entity->id) }}" method="GET">
            @csrf
            <button type="submit" class="btn btn-primary">Editar</button>
        </form>
    </div>
</div>
@endsection


