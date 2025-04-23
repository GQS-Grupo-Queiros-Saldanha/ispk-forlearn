<script>
  $(document).ready(function(){
    var table = $('.data-table').DataTable({

      //Configuração das colunas não visíveis
      "columnDefs": [
             {
                 "targets": [1],
                 "visible": false,
             },

              {
                 "targets": [7],
                 "visible": false,
             }
            
         ],

        //Configuração do plugins dos botões para (impimir, copiar, pdf, e excel)
        dom: 'Bfrtip',
        buttons: [
            {
                 extend: 'pageLength',
                 text: 'Mostrar Registos'
             },
             {
                 extend: 'colvis',
                 text: 'Colunas Visíveis <i class="fas fa-sort-down"></i>',
                 stateSave: true,
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
                },
                customize: function(win){
                    $(win.document.body)
                    .css('font-size', '10pt')
                    .prepend(
                            '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'
                        );;
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
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
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
  });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

     var rules_basic = {
  condition: 'AND',
  rules: [

   {
    condition: 'OR',
    rules: [{
      id: 'user_name',
      operator: 'equal',
    },
    ]
  }]
};

$('#builder-basic').queryBuilder({

  filters: [

    {
    id: 'courses_id',
    label: 'Curso',
    type: 'integer',
    input: 'select',
    values: {
        11: 'Ensino de Biologia',
        15: 'Psicologia (Psicologia Jurídica)',
        16: 'Ensino de História',
        18: 'Ensino de Psicologia',
        19: 'Educação Física e Desportos',
        20: 'Direito',
        21: 'Ensino de Sociologia',
        22: 'Ensino de Pedagogia',
        23: 'Relações Internacionais',
        25: 'Ciências Económicas e Empresariais',
        26: 'Ensino de Geografia',
        27: 'Gestão de Recursos Humanos',
        28: 'Engenharia Informática'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },

   /*  {
    id: 'display_name',
    label: 'Nome da disciplina',
    type: 'string'
  },
*/
  {
    id: 'code',
    label: 'Código da Disciplina',
    type: 'string'
  },

  {
    id: 'discipline_area_id',
    label: 'Área da disciplina',
    type: 'integer',
    input: 'select',
    values: {
        4: 'Matemática',
        5: 'Programação',
        6: 'Gestão',
        7: 'Electrónica',
        9: 'Multimedia',
        10: 'Ciências e Educação',
        13: 'Geral',
        14: 'Específíca',
        15: 'Profissional',
        16: 'Línguas',
        17: 'Jurídicas',
        18: 'Exame de admissão'
         },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },

  {
    id: 'discipline_profiles_id',
    label: 'Perfil da disciplina',
    type: 'integer',
    input: 'select',
    values: {
        1: 'Normal',
        2: 'Estágio',
        3: 'Dissertação',
        4: 'Opcional',
        8: 'Exame de admissão'
         },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  {
    id: 'created_at',
    label: 'Data ',
    type: 'datetime',
    input: 'text',
    validation: {
      
    },
    placeholder: "AA-MM-DD HH:MM",
    plugin: 'datetimepicker',
    plugin_config: {
     
    },
    operators: ['between','less', 'less_or_equal', 'greater', 'greater_or_equal']
  },

],


});
$('#exampleModalCenter').modal('hide');
$('#btn-get').on('click', function() {
  var result = $('#builder-basic').queryBuilder('getRules');
  $('#exampleModalCenter').modal('show');
  //if (!$.isEmptyObject(result)) {
    //alert(JSON.stringify(result, null, 2));
  //}
  //console.log(result);
    $.ajax({
        url: "{{ route('disciplinegetResults') }}",
        type: "POST",
        data: (JSON.stringify(result)),
        processData:true,
        contentType: 'application/json; charset=utf-8',
        success: function (response)
        {
            //console.log(response);
            // table.draw();

        }
    }).done(
        function(data)
        {   
            $('#exampleModalCenter').modal('hide');
            //table.draw();
             $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
             $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
        }
    )
});
</script>