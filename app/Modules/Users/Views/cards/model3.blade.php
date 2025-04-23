@extends('layouts.print')
@section('content')
    <title>Cartão de estudante </title>


    <style>
        /* 5,4cm de altura */
        /* 8,5cm de largura */
        * {
            padding: 0;
            margin: 0;
            top: 0;
            bottom: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .panel {
            margin-left: 280px;
        }

        body {
            padding-top: 20px;
            padding-left: 20px;
            display: flex;
            justify-content: space-around;
            background-color: white;
        }

        .card-frent,
        .card-verse {
            border: 1px solid #8e9091;
            /* width: 8.5cm;
                height: 5.4cm; */
            width: 85.6mm;
            height: 53.98mm;
            border-radius: 7px;
            background-color: white;
                 z-index:2000;

        }

        .table-card {
            width: 75.6mm;
        }
        .table-card tr td{
            vertical-align: top!important; 
            text-align: left!important;  
        }

        .card-verse {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            background-image: url(profile.jpg);
            background-position: 130px 13px;
            background-size: 138px;
            background-repeat: no-repeat;
            display: none;
        }

        .logo {
            height: 58px;
            width: 61px;
            border-radius: 50px;
            margin-top: 25px;
            margin-left: 5px;
            position: fixed;
            transform: translate(10px, 25px);
            background-image: url({{'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo}});
            background-size: 69px 70px;
            background-repeat: no-repeat;
            background-position: center;
        }
        .photo-profile {
            height: 78px;
            width: 70px;
            border-radius: 5px;
            margin-top: 89px;
            margin-left: 240px;
            position: fixed;
            @if(isset($student->photo))
            background-image: url({{$student->photo}});
            @endif
            background-size: 70px 78px;
            background-repeat: no-repeat;
            background-position: center;
            background-color:white;
            z-index:2000;
        }

        .logo-verse {
            height: 40px;
            width: 40px;
            border-radius: 50px;
            border: 1px solid black;
            margin-top: 5px;
            background-color: white;
        }

        

        .text-institution {
            width: 250px;
            text-transform: uppercase;
            font-size: 11.5px;
            margin-top: 15px;
            text-align: left;
            color: #05a9d6;
            font-weight: bold;
            margin-left: 73px;
            line-height: 16px;
            /* display: flex; */
        }

        .text-decree {
            margin-top: -4px;
            text-transform: none;
            text-transform: uppercase;
            font-weight: 700;
            color: #FF9800;
            font-size: 10px;
            text-transform: none;
            width: 53px;
            /* padding: 0px; */
            margin-top: -1px;
            margin-left: 23px;
        }

        .body-card {
            width: 100%;
            display: flex;
            margin-top: 24px;
        }

        .photo-card {
            width: 110px;
        }

        .information-card {
            width: 270px;
            margin-top: 15px;
        }

        .data {
            font-size: 8px;
            color: #03A9F4;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase; 
        }
       
        .data-value {
            color: #4c5c68;
            font-size: 9px;
            font-weight: normal!important;
           
        }

        .data-space {
            margin-top: 10px;
        }

        .information-card {
            display: flex;
            padding-left: 9px;
        }

        .data{}

        .data-value {
            font-weight: normal!important;
        }

        .card-col {
            width: 58%;
        }

        .card-left {
            margin-left: 20px;
        }

        .body-card{
            
        }

        .bar {
            background-color: #1f1f1f;
            height: 30px;
            width: 100%;
            transform: translateY(0px);
            border-end-end-radius: 5px;
            border-end-start-radius: 5px;
            color: white;
            text-align: center;
            font-size: 8px; 
            padding-top: 10px;
        }

        .obs {
            color: #FF9800;
        }

        .photo-card .data,
        .photo-card .data-value {
            margin-left: 24px;
            margin-top: 0px;
        }

        .text-institution2 {
            font-size: 11.5px;
        }
        td:first-child{
            
        }
        
    .watermark {
        opacity: 0.3;
        color: BLACK;
        position: fixed;
        top: 15px;
        background-image: url("{{'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo}}");
        background-position: 65px 10px;
        background-repeat: no-repeat;
        background-size: 200px; 
        height: 700px;
        width: 700px;
    }
       
    </style>


    <div class="panel">
        <div class="card-frent">
            <div class="header-card">
                       <div class="photo-profile"></div>
                <div class="logo"></div>
                <div class="text-institution">
                    Instituto Superior<br>
                    <as class="text-institution2">Politécnico São Martinho de Lima </as>
                    <!-- <p class="text-decree">
                           Cartão de<br> estudante
                       </p> -->
                </div>
            </div>
            <div class="body-card">
                <div class="information-card">
                    <table class="table-card">
                        <thead>
                            <tr></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 160px!important;">
                                    <p class="data">
                                        Estudante<br>
                                        <p class="data-value">
                                            {{$student->student}}
                                        </p>
                                    </p>
                                </td>
                                <td style="width: 70px!important;padding-left:5px;">
                                    <p class="data">
                                        Ano<br>
                                    <p class="data-value">{{$student->course_year}}º</p>
                                    </p>
                                </td>
                                <td style="width: 10.6mm;"></td>
                            </tr>
                            <tr>
                                <td style="width: 160px!important;">
                                    <p class="data">
                                        Curso<br>
                                    <p class="data-value">
                                        {{$student->course}}
                                    </p>
                                    </p>
                                </td>
                                <td style="width: 70px!important;padding-left:5px;">
                                    <p class="data">
                                        Ano lectivo<br>
                                    <p class="data-value">
                                        {{$lt->display_name}}
                                    </p>
                                    </p>
                                </td>
                                <td style=""> 
                               
                                </td>
                            </tr>
                           <tr>
                                <td style="width: 160px!important;">
                                    <p class="data">
                                        E-mail<br>
                                    <p class="data-value">
                                        {{$student->email}}
                                    </p>
                                    </p>
                                </td>
                                 <td style="width: 70px!important;padding-left:5px;">
                                   <p class="data">
                                        Turma<br>
                                    <p class="data-value">
                                        {{$student->classe}}
                                    </p>
                                    </p>
                                </td> 
                                <td>
                                   <td > 
                                <p class="data">
                                        Matrícula<br>
                                    <p class="data-value OBS">
                                       {{$student->matricula}}
                                    </p>
                                    </p>
                                </td>
                                </td>
                            </tr>
                        </tbody>
                    </table>
             
                </div>
               
            </div>
        </div>
        <br>
        <div class="card-verse">

            <div class="body-card">
                <p class="bar">
                    INSTITUTO SUPERIOR POLITÉCNICO SÃO MARTINHO DE LIMA<br>
                    <as>Avenida Fidel de Castro Cruz | ispsml@gmail.com | (+244) 920-000-000</as><br>
                    <b class="powered">Powered by forLEARN <sup>®</sup></b>
                </p>
            </div>
        </div>
        <div class="watermark"></div>
    </div>
    
    
