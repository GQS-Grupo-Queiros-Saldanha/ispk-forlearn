

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

             .tb_footer {
                width: 100%;
                font-size: 14px;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
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

          
        </style>
    </head>
    <body>


    <footer class="rodape_recibo">
        <table class="table m-0 p-0 tb_footer" style="margin-bottom: 4em !important; line-height: 20px">
            <tr>
                <td width="60%" class="border-0"></td>
                <td width="40%" class="border-0">
                    __________________________
                    <br/>
                    O Tesoureiro(a)
                    <br/>
                    ({{$user->name}})
                </td>
            </tr>
        </table>

        <table class="tb_footer">
            <style>#decima td{font-family:calibri light; border-bottom:1px solid #444;font-size:8.4pt; }
                  .iconeIMAg{height:15px;position:relative;top:5px; }
                  .rodape_recibo{background: transparent;}
                  .rodape_recibo td{background: transparent;}

            </style>
            <tr id="decima">
                <td >{{ $institution->nome }}<br>        
            </td>

            <td>   

            </td>
                
<td class="text-right" id="tel">  <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/50/000000/filled-message.png" /> {{ $institution->email }} | <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/60/000000/phone-office--v1.png"/>{{ $institution->telefone_geral }} </td>      
           </tr>
            <tr>
                <td style="" colspan="1"></td></label>
                <td style="color:#444; text-align:center;font-size:8pt;" >powered by forLEARN<sup>&copy;</sup> </td> 
                <td style="" colspan="1"></td>
            </tr>
        </table>
      
    </footer>
    </body>
    </html> 

