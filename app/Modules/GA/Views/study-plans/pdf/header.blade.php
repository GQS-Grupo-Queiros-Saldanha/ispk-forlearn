@php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
      
        @endphp

<style>
    .div-top {
        height:99px;
        text-transform: uppercase;
        position: relative;

        margin-bottom: 15px;
        /*background-color: rgb(240, 240, 240);*/
        /*background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');*/
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/            
        background-image: url('{{$logotipo}}');
        background-position: left;
        background-repeat: no-repeat;
        background-size: 10%;
    }
     .h1-title_Com {
        padding: 0;
        margin-bottom: 0;
        font-size:4.3em;
        font-weight: bold;
    }
</style>


<div class="div-top" style="">
    <table class="table m-0 p-0">
         <tr>

            <td class="td-declaracao" rowspan="12" style="background-color: transparent; height:96px; left:200px;">
                 <h1 class="h1-title_Com" style="padding-top:9px; text-align: right; font-size:30pt;">
                    <span>{{$titulo_documento}}</span>
                    <br>
                    <small style="font-size: 9pt;">{{$documentoGerado_documento}}</small>
                </h1> 
                
            </td>

        </tr>


    </table>
    
     
</div>