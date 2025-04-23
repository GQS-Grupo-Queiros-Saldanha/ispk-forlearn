@extends('layouts.print')
@section('content')
@section('title', 'Tabelas de Emolumentos')
<style>
    @import url('https://fonts.cdnfonts.com/css/times-new-roman');

    .cabecalho {
        font-family: 'Times New Roman';
        text-transform: uppercase;
        margin-top: 15px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        /* Centraliza o texto no meio das células */
    }

    .cabecalho>*,
    .titulo>* {
        padding: 0;
        margin: 0;
        padding-top: 3px;
    }

    .cabecalho .instituition,
    .cabecalho .area,
    .titulo p {
        font-size: 1rem;
        font-weight: 700;
    }

    .cabecalho .instituition {
        font-size: 20px !important;
        letter-spacing: 1px;
        padding-bottom: 0px;
        margin-bottom: 0px;

    }

    .cabecalho .area {
        padding-top: 0px;
    }

    .cabecalho .decreto {
        font-size: 0.5rem;
        text-align: left;
        text-indent: 210px;
        padding-top: 0px;
        top: -10;

        position: relative;
    }

    .cabecalho .logotipo {
        width: 76px;
        height: 96px;
    }


    .table {
        width: 100%;
        margin-bottom: 1rem;
        text-align: center;
    }

    .table th,
    .table td {
        border: 1px solid #ffffff;
        padding: 0.75rem;
        text-align: center;
    }

    .table tr {
        border: 1px solid #ffffff;
        padding: 0.75rem;
        text-align: center;
    }

    .table th {
        background-color: #ffffff;
        color: #080808;
        text-align: center;
    }

    .title-dom {
        font-size: 80px;
        font-weight: bold;
        color: #243f60;
    }

    .title-dom2 {
        font-size: 32px;
        color: #243f60;
    }

    .text-color {
        color: #fc8a17;
    }

    .data,
    .assinaturas {
        font-size: 22px;
        text-align: right;
        margin: 0 auto;
        margin-top: 50px;
    }


    .container {
        width: 100%;
    }

    .cabecalho .logotipo {
        width: 76px;
        height: 96px;
    }

    .logotipo img {
        width: 485px !important;
        height: auto;
    }

    .f-blue {
        color: #243f60 !important;
    }

    .t-color {
        color: #fc8a17;
    }

    .titulo p {
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .titulo .a {
        padding-top: 30px;
        padding-bottom: 5px;
    }

    .cor_linha {
        background-color: #ffffff;
        color: #000;
    }


    .last-line td {
        background-color: #ffffff;
    }

    .line td {
        background-color: #ffffff;
    }

    .data,
    .assinaturas {
        font-size: 22px;
        margin-left: 12%;
        text-align: right;
        margin-right: 100px;
        margin-top: 50px;
    }

    .bg0 {
        background-color: #2f5496 !important;
        color: white;
    }

    .bg1 {
        background-color: #8eaadb !important;
        text-align: center;
    }

    .bg2 {
        background-color: #d9e2f3 !important;
    }

    .bg3 {
        background-color: #fbe4d5 !important;
    }

    .bg4 {
        background-color: #f4b083 !important;
    }

    .f1 {
        font-size: 14pt !important;
    }

    .f2 {
        font-size: 13pt !important;
    }

    .f3 {
        font-size: 12pt !important;
    }

    .f4 {
        font-size: 11pt !important;
    }

    .pd {
        width: 60px;
    }

    .pd1 {
        width: 70px;
    }

    .strange {
        color: #1c65e5;
    }

    .title-dom {
        font-size: 60px;
        font-weight: bold;
        text-align: left;
        margin-top: 35px;
        margin-left: 90px;
        margin-bottom: 5px;
        color: #243f60;
        text-transform: UPPERCASE;
    }

    .title-dom1 {
        font-weight: none;
        font-size: 40px;
        color: #243f60;
        margin-left: 0px;
        margin-bottom: 70px;
        margin-top: 10px;
        text-transform: UPPERCASE;
        font-weight: bold;
        margin: 10px;
    }


    .title-dom2 {
        font-weight: none;
        font-size: 22px;
        color: #243f60;
        margin-left: 100px;
    }

    thead th {
        text-align: center;
        /* Centraliza o texto no cabeçalho */
    }

    th {
        font-size: 19px;
        font-weight: bold;
        text-transform: UPPERCASE;
        color: #000000;
        text-align: center;
    }

    .table th.atividades-header {
        background-color: #dbe2e6;
        color: #333;
        font-size: 45px;
        font-weight: bold;
        text-align: top;
    }

    /* jfjf */

    th,
    td {
        padding: 4px;
        /* Padding padrão das células */
        text-align: left;
        border: 10px solid white;
        /* Aumenta a espessura e define a cor das bordas */
        font-family: 'IBM Plex Mono', monospace;
    }

    th {
        background-color: #8eaadb !important;
        color: white;
        font-size: 1.1em;
        /* Ajusta o tamanho da fonte dos cabeçalhos */
        text-align: center;
        /* Centraliza o texto dos cabeçalhos */
        font-weight: bold;
        /* Torna a fonte mais espessa */
    }

    td.number-cell {
        background-color: #2f5496;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 2em;
        /* Aumenta o tamanho da fonte dos números */
        font-family: 'Bebas Neue', sans-serif;
        /* Aplica a fonte para números */
        padding: 4px;
        /* Reduz o padding das células de números para colar os números nas bordas */
        width: 0.5em;
        /* Define uma largura fixa para as células de números */
        min-width: 0.5em;
        /* Garante que as células de números não fiquem menores que a largura definida */
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .invisible {
        visibility: hidden;
        /* Torna o conteúdo invisível mas mantém o espaço reservado */
        border: none;
        /* Remove as bordas da célula invisível */
    }

    .header-title {
        background-color: #2f5496 !important;
        /* Cor de fundo */
        color: white;
        /* Cor do texto */
        font-size: 1.5em;
        /* Tamanho da fonte do título */
        text-align: center;
        /* Centraliza o texto do título */
        font-weight: bold;
        /* Fonte em negrito */
        padding: 10px;
        /* Padding para o título */
        border: 10px solid white;
        /* Borda do título */
        text-transform: uppercase;
    }


    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: auto;
        overflow: hidden;
    }

    thead {
        display: table-header-group;
    }

    tbody {
        display: table-row-group;
    }

    .page-break {
        page-break-before: always;
        margin-top: 50px;
        /* Ajuste a margem conforme necessário */
    }

    .page-break {
        page-break-before: always;
        margin: 50px 0;
    }


    table td:nth-child(1),
    table th:nth-child(1) {
        width: 50px;
        /* Ajuste conforme necessário */
    }

    table td:nth-child(2),
    table th:nth-child(2) {
        width: 50px;
        /* Ajuste conforme necessário */
    }

    table td:nth-child(3),
    table th:nth-child(3) {
        width: 290px;
        /* Ajuste conforme necessário */
    }


    table td:nth-child(4),
    table th:nth-child(4) {
        width: 40px;
        /* Ajuste conforme necessário */
    }

    /* Define a largura fixa para a coluna OBS */
    table td:nth-child(5),
    table th:nth-child(5) {
        width: 130px;
        /* Ajuste conforme necessário */
    }
</style>

<main>
    <title>Tabela de Emolumento</title>
    <div class="row">
        <div class="col-12">
            <br>
            <center>
                @php $url = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo; @endphp
                <div class="logotipo" style="margin-top:110px;">
                    <img src="{{ $url }}" style="width: 400px !important; height: auto;" class=""
                        srcset="">
                </div>
            </center>
            <h3 class="title-dom"> <br>
                <br>
                Tabela de Emolumentos
            </h3>
            <br>
            <br>
            <b class="title-dom2">
                <b>Ano lectivo:</b>
                <span class="text-color">{{ $lectiveYears[0]->currentTranslation->display_name }}</span>
            </b>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    
    <div class="data">
        <span style="text-transform: capitalize;">{{ $institution->municipio }}</span>, aos
        @php
            $m = date('m');
            $mes = [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro',
            ];
            echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
        @endphp
        <br>
        <span class="text-color">Powered by</span> <b style="color:#243f60;font-size: 20px;">forLEARN<sup>®</sup></b>
    </div>

    <main id="content">
        @foreach ($model as $categoryName => $articles)
            <div class="page-break">
                <table class="article-table">
                    <thead>
                        <tr>
                            <th class="header-title" colspan="10">{{ $categoryName }}</th>
                        </tr>
                        <tr>
                            <th class="invisible">#</th>
                            <th>SIGLA</th>
                            <th>DESIGNAÇÃO</th>
                            <th>PREÇO</th>
                            <th>OBSERVAÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $index => $article)
                            <tr>
                                <td class="number-cell">{{ $index + 1 }}</td>
                                <td>{{ $article->acronym }}</td>
                                <td>{{ $article->display_name }}</td>
                                <td class="text-right"><b>{{ number_format($article->base_value, 0, ',', '.') }}</b></td>
                                <td>
                                    @foreach (explode(';', $article->observation) as $obs)
                                        {{ $obs }}<br>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </main>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tables = document.querySelectorAll('.article-table');

        tables.forEach(function(table) {
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');

            let currentPageHeight = 0;
            const pageHeightLimit = 800; // Ajuste conforme necessário, dependendo do tamanho da página

            rows.forEach(function(row, index) {
                currentPageHeight += row.clientHeight;

                if (currentPageHeight > pageHeightLimit) {
                    // Cria um novo cabeçalho ao ultrapassar o limite de altura da página
                    const newThead = thead.cloneNode(true);
                    const newTable = document.createElement('table');
                    newTable.classList.add('article-table');
                    newTable.appendChild(newThead);

                    // Adiciona as linhas subsequentes na nova tabela
                    const newTbody = document.createElement('tbody');
                    newTable.appendChild(newTbody);
                    for (let i = index; i < rows.length; i++) {
                        newTbody.appendChild(rows[i]);
                    }

                    // Insere a nova tabela após a tabela original
                    table.parentNode.insertBefore(newTable, table.nextSibling);

                    // Remove as linhas transferidas da tabela original
                    for (let i = rows.length - 1; i >= index; i--) {
                        tbody.removeChild(rows[i]);
                    }

                    currentPageHeight = 0; // Reinicia a altura da página para a nova tabela
                }
            });
        });
    });
</script>
@endsection
