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
                padding:0;
                color: #444;
            }
            footer{
            
            }
            footer{
                width: 100%;

            }.tb_footer{
                opacity:0.8;
                width: 1000px;
                margin-tom:-20px;
                
            }

            .text-center {
            
                text-align: right;
                /* padding-top:80px; */
                padding:0;

            }

            .text-right {
                text-align:right;
                
            }
            #tel{
                /* widows:40px;  */
                /* height:40px; */
            }

        
        </style>
    </head>

    <body style="@if($requerimento->codigo_documento == 12)
    padding-top:30px;
    @endif">
        <!--<footer style="@if($requerimento->codigo_documento != 2)margin-left:595px;@else margin-left:0px @endif font-size:22pt !important;font-family:calibri light !important;">-->
<footer        style="
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
    color:#f7371e!important;>
    ">

            <table class="tb_footer">
                                    
                <style>#decima td{font-family:calibri light;@if($requerimento->codigo_documento == 2 || $requerimento->codigo_documento == 10)padding-left:135px; @else padding-left:0px;@endif  }.iconeIMAg{ height:18px;position:absolute;top:20px;
               }</style>
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
                   {{-- <td  class="text-right" id="tel">  <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/50/000000/filled-message.png" />
                        {{$institution->email}} | <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/60/000000/phone-office--v1.png"/>{{$institution->telefone_geral}} 
                    </td> --}}      
                </tr>
                {{-- <tr>
                    <td style="" colspan="1"></td></label>
                    <td style="color:#444; text-align:center;font-size:11pt;" >powered by forLEARN<sup>®</sup> </td> 
                    <td style="" colspan="1"></td>
                </tr>  --}}     
            </table>
        </footer>
    </body>
</html>
