@php

    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    $logotipo = link_storage($logotipo);
    
@endphp
<style>
        .div-top_Q {
            height:99px;
            text-transform: uppercase;
            position: relative;
   
            margin-bottom: 15px;
            /*background-color: rgb(240, 240, 240);*/
            /*background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');*/
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
                    background-image: url("{{$logotipo}}");
            background-position: left;
            background-repeat: no-repeat;
            background-size: 10%;
        }
         .h1-titleS {
            padding: 0;
            margin-bottom: 0;
            font-size:4.3em;
            font-weight: bold;
           
           
           
        }
        

</style>


<div class="div-top_Q" style="">
    <table class="table m-0 p-0">
               
            <td class="td-declaracao" rowspan="12" style="background-color: transparent; height:96px; left:200px;">
                {{-- @if($config->titulo_position==1) 
               
                <h1 class="h1-titleS" style="padding-top:9px;text-align:left;"  >
                 Declaração
                 <p style="font-size:18pt; position:absolute;margin-top:-15px; margin-left:253px;">Sem Notas</p> 
                </h1>
                 @elseif($config->titulo_position==2)
                <h1 class="h1-titleS" style="padding-top:9px;text-align:center;"  >
                 Declaração
                 <p style="font-size:18pt; position:absolute;margin-top:-15px; margin-left:253px;">Sem Notas</p>
                </h1> 
                 @elseif($config->titulo_position==3) --}}
                <h1 class="h1-titleS" style="padding-top:9px; text-align:right;"  >
                 Declaração
                 <p style="font-size:18pt; margin-top:-15px; text-align: right;">Sem Notas</p>
                </h1> 
                {{-- @else
                <h1 class="h1-titleS" style="padding-top:9px;"  >
                 Declaração
                 <p style="font-size:18pt; position:absolute;margin-top:-15px; margin-left:253px;">Sem Notas</p>
                </h1> 
                @endif --}}
            </td>

        </tr>


    </table>
    
     <label style="float:right; font-size:12pt; margin-top:7px;">Decreto Presidencial nº 168/12 de 24 de Julho</label>
</div>