<style>
    .cell-forlearn {
        
        padding-left: 13x;
        padding-right: 13px;
        padding-top: 13px!important;
        padding-bottom: 13px!important;
        margin: 0px;
        border-radius: 2px;
        border: 6px solid #8eaadb;
        border-radius: 7px;
        font-size: 14px!important;
        font-weight: 600!important;
        text-align: center;
        vertical-align: center;
    }
    .table-forlearn td, .table-forlearn th {
        padding: 5px;
        padding-left: 10px;
        padding-right: 10px;
        margin: 0px;
        border-radius: 2px;
        border: 5px solid white;
        border-radius: 7px;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        vertical-align: center;
    }

    .cell-forlearn-top {
        font-size: 12px;
    }

    .bg0 {
        background-color: #2f5496 !important;
        color: white;
    }

    .bg1 {
        background-color: #8eaadb !important;
    }

    .bg2 {
        background-color: #d9e2f3 !important;
    }

    .bg3 {
        background-color: #ffbe00 !important;
        font-weight: 700!important;
        font-size: 18px!important;
    }
    .bg5 {
        background-color: #e2d664 !important;
        color: black!important;
    }
    .bg6 {
        background-color: #17fcc3 !important;
    }

    .bg4 {
        /* background-color: #00c0ef !important; */
        background-color: #d9e2f3 !important;
        padding: 5px;
        /* border-left: 6px solid #d9e2f3;
        border-right: 6px solid #d9e2f3; */

    }

    .table-forlearn {
        font-size: 12px;
        width: 96%;
        margin-left: 2%;
    }

    .img-forlearn {
        height: 13px;
        width: 75px;
    }

    .span-forlearn{
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: #ed7a31;
        color: black;
        padding: 2px 5px;
        font-size: 9px;
        text-align: right;


    }
    .span-forlearn-2{
        position: absolute;
        top: 0;
        left: 0;
        background-color: #bdd8ed;
        color: black;
        padding: 2px 5px;
        font-size: 9px;
    }
    .span-forlearn-3{
        position: absolute;
        bottom: 0;
        left: 0;
        background-color: #a9d18a;
        color: black;
        padding: 2px 5px;
        font-size: 9px;
    }
    .table-forlearn td span{
        width: 95px!important;
        text-align: left;
        border: 1px solid white;
    }
    .time-forlearn{
        width: 100px!important;  
        position: relative;
        text-align: center;
        vertical-align: center;
    }

    .table-forlearn thead tr th {
        text-align: center;
        color: black;
    }
    .table-docent thead tr th {
        padding: 3px; 
        font-weight: 500!important;
    }
    .table-docent tbody tr td {
        padding: 3px;
        font-weight: normal!important;
    }
    .table-docent tbody tr{
        border-bottom: 1px solid rgb(223, 223, 223);
    }
    .table-docent{
        font-size: 10px;
        width: 80%;
        margin-left: 10%;
    }

    .forcell{
        background-color: #fff2cd!important;
    }

    .sign-date{
        opacity: 0!important;
    }
</style>
<div class="row">
    <div class="col-12 ">
        <table class="table_te">


            <tr class="bg1">
                
                <th class="text-center">Curso</th>
                <th class="text-center">Ano Lectivo</th>
                <th class="text-center">Turma</th>
                <th class="text-center">Turno</th>
                <th class="text-center">Ano curricular</th>
            </tr>
            <tr class="bg2">
                <td class="text-center bg2">{{ $studyPlans->course->currentTranslation->display_name }}</td>
                <td class="text-center bg2">{{ $lectiveYears->currentTranslation->display_name }}</td>
                <td class="text-center bg2">{{$classe->code}}</td>
                <td class="text-center bg2">{{ $schedule->schedule_type->currentTranslation->display_name }}</td>
                <td class="text-center bg2">{{ $schedule->study_plan_edition->course_year}} º</td>
            </tr>

        </table>
    </div>
</div>
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col">

                        <div class="card" style="padding-bottom: -5%; page-break-inside: auto;">
                            <div class="card-body">
                              
                                <table class="table-forlearn">
                                    <thead class="thead-parameter-group">
                                        <tr>
                                            
                                            <th  class="cell-forlearn time-forlearn bg3" style="font-size:9pt;">@lang('common.hours')</th>
                                            @if(!$days_of_the_week->isEmpty())
                                                @foreach($days_of_the_week as $day_of_the_week)
                                                    <th  class="cell-forlearn bg3" style="font-size:9pt;">{{ $day_of_the_week->currentTranslation->display_name }}</th>
                                                    
                                                @endforeach
                                            @else
                                                ...
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($schedule->type->times as $time)
                                      <tr>
                                          
                                            <td  class="cell-forlearn forcell time-forlearn" >{{ substr($time->start, 0, -3)}}</td>
                                            @foreach ($days_of_the_week as $dayOfWeek)
                                                @php $event = $schedule->events->where('schedule_type_time_id', $time->id)->where('day_of_the_week_id', $dayOfWeek->id)->first();
                                                @endphp
                                                <td  class="cell-forlearn forcell" style="width: 300px!important; position: relative;"rowspan="2">
                                                    <span class="span-forlearn-2"> {{$event ? $event->discipline->code : '-'}} </span> 
                                                        {{$event ? $event->discipline->currentTranslation->display_name : ''}} 
                                                    <span class="span-forlearn-3"> {{ 
                                                            $event ? 
                                                                 (
                                                                isset($teacher_discipline[$event->discipline->id]) !== false ?
                                                          $teacher_discipline[$event->discipline->id][0] : 
                                                                'Não definido'
                                                            ) : 
                                                                 '-'
                                                            }} </span> 
                                                    <span class="span-forlearn"> {{$event ? $event->room->currentTranslation->display_name : '-'}} </span> </td>

                                                @if(isset($event->discipline->study_plans_has_disciplines[0]->study_plans_has_discipline_regimes[0]))
                                                    
                                                @else
                                                    
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td  class="cell-forlearn forcell time-forlearn" >{{substr($time->end, 0, -3)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
          


            </div>
        </div>
    </div>
@section('scripts')
    @parent

    
@endsection
