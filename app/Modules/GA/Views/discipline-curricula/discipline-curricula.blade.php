@switch($action)
    @case('create') @section('title',__('GA::discipline-curricula.create_curricula')) @break
@case('show') @section('title',__('GA::discipline-curricula.curricula')) @break
@case('edit') @section('title',__('GA::discipline-curricula.edit_curricula')) @break
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
                                @case('create') @lang('GA::discipline-curricula.create_discipline_curricula') @break
                                @case('show') @lang('GA::discipline-curricula.discipline_curricula') @break
                                @case('edit') @lang('GA::discipline-curricula.edit_discipline_curricula') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('discipline-curricula.create') }} @break
                            @case('show') {{ Breadcrumbs::render('discipline-curricula.show', $discipline_curricula) }} @break
                            @case('edit') {{ Breadcrumbs::render('discipline-curricula.edit', $discipline_curricula) }} @break
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
                    {!! Form::open(['route' => ['discipline-curricula.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($discipline_curricula) !!}
                    @break
                    @case('edit')
                    {!! Form::model($discipline_curricula, ['route' => ['discipline-curricula.update', $discipline_curricula->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('discipline-curricula.edit', $discipline_curricula->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        @include('GA::discipline-curricula.partials.disciplines')
                                    </div>
                                    <div class="col-6">
                                        @include('GA::discipline-curricula.partials.study_plan_editions')
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
                                            @if($action != 'show')
                                                <div class="w-100">
                                                    {{ Form::bsTextArea('presentation['.$language->id.']', $action === 'create' ? old('presentation.'.$language->id) : $translations[$language->id]['presentation'] ?? ' ', ['placeholder' => __('GA::discipline-curricula.presentation'), 'disabled' => $action === 'show', ['placeholder' => __('GA::discipline-curricula.presentation')]]) }}
                                                    {{ Form::bsTextArea('bibliography['.$language->id.']', $action === 'create' ? old('bibliography.'.$language->id) : $translations[$language->id]['bibliography'] ?? ' ', ['placeholder' => __('GA::discipline-curricula.bibliography'), 'disabled' => $action === 'show'], ['placeholder' => __('GA::discipline-curricula.bibliography')]) }}
                                                </div>
                                            @else
                                            <div>
                                                <div>
                                                    <label>@lang('GA::discipline-curricula.presentation')</label>
                                                    {!! $translations[$language->id]['presentation'] !!}
                                                </div>
                                                <hr>
                                                <div>
                                                    <label>@lang('GA::discipline-curricula.bibliography')</label>
                                                    {!! $translations[$language->id]['bibliography'] !!}
                                                </div>
                                            </div>
                                            @endif
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

@section('scripts')
    @parent

    <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=t417tvchboud5pgbfcq2wycxjczc00s4xr335hvbs58hgwha"></script>
    <script>
        $(document).ready(function(){
            tinymce.init({
                selector: "textarea",
                statusbar: true,
                plugins: "lists",
                menubar: true,
                height: 600,
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | fontsizeselect | forecolor backcolor"
            });
        });
    </script>

@endsection
