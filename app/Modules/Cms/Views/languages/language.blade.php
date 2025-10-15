@switch($action)
    @case('create') @section('title',__('Cms::languages.create_language')) @break
    @case('show') @section('title',__('Cms::languages.language')) @break
    @case('edit') @section('title',__('Cms::languages.edit_language')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    @include('layouts.backoffice.modal_confirm')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('Cms::languages.create_language') @break
                                @case('show') @lang('Cms::languages.language') @break
                                @case('edit') @lang('Cms::languages.edit_language') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('languages.create') }} @break
                            @case('show') {{ Breadcrumbs::render('languages.show', $language) }} @break
                            @case('edit') {{ Breadcrumbs::render('languages.edit', $language) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['languages.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($language) !!}
                    @break
                    @case('edit')
                    {!! Form::model($language, ['route' => ['languages.update', $language->id], 'method' => 'put']) !!}
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

                        @switch($action)
                            @case('create')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-plus-circle')
                                @lang('common.create')
                            </button>
                            @break
                            @case('edit')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                            <a href="{{ route('languages.edit', $language->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                {{ Form::bsText('name', null, ['placeholder' => __('common.display_name'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.display_name')]) }}
                                {{ Form::bsSelect('active', [true => __('common.yes'), false => __('common.no')], null, ['placeholder' => __('common.active'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.active')]) }}
                            </div>
                        </div>

                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection
