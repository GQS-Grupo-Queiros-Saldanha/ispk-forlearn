
@if(isset($documentoCode_documento))
{{-- LISTA DE MATRICULADOS --}}
     <style>
       @font-face{
         font-family: 'Calibri Light';
         src: url('{{asset('fonts/calibril.ttf')}}');
     }
     </style>
@if ($documentoCode_documento == 1)
    <!doctype html>
    <html>
        <head>
            <meta charset="UTF-8">
            <style>

                     
        
                body{
                    font-family: 'Tinos', serif;
                }     
                html, body {
                    padding:0;
                }     
                .table td,
                .table th {
                    padding: 0;
                    border: 0;
                }     
                .form-group, .card, label {
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
                    font-size:2em;
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
                    height:99px;
                    text-transform: uppercase;
                    position: relative;
                    /* border-top: 1px solid #000;
                    border-bottom: 1px solid #000; */
                    margin-bottom: 15px;
                    background-color: rgb(240, 240, 240);
                    background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
                    /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); */
                    background-position: 100%;
                    background-repeat: no-repeat;
                    background-size: 10%;
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
                input, textarea, select {
                    display: none;
                }     
                .td-fotografia {
                    background-size: cover;
                    padding-left: 10px !important;
                    padding-right: 10px !important;
                    width:85px;
                    height:100%;
                    margin-bottom: 5px;

                    background-position:50%;
                    margin-right:8px;
                }
                .mediaClass td{
                    border: 1px solid #fff;     
                }
                .pl-1 {
                    padding-left: 1rem !important;
                }
                table { page-break-inside:auto }
                tr    { page-break-inside:avoid; page-break-after:auto }
                thead { display:table-header-group }
                tfoot { display:table-footer-group }
        
            </style>
        </head>

        <body>
            <header>
                <main>
                    <div class="div-top" style="">
                        <table class="table m-0 p-0">
                            <tr>
                                </td>
                                <td rowspan="12" style=" width:12px;">                                    
                                </td>
                                <td class="">
                                    <h1 class="h1-title">
                                        @if(isset($titulo_documento))                                        
                                            {{$titulo_documento}}
                                        @else 
                                            Sem título
                                        @endif
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="">
                                    <span class="" rowspan="1">
                                        @isset($lectiveYears)
                                        @foreach ($lectiveYears as $anoLectivo)
                                        <b> 
                                            <b>{{$anoLectivo_documento}} {{$anoLectivo->currentTranslation->display_name}}</b>
                                        </b>
                                        <b>
                                            @break
                                            @endforeach
                                    @endisset            
                                        </b>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="">
                                    <span class="" rowspan="1" style="color:rgb(240, 240, 240);">
                                        <b> 
                                            <b>-----------------</b>
                                        </b>
                                        <b> </b>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="">
                                    <span class="" rowspan="1">
                                        @if(isset($documentoGerado_documento))                                        
                                            {{$documentoGerado_documento}}
                                        @else 
                                            Documento sem data
                                        @endif
                                        <b>
                                            <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                                        </b>
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                            <h4><b>
                                @if(isset($institution->nome))                                        
                                    {{$institution->nome}} 
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
@endif

{{-- HORÁRIO DA TURMA --}}
@if ($documentoCode_documento == 2)
    <style>
        .div-top {
            text-transform: uppercase;
            position: relative;

            margin-bottom: 2px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}'); 
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 6.5%;
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
            padding: 3px 8px;
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
            font-size: 23pt;
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
        <div class="div-top" style="height:87px;">
            <div class="divTable">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell ">
                            <h1 class="h1-title" style="">
                                @if(isset($titulo_documento))                                        
                                    {{$titulo_documento}}
                                @else 
                                    Sem título
                                @endif
                            </h1>
                        </div>
            
                        <h5 class="dados_Turma">
                            <style>.dados_Turma{left:-180px; margin-left:-20; position: relative; }</style>
                            @foreach($languages as $language)
                                <div class="tab-pane row @if($language->default) active show @endif"
                                        id="language{{ $language->id }}">     
                                        {{$translations[$language->id]['display_name']}}
                                </div>
                            @endforeach
                        </h5>
            
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell "style=" top:-2px; position:relative;">
                            <span >
                                @if(isset($documentoGerado_documento))                                        
                                    {{$documentoGerado_documento}}
                                @else 
                                    Documento sem data.
                                @endif    
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>    
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>    
@endif

{{-- ANÚNCIO DE VAGAS --}}
@if ($documentoCode_documento == 3)
    <style>

        
        body{
            font-family: 'Tinos', serif;
        }
        html, body {
            padding:0;
        }
        .table td,
        .table th {
            padding: 0;
            border: 0;
        }
        .form-group, .card, label {
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
            font-size:2em;
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
            height:99px;
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 9%;
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
        input, textarea, select {
            display: none;
        }
        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width:85px;
            height:100%;
            margin-bottom: 5px;
            background-position:50%;
            margin-right:8px;
        }
        .mediaClass td{
            border: 1px solid #fff;
        }
        .pl-1 {
            padding-left: 1rem !important;
        }
        table { page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }

    </style>
    <main>
        <div class="div-top" style="">
            <table class="table m-0 p-0">
                <tr>
                    </td>
                        <td rowspan="12" style=" width:12px;">
                    </td>
                    <td class="">
                        <h1 class="h1-title">
                            @if(isset($titulo_documento))                                        
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1">
                        @foreach ($lectiveYears as $lectiveYear_T)
                        <b> 
                            <b>{{$anoLectivo_documento}} {{$lectiveYear_T->currentTranslation->display_name}}</b>
                            </b>
                            <b>
                                @break
                                @endforeach
                            </b>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1" style="color:rgb(240, 240, 240);">
                        <b> <b>-----------------</b></b>
                            <b>
                            </b>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1">
                            @if(isset($documentoGerado_documento))                                        
                                {{$documentoGerado_documento}}
                            @else 
                                Sem título
                            @endif
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>            
            <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome)) 
                            {{$institution->nome}}
                        @else 
                            Instituição sem nome
                        @endif
                </b></h4>
            </div>
        </div>
    </main>

@endif

{{-- PERCURSO ACADÉMICO --}}
@if ($documentoCode_documento == 4)

    <style>

       

        body{
            font-family: 'Tinos', serif;
        }
        html, body {
            padding:0;
        }
        .table td,
        .table th {
            padding: 0;
            border: 0;
        }
        .form-group, .card, label {
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
            font-size:2em;
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
            height:99px;
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 10%;
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
        input, textarea, select {
            display: none;
        }
        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width:85px;
            height:100%;
            margin-bottom: 5px;

            background-position:50%;
            margin-right:8px;
        }
        .mediaClass td{
            border: 1px solid #fff;
        }
        .pl-1 {
            padding-left: 1rem !important;
        }
        table { page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }

    </style>
    <main>
        <div class="div-top" style="">
            <table class="table m-0 p-0">
                <tr>
                    <td class="td-fotografia " rowspan="12" 
                        @foreach( $userFoto->parameters as $parameter)
                            @if($parameter->code === 'fotografia')
                                style="background-image:url('{{ asset('storage/attachment/' . $parameter->pivot->value) }}');width:100px; height:78px;"
                            @endif
                        @endforeach
                    > 
                    </td>
                    <td rowspan="12" style=" width:12px;"></td>
                    <td class="">
                        <h1 class="h1-title">
                            @if(isset($titulo_documento))
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1">
                            <b>
                                <b>{{$mediaFinal_documento}} </b>
                            </b>
                            <b>
                            </b>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1" style="color:rgb(240, 240, 240);">
                            <b> 
                                <b>-----------------</b>
                            </b>
                            <b>
                            </b>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1">
                            @if(isset($documentoGerado_documento))                                        
                                {{$documentoGerado_documento}}
                            @else 
                                Documento sem data
                            @endif
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>
            <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome))                                        
                        {{$institution->nome}}
                    @else 
                        Instituição sem nome
                    @endif
                </b></h4>
            </div>
        </div>
    </main>

@endif

{{-- FICHA DE CANDIDATO A ESTUDANTE --}}
@if ($documentoCode_documento == 5)

    <style>
        html, body {
            font-size: 10pt;
            padding:0;
        }
        body {
            padding:0;
            font-family: Montserrat, sans-serif;
        }  
        .table td,
        .table th {
            padding: 0;
            border: 0;
        }
        .form-group, .card, label {
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
            font-size:1.9em;
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
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 5px;
            background-color: rgb(240, 240, 240);        
            
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 8.5%;
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
        input, textarea, select {
            display: none;
        }
        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height:100%;
            margin-bottom: 5px;
            background-position:50%;
        }
        .pl-1 {
            padding-left: 1rem !important;
        }

    body {
        font-family: 'Tinos', serif;
    }

    .form-group {
        /* margin-bottom: 1px; */
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }

    .h1-title {

        padding: 0;
        margin-bottom: 0;
        /* font-size: 14pt; */
        font-size: 1.50rem;
        padding-top: 10px;
        /* background-color:red; */
        /* font-weight: bold; */
        width: 100%;

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

    .thead-parameter-group {
        color: white;
        background-color: #3D3C3C;
    }

    .th-parameter-group {
        padding: 2px 5px !important;
        font-size: .625rem;
    }

    .div-top {
        position: relative;
        margin-bottom: 5px;
        background-color: rgb(240, 240, 240);
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 10%;
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
        width: 70px;
        height: 100%;
        margin-bottom: 5px;
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
    <main>
    

        <div class="div-top" style="height:110px;">


    <table class="table m-0 p-0 " style="border:none;">
        <tr>
            <td class="td-fotografia " rowspan="12"
                style="background-image:url('{{$logotipo}}');width:87px; height:98px; background-size:70%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;padding-right:15px;top:5px;position:relative;left: 10px;
                        ">

            </td>

        </tr>

        <tr>
            <td class="td-fotografia " rowspan="12" style=" width:430px; height:78px;border:none;"></td>

        </tr>
        

        <td class="" style="padding-left:20px;padding-top: 8px;font-weight: 900;"  >
            <h1 class="h1-title" ><b>
                @if(isset($titulo_documento)) 
                    {{$titulo_documento}}
                @else 
                    Sem título
                @endif
                @if(!empty($user->roles))
                    {{ $user->roles->first()->currentTranslation->display_name }}
                @else  @endif</b>
            </h1>
        </td>
        <tr>
            <td class="" style="padding-left:20px;font-weight: 900;" rowspan="1">
                @if(isset($documentoGerado_documento))
                    {{$documentoGerado_documento}}
                @else 
                    Documento sem data
                @endif
                <b>{{ $date_generated }}</b>
            </td> 
            <td class="td-fotografia " rowspan="12"
                @isset($user->image)    
                style="background-image:url('{{$fotografia}}');width:100px; height:98px; background-size:100%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;right:50px;top:-35px;position:relative;left: 10px;
                        "                        @endisset
                        >

            </td>
            <td style="color: transparent;">ss</td>
        </tr>


    </table>





    <div style="position: absolute; top: 8px; left: 125px; width: 350px; font-family: Impact; padding-top: 12px;">
        <h4>
            <b>
                @if(isset($institution->nome))                            
                        @php
                            $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                            $new_name = explode(" ",$institutionName);
                            foreach( $new_name as $key => $value ){
                                if($key==1){
                                    echo $value."<br>";
                                }else{
                                    echo $value." "; 
                                }
                            } echo "";
                        @endphp
                @else 
                    Instituição sem nome
                @endif
            </b>
        </h4>
    </div>
</div>

    
    </main>

@endif

{{--BOLETIM DE MATRÍCULA--}}
@if ($documentoCode_documento == 6)

    <style>
        html, body {

        }
        body {
            font-family: Montserrat, sans-serif;
        }
        .table td,
        .table th {
            padding: 0;
            border: 0;
        }
        .form-group, .card, label {
            display: block !important;
        }
        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }
        .h1-title {        
            padding:0;
            margin-bottom: 0;
            font-size:1.9em;
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
        .div-top {
            text-transform: uppercase;
            position: relative;        
            /* border-top: 1px solid #000; */
            /* border-bottom: 1px solid #000; */
            /* margin-bottom: 25px; */
            margin-bottom: 2px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            /* background-image: url('/img/CABECALHO_CINZA01GRANDE.png'); */
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 9%;
        }
        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
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
        input, textarea, select {
            display: none;
        }
        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }
        .pl-1 {
            padding-left: 1rem !important;
        }
    </style>

    <main>

        <div class="div-top" style="height:80px;">
            <table class="table m-0 p-0">                    
                <tr>
                    @if ($photo)
                        <td class="td-fotografia" rowspan="12"
                            style="background-image: url('{{ asset('storage/attachment/' . $photo) }}'); width:100px;height:76px; background-position:50%;">
                        </td>                    
                    @endif
                    <td class="" style=" padding-top:16px;">
                        <h1 class="h1-title">
                            @if(isset($titulo_documento))
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="data_bMatricula" rowspan="4">
                        <style>.data_bMatricula{background-color:transparent;}</style>
                        @if(isset($documentoGerado_documento))  
                            {{$documentoGerado_documento}}
                        @else 
                            Documento sem titulo
                        @endif
                        <b>{{ Carbon\Carbon::parse($matriculation_generated_date)->format('d/m/Y') }}</b>
                    </td>
                </tr>
            </table>
            a
            <div style="position: absolute; top: 8px; right: 100px; width: 310px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome))                             
                        {{$institution->nome}}
                    @else 
                        Instituição sem nome
                    @endif
                </b></h4>
            </div>
        </div>

    </main>    

@endif

{{-- GERAR LISTA DE PENDENTES --}}
@if ($documentoCode_documento == 7)

    <style>
        
        body{
            font-family: 'Tinos', serif;                                         
        }
        .div-top {     
            font-family: 'Advent Pro', sans-serif;
            color:grey;
            text-transform: uppercase;
            position:50px;
            margin-bottom:2px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            /* background-image: url('{{ asset('img/CABECALHO_CINZA01.png') }}');  */
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 8%;
            padding-left: 3px;
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
            /* background-color: red; */
            width: 500px;
            /* height: 30px; */
            float: right;
            padding: 0;
            margin: 0;
            height: 63px; 
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
        table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
        }
        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }
        .tbody-parameter-group {
            border-top: 0;
            border-left: 1px solid #fff;
            border-right: 1px solid #fff;
            border-bottom: 1px solid #fff;
        }
        .thead-parameter-group {
            color: white;
            background-color: #6C7AE0;
            text-align: center;
        }
        .th-parameter-group {
            padding: 7px 6px !important;
            font-size: 10.5pt !important;
            font-family: Arial, Helvetica, sans-serif;

        }
        .td-parameter-column {
            padding-left: 5px 5px !important;
            border-left: 1px solid #fff;
            border-right: 1px solid #fff;
            6ufont-family: 'Tinos', serif;    

        }
        .tfoot {
            /*border-right: 1px solid #BCBCBC !important;
            border-left: 1px solid #BCBCBC !important;*/
            border-bottom: 1px solid #BCBCBC !important;
            text-align: right;
        }
        .td-of-th {
            text-align: center;
            background-color: #EEE;
        }
        table { page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group ;color:#000;}

    </style>
    <main>
        <div class="div-top" style="height:80px; min-height:70px;  ">
            <table class="table m-0 p-0">
                <tr>
                    <!--<td class="td-fotografia" rowspan="3"-->
                    <!--              style="background-image: ; width:100px; height:130px;">-->
                    <!--</td>-->
                    <td class="pl-1">
                        <h1 class="h1-title" style="padding-top:10px; color:#444;  font-family: Montserrat, sans-serif;">
                            @if(isset($titulo_documento))
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                    <!--<td class="td-institution-name" rowspan="2">-->
                    <!--    Instituto Superior<br>Politécnico Maravilha-->
                    <!--</td>-->
                    <!--<td class="td-institution-logo" rowspan="2">-->
                    <!--    <img class="img-institution-logo" src="{{ asset('img/CABECALHO_CINZA01.png') }}" style="height:67px;" alt="">-->
                    <!--</td>-->
                </tr>
                <tr>
                    <td class="pl-1" style="padding-bottom: 20px;">
                        <b>
                            @if(isset($documentoGerado_documento))
                                {{$documentoGerado_documento}}
                            @else 
                                Documento sem data
                            @endif
                        </b>
                        @if ($date2 != null)
                            {{ date('d-m-Y', strtotime($date1)) }} - {{ date('d-m-Y', strtotime($date2)) }}
                        @else
                            {{ date('d-m-Y', strtotime($date1)) }}
                        @endif
                    </td>
                </tr>
            </table>  
            <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome))                            
                        {{$institution->nome}}
                    @else 
                        Instituição sem nome
                    @endif
                </b></h4>
            </div>            
        </div>
    </main>

@endif

{{-- FOLHA DE CAIXA --}}
@if ($documentoCode_documento == 8)

    <style>        
        
                
        body{

        }
        .div-top {
            font-family: 'Advent Pro', sans-serif;
            color:grey;
            text-transform: uppercase;
            position:50px;
            margin-bottom:2px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            background-position: 100%;
            background-repeat: no-repeat;
            background-size:5.5%;
            padding-left: 3px;
        }
        .divTable{
            display: table;
            width: 100%;
        }
        .divTableRow {
            display: table-row;
        }
        .divTableHeading {
            background-color:#EEE;
            display: table-header-group;
        }
        .divTableCell, .divTableHead {

            display: table-cell;
            padding: 3px 10px;
        }
        .divTableHeading {
            background-color: #EEE;
            display: table-header-group;
            font-family:calibri light; 
        }
        .divTableFoot {
            background-color: #EEE;
            display: table-footer-group;
            /*font-weight: bold;*/
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
            padding-top:5px;
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
            width: 40%;
        }
        .img-institution-logo {
            /* background-color: red; */
            width: 500px;
            /* height: 30px; */
            float: right;
            padding: 0;
            margin: 0;
            height: 63px;     
        }
        .item1{
            /* background-color:red; */
        }
        .h1-name{
            padding: 0;
            margin-bottom: 0;
            font-size: 15pt;
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
            font-family:calibri light; 
        }

        table, tr, td, th, tbody, thead, tfoot {
            text-align: justify
        }
        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }
        .tbody-parameter-group {
            border-top: 0;
            text-align: justify;
            padding: 0;
        }
        .thead-parameter-group {
            color: white;
            background-color: #2c2c2c;
            text-align: center;
        }
        .th-parameter-group {
            /* Colocando o tamanho ficou reduzido */
            padding: 0!important;
            padding-left: 3px;
            font-size: 10.5pt !important;
            font-family: calibri ligth;
            font-family: 'Advent Pro', sans-serif;
        }
        .td-parameter-column {
            padding-left: 3px !important;
            margin:0;
            padding: 0!important;
            font-family:calibri light; 
        }
        .tfoot {
            border-bottom: 1px solid #BCBCBC !important;
            text-align: right;
        }
        .td-of-th {
            text-align: justify;
            background-color: #EEE;
            font-size:12pt;
        }
    </style>
    <main>
        <div class="div-top" style="height:57px; min-height:70px;  ">
            <table class="table m-0 p-0">
                <tr>
                    <td class="" style="">
                        <h1 class="h1-title" style="padding-top:3px; color:#444;  font-family: Montserrat, sans-serif;">
                            @if(isset($titulo_documento))
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                    {{--<td class="td-fotografia" rowspan="3" style="background-color:transparent; float: right;">
                    Instituto Superior<br>Politécnico Maravilha
                    </td>                    
                    --}}
                    {{-- <td class="td-institution-logo" rowspan="2" style="">
                        <img class="img-institution-logo" src="{{ asset('img/CABECALHO_CINZA01.png') }}" style="height:67px;" alt="">
                    </td>  --}}
                </tr>
                <tr>
                    <td class="" style="font-family: calibri light;font-weight:bold; font-size:11pt;">
                        <b>
                            @if(isset($documentoGerado_documento))                                    
                                {{$documentoGerado_documento}}
                            @else 
                                Documento sem data
                            @endif
                        </b>
                        @if ($date2 != null)
                            {{ date('d-m-Y', strtotime($date1)) }} - {{ date('d-m-Y', strtotime($date2)) }}
                        @else                                                            
                            {{ date('d-m-Y', strtotime($date1)) }}
                        @endif
                    </td>
                </tr>
            </table>
            <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome))                                                        
                        {{$institution->nome}}
                    @else 
                        Instituição sem nome
                    @endif
                </b></h4>
            </div>
        </div>
    </main>

@endif

{{-- CONTA CORRENTE --}}
@if ($documentoCode_documento == 9)

    
    <style>
         

        html,
        body {}
        body {
            font-family: Montserrat, sans-serif;
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
            margin-left: 35px;
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
        .div-top {
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 5px;
            background-color: rgb(240, 240, 240);

            background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{$institution->logotipo}}');
            /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); */
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 8.5%;
        }
        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
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
        .header-user {
            padding: 0 !important;
            text-align: left !important;
        }
    </style>

    {{-- @php $now = \Carbon\Carbon::now(); @endphp --}}

    <main>
        <div class="div-top" style="height:135px; height:80px;">
            <table class="table">
                <tr>
                    {{-- <td class="td-fotografia" rowspan="3" style="background-image: ; width:100px; height:130px;"> --}}
                    </td>
                    <td class=""  style=" padding-top:16px;">
                        <h1 class="h1-title" >
                            @if(isset($titulo_documento))
                                {{$titulo_documento}}
                            @else 
                                Sem título
                            @endif
                        </h1>
                    </td>
                    {{-- <td class="td-institution-name" rowspan="2">
                        Instituto Superior<br>Politécnico Maravilha
                    </td>
                    <td class="td-institution-logo" rowspan="2">
                        <img class="img-institution-logo" src="{{ asset('img/logo.jpg') }}" alt="">
                    </td>--}}
                </tr>
                <tr>
                    <td class="" style="padding-bottom: 20px; padding-left:40px;">
                        <b>
                            @if(isset($documentoGerado_documento))                                    
                                {{$documentoGerado_documento}}
                            @else 
                                Documento sem data
                            @endif
                            {{ date('d/m/Y') }} 
                        </b>
                    </td>
                </tr>
            </table>
            
            <div style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 15px;"> 
                <h4><b>
                    @if(isset($institution->nome))                                                                
                        {{$institution->nome}}
                    @else 
                        Instituição sem nome
                    @endif
                </b></h4>
            </div>
        </div>
    </main>

@endif






@if ($documentoCode_documento == 10)                        
    
    <style>
                    
        body{
            font-family: 'Tinos', serif;                                         
        }
        .form-group {
            /* margin-bottom: 1px; */
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }            
        .h1-title {

            padding: 0;
            margin-bottom: 0;
            /* font-size: 14pt; */
            font-size: 1.50rem;
            padding-top:10px;
            /* background-color:red; */
            /* font-weight: bold; */
            width: 100%;
           
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
        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }
        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }
        .div-top {
            position: relative;
            margin-bottom: 5px;
            background-color: rgb(240, 240, 240);
            /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');      */
            /* background-image:url('{{$logotipo}}');     */
            /* background-image: url({{asset('/storage/images/backgrounds/{$institution->logotipo}')}}); */
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 10%;
        }
        input, textarea, select {
            display: none;
        }
        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }
        .pl-1 {
            padding-left: 1rem !important;
        }
        table     { page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }

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

    </style>

    <div class="div-top bg0" style="height:110px;" > 
        
        
        <table  class="table m-0 p-0 " style="border:none;">
            <tr>
                <td class="td-fotografia " rowspan="12"
                        style="background-image:url('{{$logotipo}}');width:100px; height:78px; background-size:100%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;padding-right:12px;top:-5px;position:relative;
                        "> 
            </td>
        
            </tr>

            <tr>
               <td  class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
            
            </tr>
            <tr >
               <td  class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td> 
            </tr>
     

            <tr>
                <td class="" style="border:none;">
                    <h1 class="h1-title text-white" style="font-weight:bold;padding-left: 20%;">
                         PAUTA DE {{ mb_strtoupper($discipline_name, 'UTF-8') }}  
                        <!--Pauta de {{ mb_strtoupper("Prática Diplomáticas, Protocolares e Cerimoniais", 'UTF-8') }} -->
                    </h1>
                </td>
            </tr>
            <tr>
                <td class="" style="border:none;">
                    
                    <span class=" text-white" rowspan="1" style=font-weight:bold;padding-left:20%;top:-20px;position:relative;">
                        Documento gerado a 
                        <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                    </span>
                
                </td>
            </tr>

        </table>
      
        



        <div style="position: absolute; top: 8px; left: 110px; width: 350px; font-family: Impact; padding-top: 15px;"> 
            <h4><b>
                @if(isset($institution->nome)) 
                @php  
                    $institutionName= mb_strtoupper($institution->nome, 'UTF-8');
                @endphp   
                {{$institutionName}}                                    
                @else 
                    Nome da instituição não encontrado
                @endif
            </b></h4>
        </div>

    </div>

    <table class="table_te">
        <style>
            .table_te {background-color: #F5F3F3; !important ;width:100%;text-align:right;font-family:calibri light; margin-bottom: 6px; }
                
            .table_pauta {background-color: #F5F3F3; !important ;width:100%;text-align:right;font-family:calibri light; margin-bottom: 6px;border:none; border-left:1px solid #fff;border-bottom: 1px solid #fff;}
            .table_te  th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center;}
            .table_pauta  th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center;}
            .table_te td{border-left:1px solid #fff;background-color:#F9F2F4; } 
            .table_pauta td{border-left:1px solid #fff;background-color:#F9F2F4; }
            .table_pauta tr{border-bottom:1px solid #fff; } 
            .table_pauta  thead{}
            .tabble  thead{ }
            #corpoTabela tr td{ font-size: 12pt; }
            #chega th{ font-size: 13pt; font-weight: bold;}
            .c_final{ font-size: 13pt; font-weight:bold;  }
        </style>
        
        <thead style="border:none;" id="chega">
            <tr class="bg1">

                <th >DISCIPLINA</th>
                <th >CURSO</th>
                <th >ANO CURRICULAR</th>
                <th >ANO LECTIVO</th>
                <th >REGIME</th>
                <th >TURMA</th> 
                @isset($prova)
                <th>PROVA</th> 
            </tr>
            @endisset   
        </thead>




        <tbody id="corpoTabela"> 
            <tr class="bg2">
                <td  class="text-center bg2">
                    {{$discipline_code}}
                </td>

                <td  class="text-center bg2">
                    {{$curso}} 
                </td>
                {{--<td  class="text-center bg2">{{$lectiveYear[0]->display_name}}</td>--}}
                <td  class="text-center bg2">
                    {{$ano_curricular }} 
                </td>
                <td  class="text-center bg2">
                    {{$lectiveYear}}   
                </td> 
                <td  class="text-center bg2">
                    {{$regimeFinal}}
                </td>
                <td  class="text-center bg2">
                    {{$turma}}
                </td>
                  @isset($prova)
                <td  class="text-center bg2">
                    {{$prova}}
                </td>
                @endisset
            </tr>                    
        </tbody>
    </table>


@endif




    {{-- PAUTA FINAL DA DISCIPLINA --}}
    @if ($documentoCode_documento == 101)

        <style>

            body {
                font-family: 'Tinos', serif;
            }

            .form-group {
                /* margin-bottom: 1px; */
                font-weight: normal;
                line-height: unset;
                font-size: 0.75rem;
            }

            .h1-title {

                padding: 0;
                margin-bottom: 0;
                /* font-size: 14pt; */
                font-size: 1.50rem;
                padding-top: 10px;
                /* background-color:red; */
                /* font-weight: bold; */
                width: 100%;

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

            .thead-parameter-group {
                color: white;
                background-color: #3D3C3C;
            }

            .th-parameter-group {
                padding: 2px 5px !important;
                font-size: .625rem;
            }

            .div-top {
                position: relative;
                margin-bottom: 5px;
                background-color: rgb(240, 240, 240);
                /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');      */
                /* background-image:url('{{ $logotipo }}');     */
                /* background-image: url({{ asset('/storage/images/backgrounds/{$institution->logotipo}') }}); */
                /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
                background-position: 100%;
                background-repeat: no-repeat;
                background-size: 10%;
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
                width: 70px;
                height: 100%;
                margin-bottom: 5px;
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

        <div class="div-top" style="height:110px;">


            <table class="table m-0 p-0 " style="border:none;">
                <tr>
                    <td class="td-fotografia " rowspan="12"
                            style="background-image:url('{{ $logotipo }}');width:100px; height:78px; background-size:100%;
                                    background-repeat:no-repeat;Background-position:center center;
                                    border:none;padding-right:12px;top:-5px;position:relative;
                                    "
                                    >
                    </td>

                </tr>

                <tr>
                    <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>

                </tr>
                <tr>
                    <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
                </tr>





                <tr>
                    <td class="" style="border:none;">
                        <h1 class="h1-title" style=" padding-left: 20%;">
                            Pauta de {{ mb_strtoupper($discipline_name, 'UTF-8') }}
                            <!--Pauta de {{ mb_strtoupper('Prática Diplomáticas, Protocolares e Cerimoniais', 'UTF-8') }} -->
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="" style="border:none;">

                        <span class="" rowspan="1" style="padding-left:20%;top:-20px;position:relative;">
                            Documento gerado a
                            <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                        </span>

                    </td>
                </tr>

            </table>





            <div
                style="position: absolute; top: 8px; left: 110px; width: 350px; font-family: Impact; padding-top: 15px;">
                <h4><b>
                        @if (isset($institution->nome))
                            @php
                                $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                            @endphp
                            {{ $institutionName }}
                        @else
                            Nome da instituição não encontrado
                        @endif
                    </b></h4>
            </div>

        </div>

        <table class="table_te">
            <style>
                .table_te {
                    background-color: #F5F3F3;
                    !important;
                    width: 100%;
                    text-align: right;
                    font-family: calibri light;
                    margin-bottom: 6px;
                }

                .table_pauta {
                    background-color: #F5F3F3;
                    !important;
                    width: 100%;
                    text-align: right;
                    font-family: calibri light;
                    margin-bottom: 6px;
                    border: none;
                    border-left: 1px solid #fff;
                    border-bottom: 1px solid #fff;
                }

                .table_te th {
                    border-left: 1px solid #fff;
                    border-bottom: 1px solid #fff;
                    padding: 4px;
                    !important;
                    text-align: center;
                }

                .table_pauta th {
                    border-left: 1px solid #fff;
                    border-bottom: 1px solid #fff;
                    padding: 4px;
                    !important;
                    text-align: center;
                }

                .table_te td {
                    border-left: 1px solid #fff;
                    background-color: #F9F2F4;
                }

                .table_pauta td {
                    border-left: 1px solid #fff;
                    background-color: #F9F2F4;
                }

                .table_pauta tr {
                    border-bottom: 1px solid #fff;
                }

                .table_pauta thead {}

                .tabble thead {}

                #corpoTabela tr td {
                    font-size: 12pt;
                }

                #chega th {
                    font-size: 13pt;
                    font-weight: bold;
                }

                .c_final {
                    font-size: 13pt;
                    font-weight: bold;
                }
            </style>

            <thead style="border:none;" id="chega">
                {{-- <th>DISCIPLINA</th> --}}
                <th>CURSO</th>
                <th>ANO CURRICULAR</th>
                <th>ANO LECTIVO</th>
                <th>REGIME</th>
                {{-- <th>TURMA</th> --}}
                @isset($prova)
                    <th>PROVA</th>
                @endisset
            </thead>




            <tbody id="corpoTabela">
                <tr>
                    {{-- <td class="text-center">
                        {{ $discipline_code }}
                    </td> --}}

                    <td class="text-center">
                        {{ $curso }}
                    </td>
                    {{-- <td  class="text-center">{{$lectiveYear[0]->display_name}}</td> --}}
                    <td class="text-center">
                        {{ $ano_curricular }}
                    </td>
                    <td class="text-center">
                        {{ $lectiveYear }}
                    </td>
                    <td class="text-center">
                        {{ $regimeFinal }}
                    </td>
                    {{-- <td class="text-center">
                        {{ $turma }}
                    </td> --}}
                    @isset($prova)
                        <td class="text-center">
                            {{ $prova }}
                        </td>
                    @endisset
                </tr>
            </tbody>
        </table>


    @endif




{{-- CABEÇALHO DA ESTATÍSTICAS  --}}


@if ($documentoCode_documento == 501)

<style>

    body {
        font-family: 'Tinos', serif;
    }

    .form-group {
        /* margin-bottom: 1px; */
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }

    .h1-title {

        padding: 0;
        margin-bottom: 0;
        /* font-size: 14pt; */
        font-size: 1.50rem;
        padding-top: 10px;
        /* background-color:red; */
        /* font-weight: bold; */
        width: 100%;

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

    .thead-parameter-group {
        color: white;
        background-color: #3D3C3C;
    }

    .th-parameter-group {
        padding: 2px 5px !important;
        font-size: .625rem;
    }

    .div-top {
        position: relative;
        margin-bottom: 5px;
        background-color: rgb(240, 240, 240);
        /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');      */
        /* background-image:url('{{ $logotipo }}');     */
        /* background-image: url({{ asset('/storage/images/backgrounds/{$institution->logotipo}') }}); */
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 10%;
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
        width: 70px;
        height: 100%;
        margin-bottom: 5px;
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

<div class="div-top" style="height:110px;">


    <table class="table m-0 p-0 " style="border:none;">
        <tr>
            <td class="td-fotografia " rowspan="12"
                style="background-image:url('{{ $logotipo }}');width:100px; height:78px; background-size:100%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;padding-right:12px;top:-5px;position:relative;
                        ">

            </td>

        </tr>

        <tr>
            <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>

        </tr>
        <tr>
            <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
        </tr>





        <tr>
            <td class="" style="border:none;">
               
                        @php
                $Titulo = mb_strtoupper("Análise estatística-".$Pauta_Name??"", 'UTF-8');
                @endphp
                <h1 class="h1-title" style=" padding-left: 40%;" >
                    <b>{{$Titulo}}</b>
                </h1>
              
            </td>
        </tr>
        <tr>
            <td class="" style="border:none;">

                <span class="" rowspan="1" style="padding-left:40%;top:-20px;position:relative; font-size:17px;">
                    Documento gerado a
                    <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                </span>

            </td>
        </tr>

    </table>





    <div
        style="position: absolute; top: 8px; left: 110px; width: 350px; font-family: Impact; padding-top: 12px;">
        <h4><b>
                @if (isset($institution->nome))
                    @php
                        $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                    @endphp
                    {{ $institutionName }}
                @else
                    Nome da instituição não encontrado
                @endif
            </b></h4>
    </div>

</div>

@endif






{{-- CABEÇALHO DA ESTATÍSTICAS  --}}


@if ($documentoCode_documento == 502)

<style>

    body {
        font-family: 'Tinos', serif;
    }

    .form-group {
        /* margin-bottom: 1px; */
        font-weight: normal;
        line-height: unset;
        font-size: 0.75rem;
    }

    .h1-title {

        padding: 0;
        margin-bottom: 0;
        /* font-size: 14pt; */
        font-size: 1.50rem;
        padding-top: 10px;
        /* background-color:red; */
        /* font-weight: bold; */
        width: 100%;

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

    .thead-parameter-group {
        color: white;
        background-color: #3D3C3C;
    }

    .th-parameter-group {
        padding: 2px 5px !important;
        font-size: .625rem;
    }

    .div-top {
        position: relative;
        margin-bottom: 5px;
        background-color: rgb(240, 240, 240);
        /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');      */
        /* background-image:url('{{ $logotipo }}');     */
        /* background-image: url({{ asset('/storage/images/backgrounds/{$institution->logotipo}') }}); */
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 10%;
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
        width: 70px;
        height: 100%;
        margin-bottom: 5px;
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

<div class="div-top" style="height:110px; margin-botton:1px;">


    <table class="table m-0 p-0 " style="border:none;">
        <tr>
            <td class="td-fotografia " rowspan="12"
                style="background-image:url('{{ $logotipo }}');width:100px; height:78px; background-size:100%;
                        background-repeat:no-repeat;Background-position:center center;
                        border:none;padding-right:12px;top:-5px;position:relative;
                        ">

            </td>

        </tr>

        <tr>
            <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>

        </tr>
        <tr>
            <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
        </tr>





        <tr>
                        @php
                                 if($estado==2){
                                     $titulo= mb_strtoupper("Análise estatístico dos graduados", 'UTF-8');
                                }else if($estado==3){
                                    
                                    $titulo= mb_strtoupper("Listagem dos graduados", 'UTF-8');
                                }
                                else{
                                    
                                    $titulo= mb_strtoupper("Análise estatístico", 'UTF-8');
                                 }
                                   

                            @endphp
            <td class="" style="border:none;">
                <h1 class="h1-title" style=" padding-left: 40%;" >
                    <b>{{$titulo}}</b>
                </h1>
            </td>
        </tr>
        <tr>
            <td class="" style="border:none;">

                <span class="" rowspan="1" style="padding-left:40%;top:-20px;position:relative; font-size:17px;">
                    Documento gerado a
                    <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                </span>

            </td>
        </tr>

    </table>





    <div
        style="position: absolute; top: 8px; left: 110px; width: 350px; font-family: Impact; padding-top: 12px;">
        <h4><b>
                @if (isset($institution->nome))
                    @php
                        $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                    @endphp
                    {{ $institutionName }}
                @else
                    Nome da instituição não encontrado
                @endif
            </b></h4>
    </div>
   
    <table class="table_te" style="margin-top:10px;">
    <style>
        .table_te {
            background-color: #F5F3F3;
             !important;
            width: 100%;
            text-align: right;
            font-family: calibri light;
            margin-bottom: 6px;
        }

        .table_pauta {
            background-color: #F5F3F3;
             !important;
            width: 100%;
            text-align: right;
            font-family: calibri light;
            margin-bottom: 6px;
            border: none;
            border-left: 1px solid #fff;
            border-bottom: 1px solid #fff;
        }

        .table_te th {
            border-left: 1px solid #fff;
            border-bottom: 1px solid #fff;
            padding: 4px;
             !important;
            text-align: center;
        }

        .table_pauta th {
            border-left: 1px solid #fff;
            border-bottom: 1px solid #fff;
            padding: 4px;
             !important;
            text-align: center;
        }

        .table_te td {
            border-left: 1px solid #fff;
            background-color: #F9F2F4;
        }

        .table_pauta td {
            border-left: 1px solid #fff;
            background-color: #F9F2F4;
        }

        .table_pauta tr {
            border-bottom: 1px solid #fff;
        }

        .table_pauta thead {}

        .tabble thead {}

        #corpoTabela tr td {
            font-size: 12pt;
        }

        #chega th {
            font-size: 13pt;
            font-weight: bold;
        }

        .c_final {
            font-size: 13pt;
            font-weight: bold;
        }
    </style>

    <thead style="border:none;" id="chega">
         @if ($estado!=2 && $estado!=3 )
                <th>DISCIPLINA</th>
         @endif
        <th>CURSO</th>
        <th>ANO LECTIVO</th>
        @if ($estado==0)
        <th>TOTAL</th>
        @endif
    </thead>




    <tbody id="corpoTabela">
        <tr>
            @if ($estado!=2 && $estado!=3 )
                    <td class="text-center">
                        {{ $estudantes[0]->codigo_disciplina ?? "" }} - {{ $estudantes[0]->disciplina ??"" }}
                    </td>
             @endif

            <td class="text-center">
                {{ $curso->course_name ??""}}
            </td>
        
            <td class="text-center">
                {{ $estudantes[0]->AnoLectivo ?? ""}}
            </td>
            @if ($estado==0)
            <td class="text-center">
                {{ count($estudantes)}}
            </td>
            @endif
          
        </tr>
    </tbody>
</table>


</div>

@endif





@endif