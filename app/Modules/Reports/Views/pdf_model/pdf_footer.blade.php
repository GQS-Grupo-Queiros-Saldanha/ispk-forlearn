<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
       
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

                width: 100%;
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
            .t-color {
                color: #fc8a17;
            }
        
        </style>
    </head>

    <body>
        <footer>
            <table class="tb_footer">
                <style>#decima td{font-family:calibri light; border-bottom:1px solid #444; }.iconeIMAg{ height:18px;position:relative;top:5px; }</style>
                <tr id="decima">
                    <td >{{$institution->nome}}<br>
                    {{-- <td style="font-family:calibri light;  "><b > Instituto Superior Politécnico Maravilha</b><br> --}}
                    </td>

                    <td>
                        
                    </td>
                    
                    <td  class="text-right" id="tel">  <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/50/000000/filled-message.png" />
                        {{$institution->email}} | <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/60/000000/phone-office--v1.png"/>{{$institution->telefone_geral}} 
                    </td>      
                </tr>
                <tr>
                    <td style="color:#444; text-align:center;font-size:11pt;" colspan="3">
                        <span class="t-color"> Powered by</span> <b
                        style="color:#243f60;font-size: 18px;margin-top:10px;">forLEARN <sup>®</sup></b>
                    </td> 
                    
                </tr>
            </table>
        </footer>
    </body>
</html>
