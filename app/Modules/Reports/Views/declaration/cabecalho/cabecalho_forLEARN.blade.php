<br>
<br>

@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;

    $logotipo = link_storage($logotipo);

@endphp
<style>
    .div-top {
        height: 190px;
        text-transform: none;
        background-color: white;
        background-image: url("{{$logotipo}}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 180px;
    }

    .h1-title {
        height: 100px;
        background-position: center;
        color: black;
        background-repeat: no-repeat;
        margin-top: 40px;
        font-size: 23px;
        letter-spacing: 1px;
        text-transform: uppercase !important;
        font-weight: bolder;
    }

    .watermark {
        opacity: 0.2;
        color: BLACK;
        position: fixed;
        top: 280px;
        background-image: url("{{$logotipo}}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 500px;
        height: 800;
        width: 100%;
    }

    .div-top_f {
        margin-top: 50px;
        margin-bottom: 0px;
    }

    #decreto {
        padding: 0px !important;
        margin-left: 120px !important;
    }
</style>

<div class="div-top">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12" style="background-color: transparent; height:96px;">
                <p style="text-align: center;margin-top: 205px;font-size: 31.5px;text-transform: uppercase!important;"
                    class="institution-name">
                    <b style="margin-top: 1rem; color: red;">{{$institution->ome}}</b>
                </p>
                <p id="decreto" style="margin-top: -15px;font-weight: 600;font-size: 15px;padding-left: 35px;">
                    {{$institution->decreto_instituicao}}
                </p>
            </td>
        </tr>
    </table>
</div>
<br>
@if($requerimento->codigo_documento == 15)

<div style="text-align: right;margin-top:120px;margin-bottom: -30px" >
                        <div style="margin-right:130px;text-align: left; display: inline-block;width:500px">
                            <p style="font-size:17pt!important;font-weight:bold">{{ $instituicao_nome }}</p>
                        </div>
                    </div>


@endif
<br>
<div class="div-top_f" @if($requerimento->codigo_documento == 15) hidden="true"@endif</div>

<table class="table m-0 p-0 top">
    <tr>

        <td class="td-declaracao" rowspan="12" style="background-color: transparent; text-align:center;">

            <h1 class="h1-title"><b>
                    @php $doc_title = "";
                        if (isset($requerimento->codigo_documento)) {

                            switch ($requerimento->codigo_documento) {

                                case '1':
                                    $doc_title = "Declaração sem notas";
                                    break;
                                case '2':
                                    $doc_title = "Declaração com notas";
                                    break;
                                case '4':
                                    $doc_title = "Certificado";
                                    break;
                                case '6':
                                    $doc_title = "Declaração de frequência";
                                    break;
                                case '8':
                                    $doc_title = "Declaração de fim de curso";
                                    break;
                                case '5':
                                    $doc_title = "Diploma";
                                    break;
                                case '9':
                                    $doc_title = "Declaração com notas de exame de acesso";
                                    break;
                                case '10':
                                    $doc_title = "Pedido de equivalência (de Entrada no ISPK)";
                                    break;
                                case '11':
                                    $doc_title = "Pedido de transferência (de saída no ISPK)";
                                    break;
                                case '15':
                                    $doc_title = "";
                                    break;
                                default:
                                    # code...
                                    break;

                            }

                        }
                        echo $doc_title;
                    @endphp
                </b>
            </h1>


        </td>

    </tr>


</table>

</div>