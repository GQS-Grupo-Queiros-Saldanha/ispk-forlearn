@switch($action)
    @case('create') @section('title',__('GA::discipline-absence-configuration.create_discipline_absence_configuration')) @break
@case('show') @section('title',__('GA::discipline-absence-configuration.discipline_absence_configuration')) @break
@case('edit') @section('title',__('GA::discipline-absence-configuration.edit_discipline_absence_configuration')) @break
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
                                @case('create') @lang('GA::discipline-absence-configuration.create_discipline_absence_configuration') @break
                                @case('show') @lang('GA::discipline-absence-configuration.discipline_absence_configuration') @break
                                @case('edit') @lang('GA::discipline-absence-configuration.edit_discipline_absence_configuration') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('discipline-absence-configuration.create') }} @break
                            @case('show') {{ Breadcrumbs::render('discipline-absence-configuration.show', $discipline_absence_configuration) }} @break
                            @case('edit') {{ Breadcrumbs::render('discipline-absence-configuration.edit', $discipline_absence_configuration) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                {{-- @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['discipline-absence-configuration.store']]) !!}
                    @break
                    @case('show')
                        {!! Form::model($discipline_absence_configuration) !!}
                    @break
                    @case('edit')
                        {!! Form::model($discipline_absence_configuration, ['route' => ['discipline-absence-configuration.update', $discipline_absence_configuration->id], 'method' => 'put']) !!}
                    @break
                @endswitch --}}

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

                        {{-- @switch($action)
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
                            <a href="{{ route('discipline-absence-configuration.edit', $discipline_absence_configuration->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch --}}

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        @include('GA::discipline-absence-configuration.partials.disciplines')
                                    </div>
                                    <div id="discipline-regimes" class="col-6">
                                        @include('GA::discipline-absence-configuration.partials.discipline_regimes')
                                    </div>
                                    <div class="col-6">
                                        @include('GA::discipline-absence-configuration.partials.study_plan_editions')
                                    </div>
                                    <div class="col-6">
                                        {{ Form::bsCheckbox('is_total', null, $action === 'edit' || $action === 'show' ? $discipline_absence_configuration->is_total : '0', ['disabled' => $action === 'show'], ['label' => __('GA::discipline-absence-configuration.is_total')]) }}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::bsNumber('max_absences', null, ['placeholder' => __('GA::discipline-absence-configuration.max_absences'), 'disabled' => $action === 'show', 'required'], ['label' => __('GA::discipline-absence-configuration.max_absences')]) }}
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
@section('scripts')
    @parent
    <script>

       $(document).on("click", "#is_total", function(){
            $button = $("#discipline-regimes").find("button");
            if($(this).is(":checked")){
                $button.prop('disabled', true);
            }else{
                $button.prop('disabled', false);
            }
        });

        $(document).ready(function(){
            $button = $("#discipline-regimes").find("button");
            if($("#is_total").is(":checked")){
                $button.prop('disabled', true);
            }else{
                $button.prop('disabled', false);
            }
        });
    </script>
@endsection
