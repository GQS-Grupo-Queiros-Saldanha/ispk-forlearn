<title>Disciplinas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@switch($action)
    @case('create')
        @section('page-title', __('GA::disciplines.create_discipline'))
    @break

    @case('show')
        @section('page-title', __('GA::disciplines.discipline'))
    @break

    @case('edit')
        @section('page-title', __('GA::disciplines.edit_discipline'))
    @break
@endswitch
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('disciplines.index') }}">Disciplinas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ $action }}</li>
@endsection
@section('body')
    @switch($action)
        @case('create')
            {!! Form::open(['route' => ['disciplines.store']]) !!}
        @break

        @case('show')
            {!! Form::model($discipline) !!}
        @break

        @case('edit')
            {!! Form::model($discipline, ['route' => ['disciplines.update', $discipline->id], 'method' => 'put']) !!}
        @break
    @endswitch

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
            <a href="{{ route('disciplines.edit', $discipline->id) }}" class="btn btn-sm btn-warning mb-3">
                @icon('fas fa-edit')
                @lang('common.edit')
            </a>
        @break
    @endswitch

    <div class="row">
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

        <div class="card">
            <div class="card-body">
                {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}

                <div class="col-12">
                    <label for="">Código Antigo</label>
                    <input type="text" placeholder="Código Antigo" name="oldCode" class="form-control"
                        value="{{ isset($discipline->old_Code) ? $discipline->old_Code : '' }}">
                </div>

                @include('GA::disciplines.partials.discipline-course')
                @include('GA::disciplines.partials.discipline-areas')
                @include('GA::disciplines.partials.discipline-profiles')

                @if ($action == 'show')
                    @isset($discipline->percentage)
                        @include('GA::disciplines.partials.discipline-percentage')
                    @endisset
                @else
                    @include('GA::disciplines.partials.discipline-percentage')
                @endif
                <div class="col-12">
                <label>Unidades de Crédito</label>
                    <input type="number" placeholder="Unidades de Crédito" name="uc" class="form-control"
                        value="{{ isset($discipline->uc) ? $discipline->uc : '' }}" max="100"
                        @if($action == 'show') readonly @endif>
                </div>
                @include('GA::disciplines.partials.discipline-mandatory-discipline')
                @include('GA::disciplines.partials.tfc')

                
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
                                    href="#language{{ $language->id }}" data-toggle="tab">{{ $language->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($languages as $language)
                            <div class="tab-pane @if ($language->default) active show @endif"
                                id="language{{ $language->id }}">
                                {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                {{ Form::bsText('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                {{ Form::bsText('abbreviation[' . $language->id . ']', $action === 'create' ? old('abbreviation.' . $language->id) : $translations[$language->id]['abbreviation'] ?? null, ['placeholder' => __('translations.abbreviation'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.abbreviation')]) }}
                                {{ Form::bsTextArea('objectives[' . $language->id . ']', $action === 'create' ? old('objectives.' . $language->id) : $translations[$language->id]['objectives'] ?? null, ['placeholder' => __('Objectivos'), 'disabled' => $action === 'show'], ['label' => __('Objectivos')]) }}
                                {{ Form::bsTextArea('learning_outcomes[' . $language->id . ']', $action === 'create' ? old('learning_outcomes.' . $language->id) : $translations[$language->id]['learning_outcomes'] ?? null, ['placeholder' => __('Resultados de Aprendizagem'), 'disabled' => $action === 'show'], ['label' => __('Resultados de Aprendizagem')]) }}
                                {{ Form::bsTextArea('topics[' . $language->id . ']', $action === 'create' ? old('topics.' . $language->id) : $translations[$language->id]['topics'] ?? null, ['placeholder' => __('Temas'), 'disabled' => $action === 'show'], ['label' => __('Temas')]) }}
                                {{ Form::bsTextArea('bibliography[' . $language->id . ']', $action === 'create' ? old('bibliography.' . $language->id) : $translations[$language->id]['bibliography'] ?? null, ['placeholder' => __('Bibliografia'), 'disabled' => $action === 'show'], ['label' => __('Bibliografia')]) }}
                                {{ Form::bsText('teaching_methods[' . $language->id . ']', $action === 'create' ? old('teaching_methods.' . $language->id) : $translations[$language->id]['teaching_methods'] ?? null, ['placeholder' => __('Métodos de Ensino'), 'disabled' => $action === 'show'], ['label' => __('Métodos de Ensino')]) }}
                                {{ Form::bsText('assessment_strategy[' . $language->id . ']', $action === 'create' ? old('assessment_strategy.' . $language->id) : $translations[$language->id]['assessment_strategy'] ?? null, ['placeholder' => __('Estratéia de Avaliação'), 'disabled' => $action === 'show'], ['label' => __('Estratégia de Avaliação')]) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let disciplineProfile = document.getElementById('discipline_profile');
            let groupFieldPercent = document.getElementById('groupFieldPercent');
            let percentInput = groupFieldPercent ? groupFieldPercent.querySelector('input') : null;

            function togglePercentageField() {
                if (disciplineProfile && groupFieldPercent) {
                    if (disciplineProfile.value == '8') {
                        groupFieldPercent.style.display = 'block'; // Mostra o campo e o rótulo
                    } else {
                        groupFieldPercent.style.display = 'none'; // Oculta o campo e o rótulo
                        if (percentInput) {
                            percentInput.value = ''; // Limpa o valor do campo quando oculto
                        }
                    }
                }
            }

            // Inicializar a visibilidade correta ao carregar a página
            if (groupFieldPercent) {
                togglePercentageField();
            }

            // Adicionar evento ao mudar o valor do campo
            if (disciplineProfile) {
                disciplineProfile.addEventListener('change', function() {
                    if (groupFieldPercent) {
                        togglePercentageField();
                    }
                });
            }
        });
    </script>
@endsection
