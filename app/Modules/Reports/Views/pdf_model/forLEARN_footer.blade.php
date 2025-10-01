<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="https://kit.fontawesome.com/8c60ea13ff.js" crossorigin="anonymous"></script>
        <script>
            function subst() {
                var vars = {};
                var x = document.location.search.substring(1).split('&');
                for (var i in x) {
                    var z = x[i].split('=', 2);
                    vars[z[0]] = unescape(z[1]);
                }
                var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
                for (var i in x) {
                    var y = document.getElementsByClassName(x[i]);
                    for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
                }
            }
        </script>
        <style>
            body{
                margin: 0;
                padding: 0 0 20px 0; /* Reduzi o padding inferior */
                color: #444;
                position: relative;
                min-height: 100vh; /* Garante que o body tenha altura mínima */
            }
            footer{
                margin-top: 30px; /* Reduzi a margem superior */
                width: 100%;
                position: fixed; /* Mudei para fixed para melhor controle */
                bottom: 10px; /* Aumentei um pouco para evitar corte */
                padding-top: 5px; /* Reduzi o padding superior */
                font-family: Calibri !important;
                color: #f7371e !important;
                left: 0;
                z-index: 100;
            }
            
            .tb_footer{
                opacity:0.8;
                width: 1000px;
            }

            .text-center {
                text-align: right;
                padding:0;
            }

            .text-right {
                text-align:right;
            }
            
            #tel{
                /* widows:40px;  */
                /* height:40px; */
            }

            /* Adicionei uma classe para o conteúdo principal */
            .main-content {
                padding-bottom: 80px; /* Espaço para o footer não sobrepor conteúdo */
            }
        
        </style>
    </head>

    <body style="@if($requerimento->codigo_documento == 12)
    padding-top:30px;
    @endif">
        <!-- Conteúdo principal -->
        <div class="main-content">
            <!-- Seu conteúdo aqui -->
        </div>

        <!-- Footer -->
        <footer style="
            @if($requerimento->codigo_documento == 2)
                margin-left: -20px;
                font-size: 11.5pt !important;
            @elseif($requerimento->codigo_documento == 10 || $requerimento->codigo_documento == 6)
                margin-left: 150px;
                font-size: 13pt !important;
            @elseif($requerimento->codigo_documento == 12)
                margin-left: 30px;
                font-size: 13pt !important;
            @elseif($requerimento->codigo_documento == 11)
                margin-left: 150px;
                font-size: 13pt !important;
            @else
                margin-left: 595px;
                font-size: 15pt !important;
            @endif
            font-family: Calibri !important;
            color:#f7371e!important;
        ">

            <table class="tb_footer">                    
                <style>
                    #decima td{
                        font-family:calibri light;
                        @if($requerimento->codigo_documento == 2 || $requerimento->codigo_documento == 10)
                            padding-left:135px; 
                        @else 
                            padding-left:0px;
                        @endif  
                    }
                    .iconeIMAg{ 
                        height:18px;
                        position:absolute;
                        top:20px;
                    }
                </style>
                
                <tr id="decima">
                @if($requerimento->codigo_documento == 2)
                 <td> 
                         {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->dominio_internet}}
                    </td>
                @elseif($requerimento->codigo_documento == 11)
                 <td> 
                         {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;
                         
                         {{$institution->dominio_internet}}
                    </td>
                 @elseif($requerimento->codigo_documento == 10)
                 
                         {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->dominio_internet}}
                 @elseif($requerimento->codigo_documento == 12)
                 
                         {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->dominio_internet}}
                 @elseif($requerimento->codigo_documento == 6)
                 
                         {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->dominio_internet}}
                @else
                <td> 
                Documento nº {{$requerimento->code ?? 'código doc'}} liquidado com CP nº{{$recibo ?? 'recibo'}} 
                </td>
                @endif
                </tr>    
            </table>
        </footer>
    </body>
</html>