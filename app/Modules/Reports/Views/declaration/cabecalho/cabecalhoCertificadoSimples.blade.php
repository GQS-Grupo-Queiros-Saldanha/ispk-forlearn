<br>
<br>
<br>
@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    
    $logotipo = link_storage($logotipo);
    
@endphp


<style>
    .div-top {
        height: 140px;
        background-color: white;
        background-image: url("{{$logotipo}}");
        background-position: left;
        background-position-y: 7px;
        background-position-x: 120px;
        background-repeat: no-repeat;
        background-size: 140px; 
    }



    *{
            margin: 0!important;
            padding: 0!important;
        }
        .watermark {
        color: BLACK;
        position: fixed;
        top: -80px;     
        background-image: url("https://forlearn.ispm.ao/storage/attachment/Merito2.png");
        background-position: center;   
        /* background-position-y: 0px; */
        background-position-x: 0px;
        background-repeat: no-repeat;
        background-size:1000;
        height: 1570;
        width: 100%; 
    }

    .h1-title{
        background-position: center;
        background-repeat: no-repeat;
        color: #00c0f0!important; 
        font-size: 95px;
        font-weight: 900;
        letter-spacing: 4px;
        opacity: 0;
    }
 
    .title-i{
        text-align: center;
        margin-top: 20px!important;
        font-weight: 700; 
        text-transform: uppercase;
        margin-left: 100px!important;  
        font-size: 21px; 
        font-family: 'EB Garamond'!important;
        letter-spacing: 1px;
        color:#484846!important;
        
    } 
    .title-i2{
        text-align: center;
        font-weight: 600;
        font-size: 17px;
        margin-top: -17px;
        margin-left: 180px;
        letter-spacing: 0.8px;

    }

    as{
        font-size: 80px;
        
    } 
</style>

<div class="div-top" style="background-color: transparent;">
    <table class="table m-0 p-0">
        <tr style="">
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:86px;  text-align:center;">

                <p class="title-i">
                    {{ $institution->nome }} 
                    {{-- <br>Benguela - Angola  --}}
                </p>
                <p  class="title-i2">
                    {{-- Criado pelo Decreto Presidencial nº168/12, de 24 de Julho de 2012 --}}
                </p>


            </td>

        </tr>


    </table>
</div>

<div class="div-top_f" style="">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent;  text-align:center;">

                <h1 class="h1-title">
                   <b><as>DIPLOMA</as><br>DE MÉRITO</b>
                </h1>


            </td>

        </tr>


    </table>
</div>
