<br>

@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    
    $logotipo = link_storage($logotipo);
    
@endphp

<style>
    .div-top {
        height: 110px;
        background-color: white;
        background-image: url("{{$logotipo}}");
        background-position: left;
        background-position-y: 0px;
        background-position-x: 77px;
        background-repeat: no-repeat;
        background-size: 90px;

    }

   
    .watermark {
        opacity: 0.1;
        color: BLACK;
        position: fixed;
        top: 300px;
        background-image: url("{{$logotipo}}");
        background-size: 800px;
        background-position: center;
        background-position-x: 100px;
        background-repeat: no-repeat;
        background-size: 800px;
        height: 800;
        width: 100%;

    }

    .h1-title {
        
    background-size: 380px 80px;
    background-position: center;
    color: transparent;
    background-repeat: no-repeat;
    
    }
</style>

<div class="div-top" style="">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:96px;  text-align:center;">


                <p style="text-align: center;margin-top: 30px;font-weight: 900;font-size: 25px;text-transform: uppercase;">
                    {{ $institution->nome }} <br>LUANDA - Angola
                </p>
                <p
                    style="text-align: center;margin-top: 10px;margin-left:30px;font-weight: 600;font-size: 14px;">
                    Criado pelo Decreto Presidencial nº168/12, de 24 de Julho, publicado em Diário da República nº141,
                    Iº Série
                </p>


            </td>

        </tr>


    </table>

    <!--<label style="float:right; font-size:12pt;position:absulute; margin-top:9px;">Decreto Presidencial nº 168/12 de 24 de Julho</label>-->
</div>

<div class="div-top_f" style="">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:96px;  text-align:center;">

                <h1 class="h1-title">
                    <b> <u> Declaração </u></b>
                </h1>


            </td>

        </tr>


    </table>

    <!--<label style="float:right; font-size:12pt;position:absulute; margin-top:9px;">Decreto Presidencial nº 168/12 de 24 de Julho</label>-->
</div>
