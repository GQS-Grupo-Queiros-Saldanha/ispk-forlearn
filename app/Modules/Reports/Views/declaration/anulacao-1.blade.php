<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste de Fonte Local</title>
    <style>
        @font-face {
            font-family: 'Calibri Light';
            font-style: normal;
            font-weight: 300;
            src: url('/fonts/calibril.woff') format('woff');
        }

        body {
            font-size: 16pt;
            margin: 50px;
        }

        .calibri {
            font-family: 'Calibri Light', sans-serif;
            color: green;
        }

        .padrao {
            font-family: serif;
            color: blue;
        }
    </style>
</head>
<body>
    <h1>Teste de Fonte Local</h1>

    <p class="calibri">
        Este parágrafo está usando a fonte <strong>Calibri Light</strong> local. Se você vê este texto em verde com uma aparência mais leve, a fonte foi carregada com sucesso.
    </p>

    <p class="padrao">
        Este parágrafo está usando a <strong>fonte padrão do sistema</strong> (serif). Ele serve como comparação para validar a aplicação correta da Calibri.
    </p>
</body>
</html>
