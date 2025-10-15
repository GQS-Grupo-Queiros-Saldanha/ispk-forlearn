@switch($action)
    @case('create') @section('title',__('GA::courses.create_course')) @break
    @case('show') @section('title',__('GA::courses.course')) @break
    @case('edit') @section('title',__('GA::courses.edit_course')) @break
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
                                @case('create') @lang('GA::courses.create_course') @break
                                @case('show') @lang('GA::courses.course') @break
                                @case('edit') @lang('GA::courses.edit_course') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('courses.create') }} @break
                            @case('show') {{ Breadcrumbs::render('courses.show', $course) }} @break
                            @case('edit') {{ Breadcrumbs::render('courses.edit', $course) }} @break
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
                    {!! Form::open(['route' => ['courses.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($course) !!}
                    @break
                    @case('edit')
                    {!! Form::model($course, ['route' => ['courses.update', $course->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                    {{ Form::bsNumber('numeric_code', null, ['placeholder' => __('GA::courses.numeric_code'), 'disabled' => $action === 'show', 'required', 'min' => 100, 'max' => 120], ['label' => __('GA::courses.numeric_code')]) }}
                                  
                                    @include('GA::courses.partials.is_special')
                               
                                </div>
                               
                                <div class="col-6">
                                    @include('GA::courses.partials.duration_types')
                                    {{ Form::bsNumber('duration_value', null, ['placeholder' => __('GA::courses.duration_value'), 'disabled' => $action === 'show', 'required'], ['label' => __('GA::courses.duration_value')]) }}
                                </div>
                                <div class="col-6">
                                    @include('GA::courses.partials.departments')
                                </div>
                                <div class="col-6">
                                    @include('GA::courses.partials.course_cycles')
                                </div>
                                <div class="col-6">
                                    @include('GA::courses.partials.course_regimes')
                                </div>
                                <div class="col-6">
                                    @include('GA::courses.partials.degrees')
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
                                    @if(isset($languages) && count($languages) > 0)
                                        @foreach($languages as $language)
                                            <li class="nav-item">
                                                <a class="nav-link @if($language->default) active show @endif" href="#language{{ $language->id }}" data-toggle="tab">
                                                    {{ $language->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach($languages as $language)
                                        <div class="tab-pane row @if($language->default) active show @endif" id="language{{ $language->id }}">
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
                <br>
            </div>
        </div>
    </div>
@endsection
