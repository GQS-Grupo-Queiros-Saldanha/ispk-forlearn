@switch($action)
    @case('create')
        @section('title', __('GA::course-cycles.create_course_cycle'))
    @break

    @case('show')
        @section('title', __('GA::course-cycles.course_cycle'))
    @break

    @case('edit')
        @section('title', __('GA::course-cycles.edit_course_cycle'))
    @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    @include('layouts.backoffice.modal_confirm')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include('GA::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create')
                                    Criar Calendário de Eventos
                                @break

                                @case('show')
                                    @lang('GA::course-cycles.course_cycle')
                                @break

                                @case('edit')
                                    @lang('GA::course-cycles.edit_course_cycle')
                                @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- @switch($action)
                            @case('create') {{ Breadcrumbs::render('canled.create') }} @break
                            @case('show') {{ Breadcrumbs::render('course-cycles.show', $course_cycle) }} @break
                            @case('edit') {{ Breadcrumbs::render('course-cycles.edit', $course_cycle) }} @break
                        @endswitch --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['course-cycles.store']]) !!}
                    @break

                    @case('show')
                        {!! Form::model($course_cycle) !!}
                    @break

                    @case('edit')
                        {!! Form::model($course_cycle, ['route' => ['course-cycles.update', $course_cycle->id], 'method' => 'put']) !!}
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
                                <a href="{{ route('course-cycles.edit', $course_cycle->id) }}"
                                    class="btn btn-sm btn-warning mb-3">
                                    @icon('fas fa-edit')
                                    @lang('common.edit')
                                </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                <form action="">
                                    @csrf

                                    <label for=""></label>
                                    <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                        required="" id="type-event" data-actions-box="false"
                                        data-selected-text-format="values" name="course" tabindex="-98">
                                    <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                        required="" id="course" data-actions-box="false"
                                        data-selected-text-format="values" name="course" tabindex="-98">
                                      
                                    </select>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
