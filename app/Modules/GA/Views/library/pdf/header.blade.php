{{-- LISTA DE MATRICULADOS --}}

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        

        body {
            font-family: 'Calibri Light', sans-serif;
        }

        html,
        body {
            padding: 0;
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;
        }

        .form-group,
        .card,
        label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

        .h1-title {
            padding: 0;
            margin-bottom: 0;
            font-size: 2em;
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
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
            /* border-left: 1px solid #BCBCBC;
                        border-right: 1px solid #BCBCBC; */
            /* border-bottom: 1px solid #BCBCBC; */
            padding: 0;
            margin: 0;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }

        .div-top {
            height: 99px;
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
                        border-bottom: 1px solid #000; */
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            /* background-image: url('https://forlearn.ispm.ao/storage/{{ $institution->logotipo }}'); */
            background-image: url('https://forlearn.ao/storage/attachment/{{ $institution->logotipo }}');
            /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); */
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 7.5%;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: justify;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
        }

        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }

        input,
        textarea,
        select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 85px;
            height: 100%;
            margin-bottom: 5px;

            background-position: 50%;
            margin-right: 8px;
        }

        .mediaClass td {
            border: 1px solid #fff;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

    </style>
</head>

<body style="margin-top: 2%;">
    <header>
        <main>
            <div class="div-top" >
                <table class="table m-0  p-0" style="padding-top: 10px!important">
                    <tr class="mt-3">

                        <td rowspan="12" style=" width:12px;">
                        </td>

                        <td class="">
                            <br>

                            <h1 class="h1-title " style="transform: translateY(10px)!important">
                                @if (isset($titulo_documento))
                                    {{ $titulo_documento }}
                                    
                                @else
                                    Sem título
                                @endif
                            </h1>
                        </td>
                    </tr>


                    <tr>
                        <td class="">
                            <span class="" rowspan="1">

                                @if (isset($requisicao[0]->data_requisicao))
                                    {{ $documentoGerado_documento }}<b>{{ $requisicao[0]->data_requisicao }}</b>
                                @endif

                                @if (isset($requisicao[0]->data_inicio))
                                    {{ $documentoGerado_documento }}<b> {{ $requisicao[0]->data_inicio }}</b>
                                @endif

                                @if (isset($requisicao[0]->data_inicio) || isset($requisicao[0]->data_requisicao))
                                @else
                                    {{ $documentoGerado_documento }}<b>
                                        {{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                                @endif


                            </span>
                        </td>
                    </tr>
                </table>
                <div
                    style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;">
                    <h4><b>
                            @if (isset($institution->nome))
                                {{ $institution->nome }}
                            @else
                                Instituição sem nome
                            @endif
                        </b></h4>
                </div>
            </div>
        </main>
    </header>
</body>

</html>
