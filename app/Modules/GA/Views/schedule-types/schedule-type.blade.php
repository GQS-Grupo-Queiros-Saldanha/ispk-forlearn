@extends('layouts.generic_index_new')

@switch($action)
@case('create') @section('title',__('GA::schedule-types.create_schedule_type')) @break
@case('show') @section('title',__('GA::schedule-types.schedule_type')) @break
@case('edit') @section('title',__('GA::schedule-types.edit_schedule_type')) @break
@endswitch

@switch($action)
@case('create') @section('page-title',__('GA::schedule-types.create_schedule_type')) @break
@case('show') @section('page-title',__('GA::schedule-types.schedule_type')) @break
@case('edit') @section('page-title',__('GA::schedule-types.edit_schedule_type')) @break
@endswitch
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/gestao-academica/schedule-types">Tipos de Horário</a>
    </li>
    @switch($action)

    @case('create') <li class="breadcrumb-item active" aria-current="page">Criar tipo de horário</li>@break
    @case('show') <li class="breadcrumb-item active" aria-current="page">Ver tipo de horário</li>@break
    @case('edit') <li class="breadcrumb-item active" aria-current="page">Editar tipo de horário</li>@break

    @endswitch
@endsection

@section('body')
    @include('layouts.backoffice.modal_confirm')


        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['schedule-types.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($schedule_type) !!}
                    @break
                    @case('edit')
                    {!! Form::model($schedule_type, ['route' => ['schedule-types.update', $schedule_type->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('schedule-types.show', $schedule_type->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}

                                @include('GA::schedule-types.partials.times')
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
 
                                        <div class="col-6">
                                        {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                        </div>
                                        <div class="col-6">
                                        {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                        </div>
                                        
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
    

@endsection

@section('scripts-new')
    @parent
    @asset('js/backoffice/dynamic_forms.js')
    <script>
        $(function () {
            var dynamicForms = new DynamicForms();
            dynamicForms.automaticallySetupForm();
        });
    </script>
@endsection
