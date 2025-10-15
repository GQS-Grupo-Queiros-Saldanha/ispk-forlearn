@switch($action)
    @case('create') @section('title',__('Users::parameters.create_parameter')) @break
    @case('show') @section('title',__('Users::parameters.parameter')) @break
    @case('edit') @section('title',__('Users::parameters.edit_parameter')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')

<div class="content-panel" style="padding: 0;">
    @include('Users::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('Users::parameters.create_parameter') @break
                                @case('show') @lang('Users::parameters.parameter') @break
                                @case('edit') @lang('Users::parameters.edit_parameter') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('parameters.create') }} @break
                            @case('show') {{ Breadcrumbs::render('parameters.show', $parameter) }} @break
                            @case('edit') {{ Breadcrumbs::render('parameters.edit', $parameter) }} @break
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
                    {!! Form::open(['route' => ['parameters.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($parameter) !!}
                    @break
                    @case('edit')
                    {!! Form::model($parameter, ['route' => ['parameters.update', $parameter->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('parameters.edit', $parameter->id) }}"
                               class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                {{ Form::bsSelect('type', $types, null, ['disabled' => $action === 'show', 'required'], ['label' => __('Users::parameters.type')]) }}
                                <br>
                                {{ Form::bsCheckbox('required', true, null, ['disabled' => $action === 'show'], ['label' => __('Users::parameters.required')]) }}
                                <br>
                                <div class="form-group col">
                                    <label>@lang('Users::parameter-groups.parameter_group')</label>
                                    @if(in_array($action, ['create','edit'], true))
                                        {{ Form::bsLiveSelect('groups[]', $parameter_groups, $action === 'create' ? old('courses') : $parameter->groups->pluck('id'), ['multiple']) }}
                                    @else
                                        <span>
                                            @foreach($parameter->groups as $group)
                                                {{ $loop->first ? '' : ', ' }}
                                                {{ $group->translation->display_name }}
                                            @endforeach
                                        </span>
                                    @endcan
                                </div>
                                <div class="form-group col">
                                    <label>@lang('Users::parameters.roles_that_can_edit')</label>
                                    @if(in_array($action, ['create','edit'], true))
                                        {{ Form::bsLiveSelect('roles[]', $roles, $action === 'create' ? old('courses') : $parameter->roles->pluck('id'), ['required', 'multiple']) }}
                                    @else
                                        <span>
                                        @foreach($parameter->roles as $role)
                                                {{ $loop->first ? '' : ', ' }}
                                                {{ $role->currentTranslation->display_name }}
                                            @endforeach
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Options -->
                @php $hidden = (!empty(old('type')) && !in_array(old('type'), $show_options_when, true)) || (isset($parameter) && !in_array($parameter->type, $show_options_when, true)); @endphp
                <div id="options-panel" class="row" @if($hidden)style="display: none"@endif>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">@lang('Users::parameters.options')</h3>
                            </div>

                            <div class="card-body">
                                <div data-role="dynamic-fields">
                                    @if($action !== 'show')
                                        <a class="btn btn-success text-white mb-3 dynamic-fields-button-add">
                                            @lang('common.add')
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <div class="dynamic-fields-template">
                                            {{ Form::bsParameterOption('options[][value]', null, ['required'],
                                            /* Extra */ [
                                            'languages' => $languages,
                                            'translations' => $translations ?? '',
                                            'action' => $action,
                                            'parameters' => $parameters,
                                            'parameter' => $parameter ?? null,
                                            ]) }}
                                        </div>
                                    @endif

                                    @if($action !== 'create')
                                        @foreach($parameter->options as $index=>$option)
                                            {{ Form::bsParameterOption('options[' . $index . '][value]', $option->code, ['disabled' => $action === 'show', 'required'],
                                            /* Extra */ [
                                            'languages' => $languages,
                                            'translations' => $option->translations->keyBy('language_id')->toArray() ?? '',
                                            'action' => $action,
                                            'index' => $index,
                                            'parameters' => $parameters,
                                            'parameter' => $parameter,
                                            'id' => $option->id
                                            ]) }}
                                        @endforeach
                                    @endif

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

@section('scripts')
    @parent
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/js/jquery.multi-select.min.js"></script>--}}

    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.full.min.js"></script>
    <script src="{{ asset('js/backoffice/select2.sortable.js') }}"></script>--}}

    {{--<script src="{{ asset('js/backoffice/multiselect.min.js') }}"></script>--}}

    <script src="{{ asset('js/backoffice/jquery.dynamic-fields.js') }}"></script>
    <script>
        $(function () {

            // Variables
            var optionsPanel = $('#options-panel');

            // When type is changed
            $('#type').on('change', function () {
                var type = $(this).val();
                var showOptionsWhen = {!! json_encode($show_options_when) !!};

                // If its in array, show
                if (typeof showOptionsWhen !== 'undefined') {
                    if (inArray(type, showOptionsWhen)) {
                        optionsPanel.show();
                    } else {
                        optionsPanel.hide();
                    }
                }
            }).trigger('change');

            // When code is changed
            $('input[name="code"]').on('blur', function () {
                Forlearn.checkIfModelFieldExists(this, '{{ route('parameters.exists') }}', '{{ $parameter->id ?? '' }}');
            });
        });
    </script>
@endsection
