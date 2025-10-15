@switch($action)
    @case('create') @section('title',__('GA::classes.create_class')) @break
@case('show') @section('title',__('GA::classes.class')) @break
@case('edit') @section('title',__('GA::classes.edit_class')) @break
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
                                @case('create') @lang('GA::classes.create_class') @break
                                @case('show') @lang('GA::classes.class') @break
                                @case('edit') @lang('GA::classes.edit_class') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('classes.create') }} @break
                            @case('show') {{ Breadcrumbs::render('classes.show', $class) }} @break
                            @case('edit') {{ Breadcrumbs::render('classes.edit', $class) }} @break
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
                    {!! Form::open(['route' => ['classes.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($class) !!}
                    @break
                    @case('edit')
                    {!! Form::model($class, ['route' => ['classes.update', $class->id], 'method' => 'put']) !!}
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
                            <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                </div>
                                <div class="col-6">
                                    {{ Form::bsText('display_name', null, ['placeholder' => __('common.display_name'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.display_name')]) }}
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('GA::courses.course')</label>
                                        @php($courses = App\Modules\GA\Models\Course::get())
                                        {{ Form::bsLiveSelect('course', $courses, $class->course->id ?? null, ['required', 'disabled' => $action === 'show', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    {{ Form::bsNumber('year', null, ['placeholder' => 0, 'min' => 0, 'max' => 5, 'disabled' => $action === 'show'], ['label' => __('GA::classes.year')]) }}
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('GA::rooms.room')</label>
                                        @php($rooms = App\Modules\GA\Models\Room::get())
                                        {{ Form::bsLiveSelect('room', $rooms, $class->room->id ?? null, ['required', 'disabled' => $action === 'show', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    {{ Form::bsNumber('vacancies', null, ['placeholder' => 0, 'min' => 0, 'disabled' => $action === 'show'], ['label' => __('GA::classes.vacancies')]) }}
                                </div>

                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Turno</label>
                                        @php($schedule_types = App\Modules\GA\Models\ScheduleType::get())
                                        {{ Form::bsLiveSelect('schedule_type', $schedule_types, $class->scheduleType->id ?? null, ['required', 'disabled' => $action === 'show', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Ano lectivo</label>
                                        @php($lectiveYears = App\Modules\GA\Models\LectiveYear::get())
                                        {{ Form::bsLiveSelect('lective_year', $lectiveYears, $class->lectiveYear->id ?? null, ['required', 'disabled' => $action === 'show', 'placeholder' => '']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <br><br>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
