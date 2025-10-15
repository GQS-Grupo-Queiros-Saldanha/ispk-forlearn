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
     
<footer        style="
    margin-left: 150px;
    font-size: 13pt !important;

    font-family: Calibri !important;
    color:#f7371e!important;
    ">

            <table class="tb_footer">
                                    
                <style>#decima td{font-family:calibri light;padding-left:135px;}.iconeIMAg{ height:18px;position:absolute;top:20px;
               }</style>
                <tr id="decima">
                
               <td>
                   {{$institution->morada}}, {{$institution->provincia}}, Angola
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         {{$institution->dominio_internet}}
               </td>
                    </tr>
            </table>
        </footer>
    </body>
</html>
