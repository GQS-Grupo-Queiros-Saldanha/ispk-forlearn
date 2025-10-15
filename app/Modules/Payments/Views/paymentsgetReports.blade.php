<table class="table table-striped table-hover data-tables">
            <thead>
                <tr>
                    <th>Nº de Ordem</th>
                    <th>Nº de Candidato</th>
                    <th>Nº de Matrícula</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Curso</th>
                    <th>Turma do Candidato</th>
                    <th>Turma do Estudante</th>
                    <th>Emolumento</th>
                    {{--<th>Mês</th>--}}
                    <th>Estado</th>
                    <th>Valor</th>
                    <th>Banco</th>
                    <th>Referência</th>
                    <th>Nota</th>
                    {{--<th>Nº do Recibo</th>--}}
                    <th>Criado Por</th>
                    <th>Actualizado Por</th>
                    <th>Criado a</th>
                    <th>Acualizado a</th>
                    <th>ID da Tansação</th>
                    <th>ID do Pedido</th>
                    <th>ID de Usuário</th>
                </tr>
            </thead>
            <tbody>￼
                @foreach ($rows as $item)
                    <tr>
                        <td></td>
                        <td>{{ $item->code_candidate }}</td>
                        <td>{{ $item->code_matricula }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        @if($item->courses_id == 11)
                        <td>Licenciatura em Ensino de Biologia</td>
                        @elseif($item->courses_id == 15)
                        <td>Licenciatura em Psicologia (Psicologia Jurídica)</td>
                        @elseif($item->courses_id == 16)
                        <td>Licenciatura em Ensino de história</td>
                        @elseif($item->courses_id == 18)
                        <td>Licenciatura em Ensino de Psicologia</td>
                        @elseif($item->courses_id == 19)
                        <td>Licenciatura em Educação Fisíca e Desportos</td>
                        @elseif($item->courses_id == 20)
                        <td>Licenciatura em Direito</td>
                        @elseif($item->courses_id == 21)
                        <td>Licenciatura em Ensino de Sociologia</td>
                        @elseif($item->courses_id == 22)
                        <td>Licenciatura em Ensino de Pedagogia</td>
                        @elseif($item->courses_id == 23)
                        <td>Licenciatura em Relações Internacionais</td>
                        @elseif($item->courses_id == 25)
                        <td>Licenciatura em Ciências Económicas e Empresariais</td>
                        @elseif($item->courses_id == 26)
                        <td>Licenciatura em Ensino de Geografia</td>
                        @elseif($item->courses_id == 27)
                        <td>Licenciatura em Gestão de Recursos Humanos</td>
                        @elseif($item->courses_id == 28)
                        <td>Licenciatura em Engenharia informática</td>
                         @else
                        <td> </td>
                            @endif
                        <td>{{ $item->turma_candidato }}</td>
                        <td>{{ $item->turma_matriculado }}</td>    
                        <td>{{ $item->display_name }}</td>

                      {{--  @if ($item->month == 1){
                        <td>Janeiro</td>}
                    @elseif ($item->month == 2){
                       <td>Fevereiro</td>}
                    @elseif ($item->month == 3){
                       <td>Março</td>} 
                    @elseif ($item->month == 4){
                       <td>Abril</td>}
                    @elseif ($item->month == 5){
                       <td>Maio</td>}    
                    @elseif ($item->month == 6){
                       <td>Junho</td>}
                    @elseif ($item->month == 7){
                       <td>Julho</td>} 
                    @elseif ($item->month == 8){
                       <td>Agosto</td>}
                    @elseif ($item->month == 9){
                       <td>Setembro</td>}   
                    @elseif ($item->month == 10){
                       <td>Outubro</td>} 
                    @elseif ($item->month == 11){
                       <td>Novembro</td>}
                    @elseif ($item->month == 12){
                       <td>Dezembro</td>}   
                    @endif --}}

                    @if ($item->status == "total"){
                        <td>Pago</td>}
                    @elseif ($item->status == "pending"){
                       <td>Por pagar</td>}
                    @elseif ($item->status == "partial"){
                       <td>Pagamento parcial</td>}   
                    @endif
                        <td>{{ $item->value }}</td>
                        {{--<td>{{ $item->code }}</td>--}}
                        <td>{{ $item->banco }}</td>
                        <td>{{ $item->reference }}</td>
                        <td>{{ $item->nota }}</td>
                        <td>{{ $item->created_by }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->updated_at }}</td>
                        
                         <td>{{ $item->id_transaction }}</td>
                        <td>{{ $item->article_requests_id }}</td>
                        <td>{{ $item->user_id }}</td>
                    </tr>￼
                @endforeach
            </tbody>
        </table>
<script>
    $(document).ready(function(){
    var table = $('.data-tables').DataTable({
         "columnDefs": [
              {
                 "targets": [6],
                 "visible": false,
             },
             
             {
                 "targets": [7],
                 "visible": false,
             },

             {
                 "targets": [11],
                 "visible": false,
             },

             {
                 "targets": [12],
                 "visible": false,
             },

             {
                 "targets": [13],
                 "visible": false,
             },

             {
                 "targets": [14],
                 "visible": false,
             },

             {
                 "targets": [15],
                 "visible": false,
             },

             {
                 "targets": [16],
                 "visible": false,
             },

              {
                 "targets": [17],
                 "visible": false,
             },

              {
                 "targets": [18],
                 "visible": false,
             },

             {
                 "targets": [19],
                 "visible": false,
             },

              {
                 "targets": [20],
                 "visible": false,
             }
            
         ],
         "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "Todos"] ],
        //Configuração do plugins dos botões para (impimir,￼ copiar, pdf, e excel)
        dom: 'Bfrtip',
        buttons: [
             {
                 extend: 'pageLength',
                 text: 'Mostrar Registos'
             },
             {
                 extend: 'colvis',
                 text: 'Colunas Visíveis <i class="fas fa-sort-down"></i>',
                 collectionLayout: 'fixed four-column'
             },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-lg fa-file-pdf"></i>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-lg fa-print"></i>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-lg fa-copy"></i>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
        ]
    });
    table.on('order.dt search.dt', function () {
     table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
           cell.innerHTML = i+1;
           table.cell(cell).invalidate('dom');
     });
}).draw();
    });
</script>
