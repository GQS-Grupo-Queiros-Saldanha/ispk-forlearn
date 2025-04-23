@extends('layouts.generic_index_new')
@section('title', __('Gerir bolsas de estudo'))
@section('page-title', 'Editar entidades')
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
<li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection
@section('body')
<form action="{{ route('update.scholarship', $entity->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="form-group col-6">
            <label for="code">Código</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $entity->code) }}"
                required>
        </div>
        <div class="form-group col-6">
            <label for="company">Nome da empresa</label>
            <input type="text" class="form-control" id="company" name="company"
                value="{{ old('company', $entity->company) }}" required>
        </div>
        <div class="form-group col-6">
            <label for="registered_office">Sede social</label>
            <input type="text" class="form-control" id="registered_office" name="registered_office"
                value="{{ old('registered_office', $entity->registered_office) }}" required>
        </div>
        <div class="form-group col-6">
            <label for="nif">NIF</label>
            <input type="text" class="form-control" id="nif" name="nif" value="{{ old('nif', $entity->NIF) }}" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-6">
            <label for="tel">Telefone</label>
            <input type="number" class="form-control" id="tel" name="tel" value="{{ old('tel', $entity->telf) }}">
        </div>
        <div class="form-group col-6">
            <label for="phone_person">Pessoa de contacto</label>
            <input type="text" class="form-control" id="phone_person" name="phone_person"
                value="{{ old('phone_person', $entity->phone_person) }}">
        </div>
    </div>

    <button type="submit" class="btn btn-success float-right">Salvar edição</button>
</form>
@endsection
@section('models')
@include('layouts.backoffice.modal_confirm')
@endsection



