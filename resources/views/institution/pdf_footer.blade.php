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



    <footer>
        <table class="tb_footer">
            <style>#decima td{font-family:calibri light; border-bottom:1px solid #444; }.iconeIMAg{ height:18px;position:relative;top:5px; }</style>
            <tr id="decima">
                <td >{{$institution->nome}}<br>
                {{-- <td style="font-family:calibri light;  "><b > Instituto Superior Politécnico Maravilha</b><br> --}}
                </td>
    
                <td>
                    
                </td>
                
                <td  class="text-right" id="tel">  
                    <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/50/000000/filled-message.png" />
                    {{$institution->email}} | <img class="iconeIMAg" src="https://img.icons8.com/ios-glyphs/60/000000/phone-office--v1.png"/>{{$institution->telefone_geral}} 
                </td>      
            </tr> 
            <tr>
                <td style="" colspan="1"></td></label>
                <td style="color:#444; text-align:center;font-size:11pt;" >powered by forLEARN<sup>®</sup> </td> 
                <td style="" colspan="1"></td>
            </tr>                  
        </table>      
    </footer>
    

</body>
</html>
