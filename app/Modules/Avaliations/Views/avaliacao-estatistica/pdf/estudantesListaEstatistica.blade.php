@extends('layouts.print')
@section('content')
    <link href="http://fonts.cdnfonts.com/css/calibri-light" rel="stylesheet">

    <main>
        @php
            $doc_name = 'Análise estatística - Disciplina';
            $discipline_code = '';
        @endphp
        @include('Reports::pdf_model.forLEARN_header')
        <!-- aqui termina o cabeçalho do pdf -->

        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">



                    </div>
                </div>
                <!-- personalName -->
                <br>
                <br>
                <div class="row">
                    <div class="col-12" style="margin-top:4px;">
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                @endphp

                                @foreach ($estudantes as $item)
                                    @if ($item->nota >= 0 && $item->nota < 7)
                                        <table class="table_te">
                                            <tr class="bg1">
                                                <th class="text-center" style="font-size: 15pt; padding: 0px; "
                                                    colspan="6">Escala de 0-6</th>
                                            </tr>
                                            <tr class="bg2">
                                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
                                                <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula
                                                </th>
                                                <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a)
                                                    estudante</th>
                                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>


                                            </tr>

                                            @php
                                                $i = 1;
                                            @endphp

                                            @foreach ($estudantes as $item)
                                                @if ($item->nota >= 0 && $item->nota < 7)
                                                    <tr>
                                                        <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                                                        <td class="text-center" style="">{{ $item->matricula }}</td>
                                                        <td class="text-left" style="">{{ $item->nome_completo }}</td>
                                                        <td class="text-left" style="">{{ $item->sexo }}</td>
                                                        <td class="text-left">{{ $item->nota }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </table>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                        @break
                        @endif
                        @endforeach



                        @foreach ($estudantes as $item)
                            @if ($item->nota > 6 && $item->nota < 10)
                                <table class="table_te">
                                    <tr class="bg1">
                                        <th class="text-center" style="font-size: 15pt; padding: 0px; " colspan="6">
                                            Escala de 7-9</th>
                                    </tr>
                                    <tr class="bg2">
                                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
                                        <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula</th>
                                        <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a)
                                            estudante</th>
                                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>


                                    </tr>
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach ($estudantes as $item)
                                        @if ($item->nota > 6 && $item->nota < 10)
                                            <tr>
                                                <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                                                <td class="text-center" style="">{{ $item->matricula }}</td>
                                                <td class="text-left" style="">{{ $item->nome_completo }}</td>
                                                <td class="text-left" style="">{{ $item->sexo }}</td>
                                                <td class="text-left">{{ $item->nota }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                @break
                @endif
                @endforeach




                @foreach ($estudantes as $item)
                    @if ($item->nota > 9 && $item->nota < 14)
                        <table class="table_te">

                            <tr class="bg1">
                                <th class="text-center" style="font-size: 15pt; padding: 0px; " colspan="6">
                                    Escala de 10-13</th>
                            </tr>
                            <tr class="bg2">
                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
                                <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula</th>
                                <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a) estudante
                                </th>
                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>
                            </tr>

                            @php
                                $i = 1;
                            @endphp

                            @foreach ($estudantes as $item)
                                @if ($item->nota > 9 && $item->nota < 14)
                                    <tr>
                                        <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                                        <td class="text-center" style="">{{ $item->matricula }}</td>
                                        <td class="text-left" style="">{{ $item->nome_completo }}</td>
                                        <td class="text-left" style="">{{ $item->sexo }}</td>
                                        <td class="text-left">{{ $item->nota }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
            </div>
            <br>
            <br>
            <br>
            <br>
        @break
        @endif
        @endforeach




        @foreach ($estudantes as $item)
            @if ($item->nota > 13 && $item->nota < 17)
                <table class="table_te">
                    <tr class="bg1">
                        <th class="text-center" style="font-size: 15pt; padding: 0px; " colspan="6">Escala de
                            14-16</th>
                    </tr>
                    <tr class="bg2">
                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
                        <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula</th>
                        <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a) estudante</th>
                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>


                    </tr>
                    @php
                        $i = 1;
                    @endphp

                    @foreach ($estudantes as $item)
                        @if ($item->nota > 13 && $item->nota < 17)
                            <tr>
                                <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                                <td class="text-center" style="">{{ $item->matricula }}</td>
                                <td class="text-left" style="">{{ $item->nome_completo }}</td>
                                <td class="text-left" style="">{{ $item->sexo }}</td>
                                <td class="text-left">{{ $item->nota }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
    </div>
    <br>
    <br>
    <br>
    <br>
@break
@endif
@endforeach


@foreach ($estudantes as $item)
    @if ($item->nota > 16 && $item->nota < 20)
        <table class="table_te">
            <tr class="bg1">
                <th class="text-center" style="font-size: 15pt; padding: 0px; " colspan="6">Escala de
                    17-19</th>
            </tr>
            <tr class="bg2">
                <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
                <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula</th>
                <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a) estudante</th>
                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
                <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>


            </tr>
            @php
                $i = 1;
            @endphp

            @foreach ($estudantes as $item)
                @if ($item->nota > 16 && $item->nota < 20)
                    <tr>
                        <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                        <td class="text-center" style="">{{ $item->matricula }}</td>
                        <td class="text-left" style="">{{ $item->nome_completo }}</td>
                        <td class="text-left" style="">{{ $item->sexo }}</td>
                        <td class="text-left">{{ $item->nota }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
</div>
<br>
<br>
<br>
<br>
@break
@endif
@endforeach



@foreach ($estudantes as $item)
@if ($item->nota > 19)
<table class="table_te">
    <tr class="bg1">
        <th class="text-center" style="font-size: 15pt; padding: 0px; " colspan="6">Escala de 20
        </th>
    </tr>
    <tr class="bg2">
        <th class="text-center" style="font-size: 12pt; padding: 0px; ">#</th>
        <th class="text-center" style="font-size: 12pt; padding: 0px;">Matrícula</th>
        <th class="text-center" style="font-size: 12pt; padding: 0px;">Nome do(a) estudante</th>
        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Sexo</th>
        <th class="text-center" style="font-size: 12pt; padding: 0px; ">Nota</th>


    </tr>
    @php
        $i = 1;
    @endphp

    @foreach ($estudantes as $item)
        @if ($item->nota > 19)
            <tr>
                <td class="text-center"style="width:40px;">{{ $i++ }}</td>
                <td class="text-center" style="">{{ $item->matricula }}</td>
                <td class="text-left" style="">{{ $item->nome_completo }}</td>
                <td class="text-left" style="">{{ $item->sexo }}</td>
                <td class="text-left">{{ $item->nota }}</td>
            </tr>
        @endif
    @endforeach
</table>
</div>
<br>
<br>
<br>
<br>
@break
@endif
@endforeach





</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
@endsection

<script></script>
