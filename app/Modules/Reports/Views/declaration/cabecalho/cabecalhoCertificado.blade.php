@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    
    $logotipo = link_storage($logotipo);
    
@endphp
<style>
  
    .logo{
        height: 200px;
        text-transform: uppercase;
        width: 200px;
        background-color: rgb(255, 255, 255)!important;
       background-image: url("{{$logotipo}}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 200px;
        border-radius: 50%;
        padding-top: 20px; 
        padding-bottom: 20px; 
        margin-top: 10px!important;
        opacity:0;
    }
    .div-top{
        font-family: "calibri", Helvetica, sans-serif!important;
        margin-top: 8px;
        height: 30px;
        background-color: white;
    }
</style>
<center><div class="logo"></div></center>
<div class="div-top">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent;  text-align:center; margin-top:10px;">

                <h1 class="h1-title" style="">
                    
                </h1>
                <p style="text-align: center;font-weight: 900;opacity:0;">
                    {{$institution->nome}} 
                </p>
                <p style="text-align: center;margin-top: -20px;font-weight: 600;text-transform: capitalize!important;font-size: 18px;font-family: arial;opacity:0;">
                    Decreto Presidencial nº 168/12 de 24 de Julho de 2012
                </p>


            </td>

        </tr>


    </table>
</div>


<div class="div-top_f" style="">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao"
                style="background-color: transparent; text-align:center;">

                <h1 class="h1-title" style="">
                    Certificado
                </h1>


            </td>

        </tr>


    </table>

    
</div>
