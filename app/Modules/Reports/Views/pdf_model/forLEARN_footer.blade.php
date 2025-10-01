<head>
  ...
  <style>
    /* corpo - reserva espaço para o footer na view */
    html,body{
      margin:0;
      padding:0;
      color:#444;
      box-sizing:border-box;
    }
    body{
      padding-bottom: 120px; /* reserva espaço quando estás a ver no browser */
    }

    /* garantir espaço no PDF/print */
    @page {
      /* ajusta estes valores conforme precisares (mm é mais fiável para PDF) */
      margin: 20mm 10mm 25mm 10mm; /* top right bottom left */
    }

    /* footer fixo por página/viewport (mais consistente em preview e PDF) */
    footer.pdf-footer{
      width: 100%;
      position: fixed;   /* prende ao fundo da página */
      left: 0;
      right: 0;
      bottom: 30px;      /* ajusta este valor para subir/baixar o texto */
      z-index: 9999;
      box-sizing: border-box;
      padding: 6px 15mm; /* alinha com as margens da página */
      font-family: Calibri, "Calibri Light", sans-serif !important;
      color: #f7371e !important;
      text-align: left;  /* muda para right/center se preferires */
      pointer-events: none; /* evita interacções desnecessárias */
    }

    /* contenor interno do footer */
    .tb_footer{
      max-width: calc(100% - 30mm); /* deixa espaço para as margens laterais do @page */
      margin: 0 auto;
      width: auto; /* evita largura fixa que pode causar overflow */
      opacity: 0.9;
      overflow: visible; /* garante que nada é cortado internamente */
      display: block;
    }

    /* evita que o texto seja cortado horizontalmente */
    #decima td {
      font-family: "Calibri Light", Calibri, sans-serif;
      padding-left: 0;
      white-space: normal;
      word-break: break-word;
    }

    /* evita usar posicionamento absoluto nas iconices dentro do footer */
    .iconeIMAg {
      height: 18px;
      position: relative; /* antes era absolute/top:20px e isso fazia overlap */
      top: 0;
      vertical-align: middle;
      margin-right: 6px;
    }

    /* utilitário de debug — remove depois de validar */
    /* .pdf-footer { outline: 1px dashed magenta; } */
  </style>
</head>
<body>
  ...
  <!-- Footer corrigido (note a classe pdf-footer e style só para o font-size condicional) -->
  <footer class="pdf-footer" style="
    @if($requerimento->codigo_documento == 2)
      font-size: 11.5pt !important;
    @elseif($requerimento->codigo_documento == 10 || $requerimento->codigo_documento == 6 || $requerimento->codigo_documento == 11 || $requerimento->codigo_documento == 12)
      font-size: 13pt !important;
    @else
      font-size: 15pt !important;
    @endif
  ">
    <div class="tb_footer">
      <table>
        <tr id="decima">
          <td style="@if($requerimento->codigo_documento == 2 || $requerimento->codigo_documento == 10) padding-left:135px; @else padding-left:0px; @endif">
            @if($requerimento->codigo_documento == 2)
              {{$institution->morada}}, {{$institution->provincia}}, Angola
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->dominio_internet}}
            @elseif($requerimento->codigo_documento == 11)
              {{$institution->morada}}, {{$institution->provincia}}, Angola
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->dominio_internet}}
            @elseif($requerimento->codigo_documento == 10 || $requerimento->codigo_documento == 12 || $requerimento->codigo_documento == 6)
              {{$institution->morada}}, {{$institution->provincia}}, Angola
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->telemovel_geral}} | {{$institution->telefone_geral}}
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {{$institution->dominio_internet}}
            @else
              Documento nº {{$requerimento->code ?? 'código doc'}} liquidado com CP nº{{$recibo ?? 'recibo'}}
            @endif
          </td>
        </tr>
      </table>
    </div>
  </footer>
</body>
