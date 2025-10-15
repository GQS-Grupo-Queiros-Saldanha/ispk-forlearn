@switch($action)
    @case('create') @section('title',__('GA::schedules.create_schedule')) @break
    @case('show') @section('title',__('schedule')) @break
    @case('edit') @section('title',__('GA::schedules.edit_schedule')) @break
@endswitch

@extends('layouts.backoffice')

@section('styles')
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

@section('content')
    @include('layouts.backoffice.modal_confirm')

    <div class="content-panel" style="padding: 0px;">
        @include('GA::schedules.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Horários</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('GA::schedules.create_schedule') @break
                                @case('show') HORÁRIOS @break
                                @case('edit') @lang('GA::schedules.edit_schedule') @break
                            @endswitch
                        </h1>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="float-right div-anolectivo" style="width: 45%; !important">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected == $lectiveYear->id)
                                        <option value="{{ $lectiveYear->id }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>            
                                    @else 
                                        <option value="{{ $lectiveYear->id }}">
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @endif
                                @endforeach                                        
                            </select> 
                                                       
                        </div>                         
                    </div>

                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

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
                            <div class="col-6">    
                                <a href="#" class="btn btn-sm btn-info mb-3" target="_blank" id="link_print">
                                    @icon('fas fa-file-pdf')
                                    Gerar PDF
                                </a>
                            </div>
                            @break
                        @endswitch

                            

                        <div class="card">
                            <div class="card-body">                                 
                                    <br>
                                    @foreach ($events_by_type as $key => $item)
                                    <table class="table table-parameter-group" border="2">
                                        <thead class="thead-parameter-group">
                                            <tr>
                                                <th class="th-parameter-group" style="font-size:9pt;">@lang('GA::schedule-types.times')</th>
                                                <th class="th-parameter-group" style="font-size:9pt;">@lang('common.hours')</th>
                                                @if(!$days_of_the_week->isEmpty())
                                                    @foreach($days_of_the_week as $day_of_the_week)
                                                        <th class="th-parameter-group" style="font-size:9pt;"> 00Ab{{ $day_of_the_week->currentTranslation->display_name }}</th>
                                                        <th class="th-parameter-group" style="font-size:9pt;">@lang('GA::rooms.room')</th>
                                                    @endforeach
                                                @else
                                                    ...
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            @foreach($schedule_types->where('id', $key) as $type)

                                                @foreach ($type->times as $time)

                                                <tr>
                                                <td rowspan="2" style="text-align:center">{{$time->currentTranslation->display_name}}</td>
                                                    <td >{{ substr($time->start, 0, -3)}}</td>
                                                    @foreach ($days_of_the_week as $dayOfWeek)

                                                    @php 
                                                        $event = $item->where('schedule_type_time_id', $time->id)
                                                        ->where('day_of_the_week_id', $dayOfWeek->id)->first();
                                                
                                                    @endphp
                                                        

                                                        <td rowspan="2">{{$event ? $event->discipline->currentTranslation->display_name . " -" : ''}}  {{$event ? $event->discipline->course->code  . " -" : ''}} {{$event ? substr($event->discipline->code, -4, 1) . " Ano" : ''}}</td>
                                                        <td rowspan="2" style="text-align:center">{{$event ? $event->room->currentTranslation->display_name : ''}}</td>


                                                    {{--@foreach ($schedule->events as $event)
                                                            @if($event->day_of_the_week_id === $dayOfWeek->id && $event->schedule_type_time_id === $time->id)
                                                            
                                                               {{--  @foreach ($user->disciplines as $item) 
                                                                    @if($event->discipline->id == $item->id)
                                                                        @php $var = true; @endphp
                                                                        <td rowspan="2">{{$event->discipline->currentTranslation->display_name}}</td>  
                                                                    @endif
                                                                @endforeach

                                                            @endif
                                                    @endforeach--}}

                                                    {{--@foreach ($schedule->events as $event)
                                                        @if($event->day_of_the_week_id === $dayOfWeek->id && $event->schedule_type_time_id === $time->id)
                                                        
                                                                    @foreach ($user->disciplines as $item)
                                                                         @if ($event->discipline->id == $item->id)
                                                                            @php $var2 = true; @endphp
                                                                            <td rowspan="2" style="text-align:center">{{$event->room->currentTranslation->display_name}}</td>
                                                                        @endif
                                                                    @endforeach

                                                                    
                                                                @endif

                                                    @endforeach--}}

                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td >{{substr($time->end, 0, -3)}}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            @endforeach
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
        $(function () {
            var lective_year = $("#lective_year").val();
            
            $("#lective_year").change(function() {                
                lective_year = $("#lective_year").val();                
                
                $("#schedules-table").DataTable().destroy();
                
                console.log(lective_year);
                // location.reload();
                window.location.href = 'https://dev.forlearn.ao/pt/gestao-academica/schedule_teacher/'+lective_year;
                // get_schedule(lective_year);

            });

            $("#link_print").click(function() {
                // 
                console.log(9532);
                document.getElementById("link_print").href = "https://dev.forlearn.ao/pt/gestao-academica/print_schedule_teacher/"+lective_year;
                
            });

            @if($action === 'edit')
                let schedule = {!! $schedule !!};
                // console.log(schedule);

                let user = {!! $user !!};
                // console.log(user);
                

            @endif

            @if($action === 'show')
                let schedule = {!! $schedule !!};
                // console.log(schedule);

                let user = {!! $user !!};
                // console.log(user);            
                
            @endif
        });
    </script>
@endsection
