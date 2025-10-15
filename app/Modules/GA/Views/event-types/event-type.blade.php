@switch($action)
    @case('create')
        @section('title', __('GA::event-types.create_event_type'))
    @break

    @case('show')
        @section('title', __('GA::event-types.event_type'))
    @break

    @case('edit')
        @section('title', __('GA::event-types.edit_event_type'))
    @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    @include('layouts.backoffice.modal_confirm')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding:0">
        @include('GA::events.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('event-types.index') }}">Tipos de eventos</a>
                                </li>

                                <li class="breadcrumb-item active" aria-current="page">
                                    @switch($action)
                                        @case('create')
                                           Criar
                                        @break

                                        @case('show')
                                            Ver
                                        @break

                                        @case('edit')
                                            Editar
                                        @break
                                    @endswitch
                                </li>

                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create')
                                    Criar Tipo de evento
                                @break

                                @case('show')
                                    Ver Tipo de evento
                                @break

                                @case('edit')
                                    Editar Tipo de evento
                                @break
                            @endswitch
                        </h1>
                    </div>

                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['event-types.store']]) !!}
                    @break

                    @case('show')
                        {!! Form::model($event_type) !!}
                    @break

                    @case('edit')
                        {!! Form::model($event_type, ['route' => ['event-types.update', $event_type->id], 'method' => 'put']) !!}
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
                                <a href="{{ route('event-types.edit', $event_type->id) }}" class="btn btn-sm btn-warning mb-3">
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
                                    @foreach ($languages as $language)
                                        <li class="nav-item">
                                            <a class="nav-link @if ($language->default) active show @endif"
                                                href="#language{{ $language->id }}"
                                                data-toggle="tab">{{ $language->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach ($languages as $language)
                                        <div class="tab-pane row @if ($language->default) active show @endif"
                                            id="language{{ $language->id }}">
                                            {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                            {{ Form::bsText('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
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
