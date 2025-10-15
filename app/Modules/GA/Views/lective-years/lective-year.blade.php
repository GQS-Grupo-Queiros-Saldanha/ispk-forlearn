@switch($action)
    @case('create') @section('title',__('GA::lective-years.create_lective_year')) @break
@case('show') @section('title',__('GA::lective-years.lective_year')) @break
@case('edit') @section('title',__('GA::lective-years.edit_lective_year')) @break
@endswitch
 
@extends('layouts.backoffice')

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('GA::lective-years.create_lective_year') @break
                                @case('show') @lang('GA::lective-years.lective_year') @break
                                @case('edit') @lang('GA::lective-years.edit_lective_year') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('lective-years.create') }} @break
                            @case('show') {{ Breadcrumbs::render('lective-years.show', $lective_year) }} @break
                            @case('edit') {{ Breadcrumbs::render('lective-years.edit', $lective_year) }} @break
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
                    {!! Form::open(['route' => ['lective-years.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($lective_year) !!}
                    @break
                    @case('edit')
                    {!! Form::model($lective_year, ['route' => ['lective-years.update', $lective_year->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('lective-years.edit', $lective_year->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::bsDate('start_date', null, ['placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.start_date')]) }}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::bsDate('end_date', null, ['placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.end_date')]) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Translations -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">@lang('translations.languages')</h3>
                                <ul class="nav nav-pills ml-auto p-2">
                                    @foreach($languages as $language)
                                        <li class="nav-item">
                                            <a class="nav-link @if($language->default) active show @endif"
                                               href="#language{{ $language->id }}"
                                               data-toggle="tab">{{ $language->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach($languages as $language)
                                        <div class="tab-pane @if($language->default) active show @endif" id="language{{ $language->id }}">
                                            {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                            {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                            {{ Form::bsText('abbreviation['.$language->id.']', $action === 'create' ? old('abbreviation.'.$language->id) : $translations[$language->id]['abbreviation'] ?? null, ['placeholder' => __('translations.abbreviation'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.abbreviation')]) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
