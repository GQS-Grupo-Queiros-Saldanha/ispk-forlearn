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

<div class="card">
                            <div class="card-body">                 
                                    <br>
                                    <!-- style="border:1px solid black" -->
                                    @foreach ($events_by_type as $key => $item)
                                <table class="table-forlearn" >
                                    <thead class="thead-parameter-group">
                                        <tr>
                                            <th class="cell-forlearn time-forlearn bg3 style="font-size:9pt;">@lang('GA::schedule-types.times')</th>
                                            <th class="cell-forlearn time-forlearn bg3" style="font-size:9pt;">@lang('common.hours')</th>
                                            @if(!$days_of_the_week->isEmpty())
                                                @foreach($days_of_the_week as $day_of_the_week)
                                                    <th class="cell-forlearn bg3" style="font-size:9pt;">{{ $day_of_the_week->currentTranslation->display_name }}</th>
                                                    <!-- <th class="th-parameter-group" style="font-size:9pt;">@lang('GA::rooms.room')</th> -->
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
                                                <td class="cell-forlearn forcell time-forlearn" rowspan="2" style="text-align:center">{{$time->currentTranslation->display_name}}</td>
                                                    <td class="cell-forlearn forcell time-forlearn">{{ substr($time->start, 0, -3)}}</td>
                                                    @foreach ($days_of_the_week as $dayOfWeek)
                                                    @php $event = $item->where('schedule_type_time_id', $time->id)->where('day_of_the_week_id', $dayOfWeek->id)->first();
                                                    @endphp
                                            
                                                    <td class="cell-forlearn forcell time-forlearn" style="width: 300px!important; position: relative;"rowspan="2">
                                                         <span class="span-forlearn-2"> {{$event ? $event->discipline->code : '-'}} </span>
                                                         {{$event ? $event->discipline->currentTranslation->display_name: ''}}  
                                                         <span class="span-forlearn-3"> 
                                                         {{ 
                                                            $event ? 
                                                                 (
                                                                isset($teacher_discipline[$event->discipline->id]) !== false ?
                                                          $teacher_discipline[$event->discipline->id][0] : 
                                                                'Não definido'
                                                            ) : 
                                                                 '-'
                                                            }}

                                                            
                                                            </span> 
                                                         <span class="span-forlearn"> {{$event ? $event->room->currentTranslation->display_name : '-'}} </span>
                                                    </td>

                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td class="cell-forlearn forcell time-forlearn">{{substr($time->end, 0, -3)}}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                           

                        </div>