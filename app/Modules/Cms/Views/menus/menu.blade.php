@switch($action)
    @case('create') @section('title',__('Cms::menus.create_menu')) @break
    @case('show') @section('title',__('Cms::menus.menu')) @break
    @case('edit') @section('title',__('Cms::menus.edit_menu')) @break
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
                                @case('create') @lang('Cms::menus.create_menu') @break
                                @case('show') @lang('Cms::menus.menu') @break
                                @case('edit') @lang('Cms::menus.edit_menu') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('menus.create') }} @break
                            @case('show') {{ Breadcrumbs::render('menus.show', $menu) }} @break
                            @case('edit') {{ Breadcrumbs::render('menus.edit', $menu) }} @break
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
                    {!! Form::open(['route' => ['menus.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($menu) !!}
                    @break
                    @case('edit')
                    {!! Form::model($menu, ['route' => ['menus.update', $menu->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
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
                                        <div class="tab-pane row @if($language->default) active show @endif" id="language{{ $language->id }}">
                                            {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                            {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
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
