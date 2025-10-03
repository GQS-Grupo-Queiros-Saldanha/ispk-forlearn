@php
    $logotipo = link_storage('https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

    body {
        font-family: 'Tinos', serif;
        text-align: center;
        color: black;
    }

    .div-top {
        height: 190px;
        background: url("{{$logotipo}}") center no-repeat;
        background-size: 180px;
    }

    .institution-name {
        margin-top: 205px;
        font-size: 29.5px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .institution-decree {
        margin-top: -15px;
        font-weight: 600;
        font-size: 15px;
    }

    .h1-title {
        margin-top: 40px;
        font-size: 23px;
        font-weight: bolder;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .watermark {
        opacity: 0.2;
        position: fixed;
        top: 280px;
        left: 0;
        width: 100%;
        height: 800px;
        background: url("{{$logotipo}}") center no-repeat;
        background-size: 500px;
    }

    .doc-title {
        margin-top: 50px;
    }

    .doc-title h1 {
        margin: 0;
    }

    .instituicao-nome {
        font-size: 15pt !important;
        font-weight: bold;
    }
</style>

<div class="div-top"></div>

<p class="institution-name">{{$institution->nome}}</p>
<p class="institution-decree">{{$institution->decreto_instituicao}}</p>

@if($requerimento->codigo_documento == 15)
    <div style="margin-top:120px; margin-bottom:-30px; text-align:center;">
        <p class="instituicao-nome">{{ $instituicao_nome }}</p>
    </div>
@else
    <div class="doc-title">
        <h1 class="h1-title">
            @php
                $titulos = [
                    '1'  => "Declaração sem notas",
                    '2'  => "Declaração com notas",
                    '4'  => "Certificado",
                    '5'  => "Diploma",
                    '6'  => "Declaração de frequência",
                    '8'  => "Declaração de fim de curso",
                    '9'  => "Declaração com notas de exame de acesso",
                    '10' => "Pedido de equivalência (de Entrada no ISPK)",
                    '11' => "Pedido de transferência (de saída no ISPK)"
                ];
                echo $titulos[$requerimento->codigo_documento] ?? '';
            @endphp
        </h1>
    </div>
@endif
