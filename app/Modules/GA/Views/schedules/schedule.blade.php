<title>Horários | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Horários')
@section('styles-new')
    @parent
    <style>
        table .btn {
            display: none;
            float: left;
        }
        table td:hover .btn {
            display: block;
        }
        table td:hover input {
            display: none;
        }
    </style>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('schedules.index')}}">Horários</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        @switch($action)
            @case('create') Criar @break
            @case('show') Visualizar @break
            @case('edit') Editar @break
        @endswitch
    </li>
@endsection
@section('body')
    @switch($action)
        @case('create')
            {!! Form::open(['route' => ['schedules.store']]) !!}
        @break

        @case('show')
            {!! Form::model($schedule) !!}
        @break

        @case('edit')
            {!! Form::model($schedule, ['route' => ['schedules.update', $schedule->id], 'method' => 'put']) !!}
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
                    @if (auth()->user()->hasAnyPermission(['editar_horario']))
                        <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning mb-3">
                            @icon('fas fa-edit')
                            @lang('common.edit')
                        </a>
                    @endif
                    <a href="{{ route('schedules.pdf', $schedule->id) }}" class="btn btn-sm btn-dark mb-3" target="_blank">
                        @icon('fas fa-file-pdf')
                        Imprimir
                    </a>
                @break
            @endswitch
            <div class="card">
                <div class="row" >
                    {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                    <div class="col-6">

                    </div>
                </div>
                <div class="row">
                    {{ Form::bsCustom('start_at', $schedule->start_at ?? old('start_at'), ['type' => 'datetime-local', 'placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.start_date')]) }}
                    {{ Form::bsCustom('end_at', $schedule->end_at ?? old('end_at'), ['type' => 'datetime-local', 'placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.end_date')]) }}
                </div>    
                <div class="row">
                    @include('GA::schedules.partials.study-plan-editions')
                    @include('GA::schedules.partials.period-types')
                </div>
                <div class="row">
                    @include('GA::schedules.partials.classes')
                    @include('GA::schedules.partials.schedule-types')
                </div>
                <br><br>
                <div class="row">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>@lang('GA::schedule-types.times')</th>
                                <th>@lang('common.hours')</th>
                                @if (!$days_of_the_week->isEmpty())
                                    @foreach ($days_of_the_week as $day_of_the_week)
                                        <th>{{ $day_of_the_week->currentTranslation->display_name }}</th>
                                        <th>@lang('GA::rooms.room')</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
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
                                    href="#language{{ $language->id }}" data-toggle="tab">{{ $language->name }}</a>
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
@endsection
@section('models')
    @parent
    @include('layouts.backoffice.modal_confirm')
    {{-- Modal Disciplines --}}
    <div id="modal-disciplines" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('GA::disciplines.disciplines')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::bsSelect('disciplines', ['name' => $disciplines], null, ['required']) }}
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-save-discipline" class="btn btn-primary">@lang('common.save')</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Rooms --}}
    <div id="modal-rooms" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('GA::rooms.rooms')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::bsLiveSelect('buildings', $buildings, null, ['required']) }}
                    {{ Form::bsSelect('rooms', [], null, ['required']) }}
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-save-room" class="btn btn-primary">@lang('common.save')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    <script>
        @parent
        $(function() {
            var lective_year = $("#lective_year").val();

            $("#lective_year").change(function() {
                lective_year = $("#lective_year").val();

                $("#schedules-table").DataTable().destroy();

                console.log(lective_year);
                // get_schedule(lective_year);

            });

            @if ($action === 'edit')
                let schedule = {!! $schedule !!};
                console.log(schedule);
            @endif

            @if ($action === 'show')
                let schedule = {!! $schedule !!};
                console.log(schedule);
            @endif

            let daysOfTheWeek = {!! $days_of_the_week !!};

            function loadscheduleTypes(study_plan_edition_id) {
                var url = '{{ route('study-plan-editions.schedule_type', ':id') }}';
                url = url.replace(':id', study_plan_edition_id);
                $.ajax({
                    url: url,
                }).done(function(items) {
                    let $select = $('[name=schedule_type]');
                    let html = '';
                    items.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.current_translation
                            .display_name + '</option>';
                    });
                    $select.html(html);
                }).fail(function() {
                    // TODO:
                });
            }

            function loadClasses(study_plan_edition_id) {
                var url = '{{ route('study-plan-editions.classes', ':id') }}';
                url = url.replace(':id', study_plan_edition_id);
                $.ajax({
                    url: url,
                }).done(function(items) {
                    let $select = $('[name=classes]');
                    let html = '';
                    items.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.display_name +
                        '</option>';
                    });
                    $select.html(html);
                }).fail(function() {
                    // TODO:
                });
            }

            function loadDisciplines(study_plan_edition_id) {
                var url = '{{ route('study-plan-editions.disciplines', ':id') }}';
                url = url.replace(':id', study_plan_edition_id);
                $.ajax({
                    url: url,
                }).done(function(items) {
                    let $select = $('[name=disciplines]');
                    let html = '';
                    items.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.current_translation
                            .display_name + '</option>';
                    });
                    $select.html(html);
                }).fail(function() {
                    // TODO:
                });
            }

            function loadRooms(building_id) {
                var url = '{{ route('buildings.rooms', ':id') }}';
                url = url.replace(':id', building_id);
                $.ajax({
                    url: url,
                }).done(function(items) {
                    let $select = $('[name=rooms]');
                    let html = '';
                    items.forEach(function(item) {
                        html += '<option value="' + item.id + '">' + item.current_translation
                            .display_name + '</option>';
                    });
                    $select.html(html);
                }).fail(function() {
                    // TODO:
                });
            }

            function loadTimes(schedule_type_id) {
                var url = '{{ route('schedule-types.times', ':id') }}';
                url = url.replace(':id', schedule_type_id);
                $.ajax({
                    url: url,
                }).done(function(times) {
                    let html = '';
                    times.forEach(function(time) {

                        html += '<tr>';
                        html += '<td rowspan="2">' + time.current_translation.display_name +
                        '</td>';
                        html += '<td>' + time.start.substring(0, time.start.length - 3) + '</td>';
                        daysOfTheWeek.forEach(function(dayOfTheWeek) {

                            let input1_text = '';
                            let input1_hidden = '';
                            input1_text +=
                                '<input type="text" class="form-control form-control-sm" name="disciplines[' +
                                dayOfTheWeek.id + '][' + time.id + ']" readonly';
                            input1_hidden += '<input type="hidden" name="disciplines[' +
                                dayOfTheWeek.id + '][' + time.id + ']"';
                            @if ($action === 'edit') schedule.events.forEach(function (event) {
                                if (event.day_of_the_week_id === dayOfTheWeek.id && event.schedule_type_time_id === time.id) {
                                    input1_hidden += ' value="' + event.spe_discipline_id + '"';
                                    input1_text += ' value="' + event.discipline.current_translation.display_name + '"';
                                }
                            });
                        @elseif($action === 'show')
                            schedule.events.forEach(function (event) {
                                if (event.day_of_the_week_id === dayOfTheWeek.id && event.schedule_type_time_id === time.id) {
                                    input1_hidden += ' value="' + event.spe_discipline_id + '"';
                                    input1_text += ' value="' + event.discipline.current_translation.display_name + '"';
                                }
                            }); @endif
                            input1_text += '>';
                            input1_hidden += '>';

                            @if ($action === 'edit' || $action === 'create')
                                let btn1 = '';
                                btn1 +=
                                    '<button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal-disciplines" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn1 += '@icon('fas fa-wrench')';
                                btn1 += '</button>';
                                btn1 +=
                                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-discipline" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn1 += '@icon('fas fa-times')';
                                btn1 += '</button>';
                            @elseif ($action === 'show')
                                let btn1 = '';
                                btn1 +=
                                    '<button type="button" hidden class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal-disciplines" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn1 += '@icon('fas fa-wrench')';
                                btn1 += '</button>';
                                btn1 +=
                                    '<button type="button" hidden class="btn btn-sm btn-outline-danger btn-remove-discipline" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn1 += '@icon('fas fa-times')';
                                btn1 += '</button>';
                            @endif

                            let input2_text = '';
                            let input2_hidden = '';
                            input2_text +=
                                '<input type="text" class="form-control form-control-sm" name="rooms[' +
                                dayOfTheWeek.id + '][' + time.id + ']" readonly';
                            input2_hidden += '<input type="hidden" name="rooms[' +
                                dayOfTheWeek.id + '][' + time.id + ']"';
                            @if ($action === 'edit') schedule.events.forEach(function (event) {
                                if (event.day_of_the_week_id === dayOfTheWeek.id && event.schedule_type_time_id === time.id) {
                                    input2_hidden += ' value="' + event.room_id + '"';
                                    input2_text += ' value="' + event.room.current_translation.display_name + '"';
                                }
                            });
                        @elseif($action === 'show')
                            schedule.events.forEach(function (event) {
                                if (event.day_of_the_week_id === dayOfTheWeek.id && event.schedule_type_time_id === time.id) {
                                    input2_hidden += ' value="' + event.room_id + '"';
                                    input2_text += ' value="' + event.room.current_translation.display_name + '"';
                                }
                            }); @endif
                            input2_text += '>';
                            input2_hidden += '>';

                            @if ($action === 'edit' || $action === 'create')
                                let btn2 = '';
                                btn2 +=
                                    '<button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal-rooms" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn2 += '@icon('fas fa-wrench')';
                                btn2 += '</button>';
                                btn2 +=
                                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-room" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn2 += '@icon('fas fa-times')';
                                btn2 += '</button>';
                            @elseif ($action === 'show')
                                let btn2 = '';
                                btn2 +=
                                    '<button type="button" hidden class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal-rooms" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn2 += '@icon(';fas; fa-wrench;')';
                                btn2 += '</button>';
                                btn2 +=
                                    '<button type="button" hidden class="btn btn-sm btn-outline-danger btn-remove-room" data-day_of_the_week="' +
                                    dayOfTheWeek.id + '" data-time="' + time.id + '">';
                                btn2 += '@icon(';fas; fa-times;')';
                                btn2 += '</button>';
                            @endif
                            html += '<td rowspan="2">' + input1_text + input1_hidden +
                                btn1 + '</td>';
                            html += '<td rowspan="2">' + input2_text + input2_hidden +
                                btn2 + '</td>';
                        });
                        html += '</tr>';
                        html += '<tr>';
                        html += '<td>' + time.end.substring(0, time.end.length - 3) + '</td>';
                        html += '</tr>';
                    });
                    $('table tbody').html(html);
                }).fail(function() {
                    // TODO:
                });
            }

            $('[name=study_plan_edition]').on('change', function() {
                let study_plan_edition_id = $(this).val();
                loadClasses(study_plan_edition_id);

                loadDisciplines(study_plan_edition_id);
            }).trigger('change');

            $('[name=schedule_type]').on('change', function() {
                let schedule_type_id = $(this).val();
                loadTimes(schedule_type_id);
            }).trigger('change');

            $('body').on('click', '[data-toggle="modal"]', function() {
                let $self = $(this);
                let target = $self.data('target');
                let $modal = $(target);
                $modal.data('day_of_the_week', $self.data('day_of_the_week'));
                $modal.data('time', $self.data('time'));
                $modal.modal();
            });

            $('[name="buildings"]').on('change', function() {
                let $self = $(this);
                loadRooms($self.val());
            }).trigger('change');

            $('#btn-save-discipline').on('click', function() {
                let $input = $('[name="disciplines"]');

                let $self = $(this);
                let $modal = $self.parents('.modal');

                let target = '[name="disciplines[' + $modal.data('day_of_the_week') + '][' + $modal.data(
                    'time') + ']"]';
                $('[type="hidden"]' + target).val($input.val());
                $('[type="text"]' + target).val($input.find('option:selected').text());

                $modal.modal('hide');
            });

            $('#btn-save-room').on('click', function() {
                let $input = $('[name="rooms"]');

                let $self = $(this);
                let $modal = $self.parents('.modal');

                let target = '[name="rooms[' + $modal.data('day_of_the_week') + '][' + $modal.data('time') +
                    ']"]';
                $('[type="hidden"]' + target).val($input.val());
                $('[type="text"]' + target).val($input.find('option:selected').text());

                $modal.modal('hide');
            });

            $('body').on('click', '.btn-remove-discipline', function() {
                let $self = $(this);

                let target = '[name="disciplines[' + $self.data('day_of_the_week') + '][' + $self.data(
                    'time') + ']"]';
                $('[type="hidden"]' + target).val('');
                $('[type="text"]' + target).val('');
            });

            $('body').on('click', '.btn-remove-room', function() {
                let $self = $(this);

                let target = '[name="rooms[' + $self.data('day_of_the_week') + '][' + $self.data('time') +
                    ']"]';
                $('[type="hidden"]' + target).val('');
                $('[type="text"]' + target).val('');
            });


            @if ($action === 'show')
                loadTimes(schedule.schedule_type_id);
            @endif
        });
    </script>
@endsection
