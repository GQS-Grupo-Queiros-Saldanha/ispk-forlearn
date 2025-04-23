@section('title','Horário')
@extends('layouts.generic_index_new')
@section('page-title','Horário')

@section('selects')
<div class="float-right div-anolectivo" style="width: 45%; !important">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected->id == $lectiveYear->id)
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
@endsection
@section('body')
    @include('layouts.backoffice.modal_confirm')

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
      


        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

        

                <div class="row">
                    <div class="col">

                     

                    <div id="table-container">
                        
                    </div>
                    
                <div class="row float-right btn-pdf-boletim" style="margin-right: 0.1!important;">
                    <a class="btn" id="btn_pdf" style="background-color:#0082f2;" target="_blank" href="">
                    <i class="fa fa-file-pdf"></i> Horário</a> 
                </div>
                            </div>

                    </div>
                </div>
                 
                

         
               

            </div>
        </div>
    

@endsection

@section('scripts-new')
    @parent
    <script>
        $(function () {
            var lective_year = $("#lective_year").val();

            getSchedule();
            
            $("#lective_year").change(function() {                
                getSchedule();
            });

            function getSchedule(){
                
                $("#table_student").html("");

                url = '{{ route('main.get_schedule_student', ':id') }}';
                url = url.replace(':id', $("#lective_year").val());

                $.ajax({
                    url:url,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json'
                }).done(function(table){

                    href = '{{ route('schedules.student.pdf', ':id') }}';
                    href = href.replace(':id', $("#lective_year").val());

                    $("#btn_pdf").prop('href',href)
                    $("#table-container").html(table)

                })

        }

            $("#link_print").click(function() {
                // 
                // console.log(9532);
                document.getElementById("link_print").href = "https://forlearn.ispm.ao/pt/gestao-academica/print_schedule_student/"+lective_year;
                
            });

       
        });
    </script>
@endsection
