@switch($action)
    @case('create') @section('title',__('GA::discipline-classes.create_class')) @break
@case('show') @section('title',__('GA::discipline-classes.classes')) @break
@case('edit') @section('title',__('GA::discipline-classes.edit_classes')) @break
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
                                @case('create') @lang('GA::discipline-classes.create_discipline_classes') @break
                                @case('show') @lang('GA::discipline-classes.discipline_class') @break
                                @case('edit') @lang('GA::discipline-classes.edit_discipline_class') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('discipline-classes.create') }} @break
                            @case('show') {{ Breadcrumbs::render('discipline-classes.show', $discipline_classes) }} @break
                            @case('edit') {{ Breadcrumbs::render('discipline-classes.edit', $discipline_classes) }} @break
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
                    {!! Form::open(['route' => ['discipline-classes.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($discipline_classes) !!}
                    @break
                    @case('edit')
                    {!! Form::model($discipline_classes, ['route' => ['discipline-classes.update', $discipline_classes->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('discipline-classes.edit', $discipline_classes->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        @include('GA::discipline-classes.partials.classes')
                                    </div>
                                    <div class="col-6">
                                        @include('GA::discipline-classes.partials.disciplines')
                                    </div>
                                    <div class="col-6">
                                        @include('GA::discipline-classes.partials.discipline_regimes')
                                    </div>
                                    <div class="col-6">
                                        @include('GA::discipline-classes.partials.study_plan_editions')
                                    </div>
                                    <div class="col-12">
                                        {{ Form::bsText('display_name', null, ['placeholder' => __('common.display_name'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.display_name')]) }}
                                    </div>
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
