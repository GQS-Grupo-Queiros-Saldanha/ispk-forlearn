
@if(isset($documentoCode_documento))
    {{-- recibo de pagamento singular  --}}
    @if ($documentoCode_documento == 1)
                <style>
                    body{
                        /* font-family: 'Calibri Light', sans-serif; */
                        margin: 0px;
                        padding: 0px;
                    }  
                    .flex-container {
                        display: flex;
                        width: 100%;
                        height: auto;
                        margin: 0px;
                        margin-bottom: 3px;
                        padding: 0px;
                        background-color: #2f5496 !important;
                        color: white;
                        border-bottom: black 2px solid
                    }  
                    .logotipo{
                        background-size: contain;
                        background-repeat: no-repeat;
                        /* background-position: 20%; */
                        width: 90px;
                        height: 80px;
                        border: none;
                        margin: 0px;
                        margin-right: 1px;
                        padding: 0px;
                    }   
                    .name-instituicao{
                        margin: 0px;
                        padding: 0px;
                    }
                    .container-name-instituicao{
                        margin: 0px;
                        padding: 0px;
                        width:500px;
                    }
                    .name-instituicao {
                        width: fit-content;
                        margin: 0px;
                        padding: 0px;
                        padding-top: 6px;
                        padding-left: 5px;
                    }
                    .informacao-recibo{
                       
                        padding-top: 16px;
                        padding-right: 10px;
                        text-align: right!important;
                        
                    }
                   .infor-recibo{
                        width: 42%;
                        
                    }
                    .infor-recibo-text-head{
                        padding: 0px;
                        margin: 0px;
                    }
                    .informacao-recibo-text-head{
                        padding: 0px;
                        margin: 0px;
                        margin-top: -15px;
                    }
                    .infor-recibo-text{
                        padding: 0px;
                        margin: 0px;
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

                </style>
            </head>
            <body>
                <header>
                    <main  class="bg0"> 
                        <div class="flex-container" >
                            <table >
                                <tr >
                                    <td>
                                        <div class="logotipo" style="background-image:url('http://{{$_SERVER['HTTP_HOST']}}/instituicao-arquivo/{{$institution->logotipo}}')"></div>
                                    </td>
                                    <td >
                                        <div class="container-name-instituicao">
                                            @if(isset($institution->nome))                                        
                                              <h4 class="name-instituicao">
                                              @php
                                              $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                              
                                              $new_name = explode(" ",$institutionName);
                                              
                                              foreach( $new_name as $key => $value ){
                                              
                                                  
                                                  if($key==1){
                                                      echo $value."<br>";
                                                  }else{
                                                      echo $value." "; 
                                                  }
                                                 
                                                  
                                              } 
                                            @endphp
                                            @else 
                                                Instituição sem nome
                                            @endif
                                        </h4> 
                                        </div>
                                    </td>
                                    <td  class="informacao-recibo">
                                        <p class="informacao-recibo-text-head" ><small style="font-size: 1pc;color:white">Recibo de vencimentos Nº: <b style="font-size: 1.1pc">00{{$item[0]->recibo_num}}</b></small></p>
                                        <p class="infor-recibo-text-head" ><small style="font-size: 0.9pc">DOCUMENTO GERADO A </small> <strong style="font-size: 1pc" >{{$dataCreated}}</strong></p>
                                        
                                    </td>
                                </tr>
                            </table>
                            <div class="infor-recibo"  >
                                
                               
                                {{-- <div class="infor-recibo-text">
                                    <p style="margin: 0px; padding: 0px"><small>Referente a:</small> <span> </span></p>
                                </div> --}}
                            </div> 
                        </div>
                    </main>            
    @endif
    {{-- recibo de pagamento geral --}}
    @if ($documentoCode_documento == 2)

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
                    font-size: 1em;
                    
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
                    margin-bottom: 15px;
                    background-position: 100%;
                    background-repeat: no-repeat;
                    background-size: 8%;
                    width: 100%
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
                    /* font-size: 14pt; */
                    font-size: 1.20rem;
                    padding-top: 20px;
                    /* background-color:red; */
                    /* font-weight: bold; */
                    width: 100%;
            
                }
            
                .table_te {
                    background-color: #F5F3F3;
                    ;
                    width: 100%;
                    
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
                    padding: 4px;
                    !important;
                    text-align: center;
                    font-size: 18pt;
                    font-weight: bold;
                }
            
                .table_te td {
                    border-left: 1px solid #fff;
                    background-color: #F9F2F4;
                    border-bottom: 1px solid white;
                    font-size: 12pt;
                }
            
                .tabble_te thead {}
            
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
                }
            
                .table-main{
                    padding-left: 10px;
                }
                .assinaturas{
                    font-size: 12px;
                    margin-bottom: 30px;
                }
                .table-main th,  .table-main th td{
                    padding: 1px 1px!important;
                    border-right: 1px solid white!important;
                    font-size: 11px!important;
                }
                .tr td{
                    
                    font-size: 11px!important;
                }
                
            </style>
    
    
        <div class="div-top" class="bg0">
    
    
            <table class="table table-main m-0 "  style="border:none; width: 100%">
                <tr>
    
                    <td class="td-fotografia bg0" rowspan="12"
                        style="background-image:url('http://{{$_SERVER['HTTP_HOST']}}/instituicao-arquivo/{{$institution->logotipo}}');width:90px; height:78px; background-size:100%;
                            background-repeat:no-repeat;Background-position:center center;
                            border:none;padding-right:12px!important;position:relative;
                            ">
                    </td>
    
                </tr>
                <tr>
                    <td class="td-fotografia bg0" rowspan="12" style=" width:200px; border:none;"></td>
    
                </tr>
    
                <tr>
                    <td class="bg0" style="border:none;">
                        
                        <h1 class="h1-title"  style="text-align:right!important;font-size: 1.26rem;border: none;">
                                {{$doc_name}}
                                <p style="margin-top:1px;margin-right:1px;font-size: 15px ; padding:0;background-color:transparent;border: none;color:white!important;">
                                    Documento gerado a  
                                    <b>{{ date('d/m/Y')}}</b>
                                </p> 
                            </h1>
                    </td>
                </tr>
                 
    
            </table>
    
    
    
    
    
            <div
                style="position: absolute; top: 10px; left: 130px; width: 450px; font-family: Impact; color:white;">
                <h4 style="font-size: 1.20rem;"><b>
                   @if (isset($institution->nome))
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
                    Nome da instituição não encontrado
                @endif 
                    </b></h4>
            </div>
    
        </div>
        <br>        
    @endif
    {{-- recibo de pagamento geral por banco --}}
    @if ($documentoCode_documento == 3)

                <style>
                    body{
                        /* font-family: 'Calibri Light', sans-serif; */
                        margin: 0px;
                        padding: 0px;
                    }  
                    .flex-container {
                        display: flex;
                        width: 100%;
                        height: auto;
                        margin: 0px;
                        margin-bottom: 3px;
                        padding: 0px;
                        background-color: rgb(255, 251, 251);
                        border-bottom: black 2px solid
                    }  
                    .logotipo{
                        background-size: contain;
                        background-repeat: no-repeat;
                        /* background-position: 20%; */
                        width: 90px;
                        height: 80px;
                        border: none;
                        margin: 0px;
                        margin-right: 1px;
                        padding: 0px;
                    }   
                    .name-instituicao{
                        margin: 0px;
                        padding: 0px;
                    }
                    .container-name-instituicao{
                        margin: 0px;
                        padding: 0px;
                    }
                    .name-instituicao {
                        /* width: 90%; */
                        margin: 0px;
                        padding: 0px;
                        padding-top: 6px;
                        padding-left: 5px;
                    }
                   .infor-recibo{
                        /* width: 45%; */
                    }
                    .infor-recibo-text-header{
                        padding: 0px;
                        margin: 0px;
                        float: right;
                        /* padding-left: 10pc; */
                    }
                    .infor-recibo-text-head{
                        padding: 0px;
                        margin: 0px;
                    }
                    .infor-recibo-text{
                        padding: 0px;
                        margin: 0px;
                    }
                </style>
            </head>
            <body>
                <header>
                    <main>
                        <div class="flex-container">
                            <table>
                                <tr>
                                    <td>
                                        <div class="logotipo" style="background-image:url('http://{{$_SERVER['HTTP_HOST']}}/instituicao-arquivo/{{$institution->logotipo}}')"></div>
                                    </td>
                                    <td >
                                        <div class="container-name-instituicao">
                                            @if(isset($institution->nome))                                        
                                              <h2 class="name-instituicao">{{$institution->nome}}</h2>  
                                            @else 
                                                Instituição sem nome
                                            @endif
                                        </div>
                                    </td>
                                    <td  class="info-recibo">
                                        {{-- <p class="infor-recibo-text-header" ><small style="font-size: 0.9pc">FOLHA DE</small></p> --}}
                                    </td>
                                </tr>
                            </table>
                            <div class="infor-recibo">
                                <p class="infor-recibo-text-head" >&nbsp;&nbsp;<small style="font-size: 0.9pc">DOCUMENTO GERADO A </small><strong style="font-size: 1pc" >{{$dataCreated}}</strong>
                                   &nbsp;&nbsp;&nbsp;&nbsp;  </small> <strong style="font-size: 1.1pc; margin-left: 20pc;"> </strong>
                                </p>
                                {{-- <div class="infor-recibo-text">
                                    <p style="margin: 0px; padding: 0px"><small>Referente a:</small> <span> </span></p>
                                    <small>Referente a:</small> <strong> janeiro/2022</strong>
                                </div> --}}
                            </div> 
                        </div>
                    </main>            
    @endif
@endif
