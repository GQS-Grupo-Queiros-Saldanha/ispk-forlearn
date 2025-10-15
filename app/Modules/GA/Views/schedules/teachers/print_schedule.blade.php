
@section('title',__('GA::schedules.edit_schedule'))

@extends('layouts.printForSchedule')
{{--@extends('layouts.backoffice')--}}
@section('content')

    <style>
        .div-top {
            text-transform: uppercase;
            position: relative;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }
        /* DivTable.com */
        .divTable{
            display: table;
            width: 100%;
        }
        .divTableRow {
            display: table-row;
        }
        .divTableHeading {
            background-color: #EEE;
            display: table-header-group;
        }
        .divTableCell, .divTableHead {
           
            display: table-cell;
            padding: 3px 10px;
        }
        .divTableHeading {
            background-color: #EEE;
            display: table-header-group;
            font-weight: bold;
        }
        .divTableFoot {
            background-color: #EEE;
            display: table-footer-group;
            font-weight: bold;
        }
        .divTableBody {
            display: table-row-group;
        }
        .pl-1 {
            padding-left: 1rem !important;
            padding-top: 10px;
        }
        .h1-title {
            padding: 0;
            margin-bottom: 0;
            font-size: 25pt;
            padding-top:10px;
        }
        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
            float: right;
            padding-top: 30px;
        }
        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
            
        }
        .img-institution-logo {
            width: 50px;
            height: 50px;
            float: right;
            padding-top: 20px;
            height: 100px;
             width: 100px;
        }
        .item1{
            background-color:red;
        }
        .h1-name{
            padding: 0;
            margin-bottom: 0;
            font-size: 20pt;
            padding-top:15px;
            text-align: center;
        }
        .h1-tex-name-div{
            text-align: center;
            align-content: center;
        }
        .itens{
            font-size:12pt;
            color: #000;
            font-weight: bold;
        }
        .table-parameter-group {
            page-break-inside: avoid;
        }

        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }

        .tbody-parameter-group {
            border-top: 0;
            border-left: 1px solid #BCBCBC;
            border-right: 1px solid #BCBCBC;
            border-bottom: 1px solid #BCBCBC;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }
        .td-parameter-column {
            padding-left: 5px !important;
        }
        

    </style>
    <main>
        <div class="div-top">
           <div class="divTable">
            <div class="divTableBody">
            <div class="divTableRow">
            <div class="divTableCell pl-1">
                <h3 class="h1-title" style="padding-left:35px;">
                    HORÁRIO DO DOCENTE
                </h3>
            </div>
            <div class="divTableCell h1-tex-name-div">
                <h1 class="h1-name" style="color:transparent;">
                     @foreach($languages as $language)
                        <div class="tab-pane row @if($language->default) active show @endif"
                                id="language{{ $language->id }}">     
                                {{$translations[$language->id]['display_name']}}
                        </div>
                    @endforeach
                </h1>
            </div>
            <div class="divTableCell td-institution-name">
                {{-- Instituto Superior<br>Politécnico Maravilha --}}
                {{$institution->nome}}
            </div>
            <div class="divTableCell td-institution-logo">
                <img class="img-institution-logo" src="{{ asset('storage/'. $institution->logotipo) }}" alt="">
            </div>
            </div>
            <div class="divTableRow">
            <div class="divTableCell pl-1" style="width:100px; ">
                    <span style="padding-left:35px;">    
                        Documento gerado a
                        <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>    
                    </span>
             
            </div>
            </div>
            </div>
        </div>
        </div>
        @section('content')

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col">
                          <div class="card">
                            <div class="card-body">
                          
                                <table class="table table-parameter-group" border="2" style="width:250px;">
                                    <thead  class="thead-parameter-group">
                                        <tr>
                                            <th class="th-parameter-group" style="font-size:10pt;">Nome</th>
                                            <th class="th-parameter-group" style="font-size:10pt;">Curso (s) </th>
                                            <th class="th-parameter-group" style="font-size:10pt;">Ano Letivo</th>

                                            {{--<th class="th-parameter-group" style="font-size:10pt;">Semestre</th>--}}

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{Auth::user()->name}}</td>
                                            <td> 
                                                @foreach ($user->courses as $user_courses)
                                                        {{ $user_courses->currentTranslation->display_name}} 
                                                @endforeach
                                            </td>
                                            <td>{{ $lectiveYears->currentTranslation->display_name }}</td>

                                           {{--  <td>{{$schedule->period_type->currentTranslation->display_name}}</td>--}}

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        
                        <div class="card">
                            <div class="card-body">
                                    @foreach ($events_by_type as $key => $item)
                                <table class="table table-parameter-group" border="2">
                                    <thead class="thead-parameter-group">
                                        <tr>
                                            <th class="th-parameter-group" style="font-size:9pt;">@lang('GA::schedule-types.times')</th>
                                            <th class="th-parameter-group" style="font-size:9pt;">@lang('common.hours')</th>
                                            @if(!$days_of_the_week->isEmpty())
                                                @foreach($days_of_the_week as $day_of_the_week)
                                                    <th class="th-parameter-group" style="font-size:9pt;">{{ $day_of_the_week->currentTranslation->display_name }}</th>
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

                                                    @php $event = $item->where('schedule_type_time_id', $time->id)->where('day_of_the_week_id', $dayOfWeek->id)->first();
                                                        @endphp
                                                        

                                                        <td rowspan="2">{{$event ? $event->discipline->currentTranslation->display_name . " -" : ''}}  {{$event ? $event->discipline->course->code  . " -" : ''}} {{$event ? substr($event->discipline->code, -4, 1) . " Ano" : ''}} </td>
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
             let schedule = {!! $schedule !!};
                console.log(schedule);

                let user = {!! $user !!};
                console.log(user);
        });
    </script>
@endsection
