@extends('layouts.print')
@section('content')
    <link href="https://db.onlinewebfonts.com/c/0a6ee448d1bd65c56f6cf256a7c6f20a?family=Bahnschrift" rel="stylesheet">
    <title>{{ $student->student . '_' . $student->matricula }}</title>


    <style>
        /* 5,4cm de altura */
        /* 8,5cm de largura */
        * {
            padding: 0;
            margin: 0;
            top: 0;
            bottom: 0;
            font-family: "Bahnschrift";
        }

        .panel {
            margin-left: 280px;
        }

        body {
            padding-top: 15px;
            padding-left: 20px;
            display: flex;
            justify-content: space-around;
            background-color: white !important;
        }


        .card-verse {

            border: none !important;
            width: 55.6mm;

            border-radius: 7px;

        }

        .card-frent {
            background-color: #0F3782;
            border: none !important;
            width: 60.6mm;
            height: 38.6mm;

            margin-top: 10px;
        }

        .table-card {
            width: 60.6mm;
        }

        .table-card tr td {
            vertical-align: top !important;
            text-align: left !important;
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
            height: 48px;
            width: 50px;
            border-radius: 50px;
            margin-top: 28px;
            margin-left: 5px;
            position: fixed;
            transform: translate(5px, 30px);
            background-image: url({{ 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo }});
            background-size: 39px 40px;
            background-repeat: no-repeat;
            background-position: left;
        }

        .photo-profile {
            height: 70px;
            width: 70px;
            border-radius: 2px;
            margin-top: 78px;
            margin-left: 107px;
            position: fixed;

            @if (isset($student->photo))
                background-image: url({{ $student->photo }});
            @endif
            background-size: 70px 70px;
            background-repeat: no-repeat;
            background-position: center;
            background-color:white;
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
            font-size: 8px;
            margin-top: 15px;
            margin-bottom: 7px;
            text-align: left;
            color: white;
            font-weight: bold;
            margin-left: 57px;
            line-height: 16px;
            word-spacing: 1px;
            /* display: flex; */
            padding-top: 10px;

        }

        .text-decree {
            margin-top: -4px;
            text-transform: none;
            text-transform: uppercase;
            font-weight: 700;
            color: white;
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
            margin-top: 25px;
        }

        .photo-card {
            width: 110px;
        }

        .information-card {
            width: 270px;
            margin-top: 25px;

        }

        .data {
            font-size: 6.3px !important;
            color: white;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .data-value {
            color: white;
            color: black;
            font-size: 6.3px !important;
            font-weight: normal !important;
        }

        .data-space {
            margin-top: 12px;
        }

        .information-card {
            display: flex;
            padding-left: 11px;
        }

        .data {}

        .data-value {
            font-weight: normal !important;

        }

        .card-col {
            width: 58%;
        }

        .card-left {
            margin-left: 20px;
        }

        .body-card {}

        .bar {
            background-color: #1f1f1f;
            height: 10px;
            width: 100%;
            color: white;
            text-align: center;
            font-size: 8px;
            padding-top: 1px;
        }

        .obs {
            color: white;
        }

        .photo-card .data,
        .photo-card .data-value {
            margin-left: 24px;
            margin-top: 0px;
        }

        .text-institution2 {
            font-size: 12px;
            margin-left: 45px;


        }

        td {
            padding: 0px !important;
        }

        span {
            text-transform: none;
            margin-bottom: 10px;
        }

        .card-two {
            background-color: #FC791F;
            width: 47mm;
            height: 38.6mm;

        }

        .qrcode{
            position: absolute;
            left: 485px;
            top: 125px;
        }
    </style>

    <div class="panel">
        <div class="card-frent">
            <div class="card-two">
                <div class="header-card">
                    <div class="photo-profile"></div>
                    <div class="logo"></div>
                    <div class="text-institution">
                        <p style="margin-left:-8px!important">Instituto Superior Politécnico Katangoji</p>

                    </div>
                </div>
                <div>
                    <div class="information-card">
                        <table class="table-card">
                            <thead>
                                <tr></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100px!important;">
                                        <p class="data" style="font-size: 8px!important">
                                            Estudante<br>
                                            <span class="data-value">
                                                {{ $student->student }}
                                            </span>
                                        </p>
                                    </td>


                                </tr>
                                <tr>
                                    <td style="height:3px!important; margin-bottom:-10px!important"></td>
                                </tr>
                                <tr>
                                    <td style="width: 100px!important;">
                                        <p class="data">
                                            Curso<br>
                                            <span class="data-value">
                                                {{ $student->course }}
                                            </span>
                                        </p>
                                    </td>


                                </tr>
                                <tr>
                                    <td style="height:3px!important"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p class="data">
                                            E-mail<br>
                                            <span class="data-value">
                                                {{ $student->email }}
                                            </span>


                                        </p>
                                    </td>


                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:7px!important"></td>
                                </tr>
                                <td style="width: 175px!important;">
                                    <p class="data" style="text-transform: none;">
                                        Válido até:<br>
                                        <span class="data-value"> {{ $student->card_validity }}</span>


                                    </p>


                                </td>

                                <td>
                                    <p class="data" style="margin-left:-60px!important;">
                                        Matrícula<br>
                                        <span class="data-value" style="width: 40px!important;padding-left:3px;">
                                            {{ $student->matricula }}
                                        </span>
                                    </p>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="qrcode" id="qrcode">
                        <input type="text" id="codeQr" value="{{ $student->matricula }}" hidden>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        var qrcodeElement = document.getElementById("qrcode");
        var qrcodeValue = document.getElementById("codeQr").value;
        var qrcode = new QRCode(qrcodeElement, {
            text: qrcodeValue,
            width: 35,
            height: 35,
        });
    </script>
@endsection
