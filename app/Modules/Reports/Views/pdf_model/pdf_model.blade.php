
{{-- LISTA DE MATRICULADOS --}}
@if ($documentoCode_documento == 1)
    <!doctype html>
    <html>
        <head>
            <meta charset="UTF-8">
            <style>

                @import url('http://fonts.cdnfonts.com/css/calibri-light');     
        
                body{
                    font-family: 'Calibri Light', sans-serif;
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
                    background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}');
                    /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
                    background-position: 100%;
                    background-repeat: no-repeat;
                    background-size: 8%;
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
                                        {{$titulo_documento}}
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="">
                                    <span class="" rowspan="1">
                                        @foreach ($lectiveYears as $anoLectivo)
                                        <b> 
                                            <b>{{$anoLectivo_documento}} {{$anoLectivo->currentTranslation->display_name}}</b>
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
                                        {{$documentoGerado_documento}}
                                        <b>
                                            <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                                        </b>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </main>            
            </header>
        </body>
    </html>

<!-- aqui termina o cabeçalho do pdf -->
@endif




{{-- HORÁRIO DA TURMA --}}
@if ($documentoCode_documento == 2)
    <style>
        .div-top {
            text-transform: uppercase;
            position: relative;
  
            margin-bottom: 2px;
            background-color: rgb(240, 240, 240);
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}'); 
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
                                {{$titulo_documento}}
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
                                {{$documentoGerado_documento}}
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

        @import url('http://fonts.cdnfonts.com/css/calibri-light');

        body{
            font-family: 'Calibri Light', sans-serif;
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
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}');
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
                          {{$titulo_documento}}
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
                            {{$documentoGerado_documento}}
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </main>

@endif



{{-- PERCURSO ACADÉMICO --}}
@if ($documentoCode_documento == 4)

    <style>

        @import url('http://fonts.cdnfonts.com/css/calibri-light');

        body{
            font-family: 'Calibri Light', sans-serif;
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
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}');
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
                            {{$titulo_documento}}
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
                            {{$documentoGerado_documento}}
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </main>

@endif





{{-- FICHA DE CANDIDATO A ESTUDANTE --}}
@if ($documentoCode_documento == 5)

    <style>
        html, body {
            font-size: {{ $options['font-size'] }};
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
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}'); 
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
    </style>
    <main>
        <div class="div-top" style=" height:80px;">
            <table class="table m-0 p-0">
                <tr>
                    <td class="td-fotografia" rowspan="12"
                        @foreach($user->parameters as $parameter)
                            @if($parameter->code === 'fotografia')
                                style="background-image:url('{{ asset('storage/attachment/' . $parameter->pivot->value) }}');width:100px; height:78px;"
                                @endif           
                        @endforeach
                    >
                    </td>                    
                
                    <td class="" style=" padding-top:16px;"  >
                        <h1 class="h1-title" >
                            {{$titulo_documento}}
                            @if(!empty($user->roles))
                                {{ $user->roles->first()->currentTranslation->display_name }}
                            @else ... @endif
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="" rowspan="1">
                        {{$documentoGerado_documento}}
                        <b>{{ $date_generated }}</b>
                    </td>
                </tr>
            </table>
        </div>
    </main>

@endif



{{-- BOLETIM DE MATRÍCULA --}}
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
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}'); 
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
                            {{$titulo_documento}}
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="data_bMatricula" rowspan="4">
                        <style>.data_bMatricula{background-color:transparent;}</style>
                        {{$documentoGerado_documento}}
                        <b>{{ Carbon\Carbon::parse($matriculation_generated_date)->format('d/m/Y') }}</b>
                    </td>
                </tr>
            </table>
        </div>

    </main>    

@endif



{{-- GERAR LISTA DE PENDENTES --}}
@if ($documentoCode_documento == 7)

    <style>
        @import url('http://fonts.cdnfonts.com/css/calibri-light');
        
        body{
            font-family: 'Calibri Light', sans-serif;                                         
        }
        .div-top {     
            font-family: 'Advent Pro', sans-serif;
            color:grey;
            text-transform: uppercase;
            position:50px;
            margin-bottom:2px;
            background-color: rgb(240, 240, 240);
            background-image: url('http://forlearn.ao/storage/{{$institution->logotipo}}');
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
            6ufont-family: 'Calibri Light', sans-serif;    

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
                            {{$titulo_documento}}
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
                         <b>{{$documentoGerado_documento}}</b>
                         @if ($date2 != null)
                            {{ date('d-m-Y', strtotime($date1)) }} - {{ date('d-m-Y', strtotime($date2)) }}
                         @else
                            {{ date('d-m-Y', strtotime($date1)) }}
                         @endif
                    </td>
                </tr>
            </table>              
        </div>
    </main>

@endif
