@switch($action)
    @case('create') @section('title',__('Payments::banks.create_bank')) @break
    @case('show') @section('title',__('Payments::banks.bank')) @break
    @case('edit') @section('title',__('Payments::banks.edit_bank')) @break
@endswitch
@extends('layouts.generic_index_new')
@section('page-title')
    @switch($action)
        @case('create') @lang('Payments::banks.create_bank') @break
        @case('show') @lang('Payments::banks.bank') @break
        @case('edit') @lang('Payments::banks.edit_bank') @break
    @endswitch
@endsection
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
        <a href="{{ route('banks.index') }}" class="">
            Bancos
        </a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        @switch($action)
            @case('create')
                Criar
            @break

            @case('show')
                Visualizar
            @break

            @case('edit')
                Editar
            @break
        @endswitch
    </li>
@endsection
@section('body')
    @switch($action)
        @case('create')
            {!! Form::open(['route' => ['banks.store']]) !!}
        @break

        @case('show')
            {!! Form::model($bank) !!}
        @break

        @case('edit')
            {!! Form::model($bank, ['route' => ['banks.update', $bank->id], 'method' => 'put']) !!}
        @break
    @endswitch

    <div class="row">
        <div class="col">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        ×
                    </button>
                    <h5>@choice('common.error', $errors->count())</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-6">
                    {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                </div>
                <div class="col-6">
                    {{ Form::bsText('display_name', null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', 'required'], ['label' => __('translations.display_name')]) }}
                </div>
                <div class="col-6">
                    {{ Form::bsText('account_number', null, ['placeholder' => __('Payments::banks.account_number'), 'disabled' => $action === 'show', 'required'], ['label' => __('Payments::banks.account_number')]) }}
                </div>
                <div class="col-6">
                    {{ Form::bsText('iban', null, ['placeholder' => 'IBAN', 'disabled' => $action === 'show', 'required'], ['label' => 'IBAN']) }}
                </div>
            </div>

            <div class="ml-3">
            @switch($action)
                @case('create')
                    <button type="submit" class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>
                        @lang('common.create')
                    </button>
                @break

                @case('edit')
                    <button type="submit" class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-save"></i>
                        @lang('common.save')
                    </button>
                @break

                @case('show')
                    <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-sm btn-warning mb-3">
                        <i class="fas fa-edit"></i>
                        @lang('common.edit')
                    </a>
                @break
            @endswitch
            </div>
            
        </div>
    </div>

    {!! Form::close() !!}
@endsection
