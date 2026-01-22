<style>
    /* Mantenho todo o CSS original */
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
        margin-bottom: 15px;
        background-color: rgb(240, 240, 240);
        background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{ $institution->logotipo }}');
        background-position: 100%;
        background-repeat: no-repeat;
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
        margin-bottom: 20px;
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

    .h1-title {
        padding: 0;
        margin-bottom: 0;
        font-size: 1.40rem;
        padding-top: 40px;
        width: 100%;
    }

    .table_te {
        background-color: #F5F3F3;
        width: 100%;
        text-align: right;
        font-family: calibri light;
        margin-bottom: 6px;
        font-size: 14pt;
    }

    .cor_linha {
        background-color: #999;
        color: #000;
    }

    .table_te th {
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
        padding: 4px !important;
        text-align: center;
        font-weight: bold;
    }

    .table_te td {
        border-left: 1px solid #fff;
        background-color: #F9F2F4;
        border-bottom: 1px solid white;
        font-size: 12pt;
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
        background-color: #fbe4d5 !important;
    }

    .bg4 {
        background-color: #f4b083 !important;
    }

    .div-top,
    .div-top table {
        height: 130px;
    }

    .table-main {
        padding-left: 10px;
    }

    .t-color {
        color: #fc8a17;
    }
    
    /* Apenas ajustes para alinhamento */
    .pp1-center {
        text-align: center;
        font-size: 2.2em;
        padding-top: 20px !important;
        margin-bottom: 5px !important;
    }
    
    .doc-name {
        text-align: right;
        padding-top: 5px !important;
        padding-right: 7px;
    }
    
    .doc-date {
        text-align: right;
        padding-right: 7px;
        padding-top: 5px !important;
        font-size: 0.9em;
    }
    
    .logo-container {
        width: 120px;
        height: 68px;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
    }
</style>

@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    $currentDate = \Carbon\Carbon::now()->format('d/m/Y');
@endphp

<div class="div-top">
    <table class="table table-main m-0 p-1 bg0" style="border:none; margin-top:-15px; margin-right:0; height:130px;">
        <tr>
            <!-- Coluna 1: Logo -->
            <td class="td-fotografia logo-container" rowspan="3"
                style="background-image:url('{{ $logotipo }}'); width:120px; height:68px; position:relative; top:10px; left:10px;">
            </td>
            
            <!-- Coluna 2: Conteúdo principal -->
            <td style="border:none; vertical-align:top;">
                <!-- Linha 1: PP1 Centralizado -->
                <div style="text-align:center; padding-top:15px;">
                    <h1 class="h1-title pp1-center" style="color:#fc8a17; margin:0; padding:0;">
                       <b>PP1</b>
                    </h1>
                </div>
                
                <!-- Linha 2: Nome do Documento -->
                <div class="doc-name">
                    <h1 class="h1-title" style="margin:0; padding:0;">
                       <b>{{ $doc_name }}</b>
                    </h1>
                </div>
                
                <!-- Linha 3: Data de Geração -->
                <div class="doc-date">
                    <span style="color:white;">
                        Documento gerado a
                        <b>{{ $currentDate }}</b>
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Nome da Instituição (mantido igual) -->
    <div class="instituto" style="position: absolute; top: 8px; left: 130px; width: 450px; font-family: Impact; padding-top: 40px; color:white;">
        <h4><b>
            @if (isset($institution->nome))
                @php
                    $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                    $new_name = explode(" ", $institutionName);
                    foreach($new_name as $key => $value) {
                        if($key == 1) {
                            echo $value."<br>";
                        } else {
                            echo $value." "; 
                        }
                    }
                @endphp
            @else
                Nome da instituição não encontrado
            @endif 
        </b></h4>
    </div>
</div>