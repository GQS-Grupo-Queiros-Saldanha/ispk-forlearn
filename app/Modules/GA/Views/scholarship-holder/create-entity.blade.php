@extends('layouts.generic_index_new')
@section('title', __('Gerir bolsas de estudo'))
@section('page-title', 'Criar entidade')
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
<li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
<form action="{{ route('store.entity') }}" method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-6">
            <label for="">Código</label>
            <input type="text" class="form-control" name="code" required>
        </div>
        <div class="form-group col-6">
            <label for="">Nome da empresa</label>
            <input type="text" class="form-control" name="company" required>
        </div>
        <div class="form-group col-6">
            <label for="">Sede social</label>
            <input type="text" class="form-control" name="registered_office" required>
        </div>
        <div class="form-group col-6">
            <label for="">NIF</label>
            <input type="text" class="form-control" name="nif" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-6">
            <label for="tel">Telefone</label>
            <input type="number" class="form-control" id="tel" name="tel" value="{{ old('tel') }}">
        </div>
        <div class="form-group col-6">
            <label for="phone_person">Pessoa de contacto</label>
            <input type="text" class="form-control" id="phone_person" name="phone_person" value="{{ old('phone_person') }}">
        </div>
    </div>
    <button type="submit" class="btn btn-success float-right">Salvar</button>
</form>
@endsection
@section('models')
@include('layouts.backoffice.modal_confirm')
@endsection