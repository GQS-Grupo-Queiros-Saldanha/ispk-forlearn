<script>
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('index') }}",
        columns: [
            //{data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'roles', name: 'roles'},
            {data: 'created_at', name:'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            //{data: 'action', name: 'action', orderable: false, searchable: false},
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
        25: 'Ciências Económicas e Empresariais',
        20: 'Direito',
        28: 'Engenharia Informática',
        11: 'Ensino de Biologia',
        18: 'Ensino de Psicologia',
        19: 'Educação Física e Desportos',
        21: 'Ensino de Sociologia',
        22: 'Ensino de Pedagogia',
        26: 'Ensino de Geografia',
        16: 'Ensino de História',
        27: 'Gestão de Recursos Humanos',
        15: 'Psicologia Jurídica',
        23: 'Relações Internacionais',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
/*
  {
    id: 'discipline_id',
    label: 'Disciplina',
    type: 'integer',
    input: 'select',
    values: {
        645: 'Exame de admissão(PSGO)',
        654: 'Exame de admissão(GEO0)',
        655: 'Exame de admissão(DTO00)',
        656: 'Exame de admissão(BIO00)',
        658: 'Exame de admissão(HIS00)',
        659: 'Exame de admissão(PSJ00)',
        660: 'Exame de admissão(EFD00)',
        661: 'Exame de admissão(SOC00)',
        662: 'Exame de admissão(PED00)',
        663: 'Exame de admissão(RI00)',
        665: 'Exame de admissão(GRH00)',
        666: 'Exame de admissão(INF000)',
        667: 'Exame de admissão(CEE000)',
        142: 'Língua Estrangeira I'

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  */

  {
    id: 'code',
    label: 'Código da Disciplina',
    type: 'string'
  }, 

  {
    id: 'param_mecanografico',
    label: 'Nº Mecanográfico',
    type: 'string'
  }, 

  {
    id: 'email',
    label: 'E-mail',
    type: 'string'
  },

  {
    id: 'display_name',
    label: 'Turma do Estudante',
    type: 'integer',
    input: 'select',
    values: {
        'BIO1M1': 'BIO1M1 - Manhã',
        'BIO1T1': 'BIO1T1 - Tarde',
        'BIO1N1': 'BIO1N1 - Noite',
        'BIO2M1': 'BIO2M1 - Manhã',
        'BIO2T1': 'BIO2T1 - Tarde',
        'BIO2N1': 'BIO2N - Noite',
        'BIO3M1': 'BIO3M1 - Manhã',
        'BIO3T1': 'BIO3T1 - Tarde',
        'BIO4M1': 'BIO4M1 - Manhã',
        'BIO4N1': 'BIO4N1 - Noite',
        'DTO1M1': 'DTO1M1 - Manhã',
        'DTO1T1': 'DTO1T1 - Tarde',
        'DTO1N1': 'DTO1N1 - Noite',
        'DTO2M1': 'DTO2M1 - Manhã',
        'DTO2N1': 'DTO2N1 - Noite',
        'DTO2T1': 'DTO2T1 - Tarde',
        'DTO3M1': 'DTO3M1 - Manhã',
        'DTO3N1': 'DTO3N1 - Noite',
        'DTO3T1': 'DTO3T1 - Tarde',
        'DTO4M1': 'DTO4M1 - Manhã',
        'DTO4T1': 'DTO4T1 - Tarde',
        'DTO4N1': 'DTO4N1 - Noite',
        'DTO5T1': 'DTO5T1 - Tarde',
        'DTO5N1': 'DTO5N1 - Noite',
        'GEO1M1': 'GEO1M1 - Manhã',
        'GEO1T1': 'GEO1T1 - Tarde',
        'GEO2T1': 'GEO2T1 - Tarde',
        'GEO2N1': 'GEO2N1 - Noite',
        'GEO3T1': 'GEO3T1 - Tarde',
        'GEO4T1': 'GEO4T1 - Tarde',
        'HIS1N1': 'HIS1N1 - Noite',
        'HIS2M1': 'HIS2M1 - Manhã',
        'HIS2N1': 'HIS2N1 - Noite',
        'HIS3T1': 'HIS3T1 -  Tarde',
        'HIS4N1': 'HIS4N1 - Noite',
        'HIS2T1': 'HIS2T1 - Tarde',
        'PED1M1': 'PED1M1 - Manhã',
        'PED1T1': 'PED1T1 - Tarde',
        'PED1N1': 'PED1N1 - Noite',
        'PED2M1': 'PED2M1 - Manhã',
        'PED2T1': 'PED2T1 - Tarde',
        'PED2N1': 'PED2N1 - Noite',
        'PED3M1': 'PED3M1 - Manhã',
        'PED3T1': 'PED3T1 - Tarde',
        'PED4M1': 'PED4M1 - Manhã',
        'PED4T1': 'PED4T1 - Tarde',
        'PED4N1': 'PED4N1 - Noite',
        'PSG1M1': 'PSG1M1 - Manhã',
        'PSG1T1': 'PSG1T1 - Tarde',
        'PSG1N1': 'PSG1N1 - Noite',
        'PSG2M1': 'PSG2M1 - Manhã',
        'PSG2T1': 'PSG2T1 - Tarde',
        'PSG2N1': 'PSG2N1 - Noite',
        'PSG3M1': 'PSG3M1 - Manhã',
        'PSG3T1': 'PSG3T1 - Tarde',
        'PSG4M1': 'PSG4M1 - Manhã',
        'PSG4T1': 'PSG4T1 - Tarde',
        'PSG4N1': 'PSG4N1 - Noite',
        'SOC1M1': 'SOC1M1 - Manhã',
        'SOC1T1': 'SOC1T1 - Tarde',
        'SOC2M1': 'SOC2M1 - Manhã',
        'SOC2T1': 'SOC2T1 - Tarde',
        'SOC3M1': 'SOC3M1 - Manhã',
        'SOC3N1': 'SOC3N1 - Noite',
        'SOC4T1': 'SOC4T1 - Tarde',
        'DEF1M1': 'DEF1M1 - Manhã',
        'DEF2M1': 'DEF2M1 - Manhã',
        'DEF2T1': 'DEF2T1 - Tarde',
        'DEF3T1': 'DEF3T1 - Tarde',
        'DEF4T1': 'DEF4T1 - Tarde',
        'INF1T1': 'INF1T1 - Tarde',
        'INF2M1': 'INF2M1 - Manhã',
        'INF2N1': 'INF2N1 - Noite',
        'INF3M1': 'INF3M1 - Manhã',
        'INF3N1': 'INF3N1 - Noite',
        'INF4M1': 'INF4M1 - Manhã',
        'INF5N1': 'INF5N1 - Noite',
        'CEE1M1': 'CEE1M1 - Manhã',
        'CEE1T1': 'CEE1T1 - Tarde',
        'CEE1N1': 'CEE1N1 - Noite',
        'CEE2M1': 'CEE2M1 - Manhã',
        'CEE2N1': 'CEE2N1 - Noite',
        'GRH1M1': 'GRH1M1 - Noite',
        'GRH1T1': 'GRH1T1 - Tarde',
        'GRH1N1': 'GRH1N1 - Noite',
        'GRH2M1': 'GRH2M1 - Manhã',
        'GRH2N1': 'GRH2N1 - Noite',
        'GRH2T1': 'GRH2T1 - Tarde',
        'GRH3M1': 'GRH3M1 - Manhã',
        'GRH3T1': 'GRH3T1 - Tarde',
        'GRH4M1': 'GRH4M1 - Manhã',
        'GRH4T1': 'GRH4T1 - Tarde',
        'GRH4N1': 'GRH4N1 - Noite',
        'RI1M1': 'RI1M1 - Manhã',
        'RI1T1': 'RI1T1 - Tarde',
        'RI2M1': 'RI2M1 - Manhã',
        'RI3T1': 'RI3T1 - Tarde',
        'RI3N1': 'RI3N1 - Noite',
        'RI4M1': 'RI4M1 - Manhã',
        'RI4N1': 'RI4N1 - Noite',
        'COA4M1': 'COA4M1 -  Manhã',
        'COA4N1': 'COA4N1 - Noite',
        'COA3N1': 'COA3N1 - Noite',
        'PSJ2N1': 'PSJ2N1 - Noite',
        'PSJ3N1': 'PSJ3N1 - Noite',
        'PSJ4N1': 'PSJ4N1 - Noite',
        'PSJ5N1': 'PSJ5N1 - Noite',
        'GEE3M': 'GEE3M - Manhã',
        'GEE4N': 'GEE4N - Noite',
        'PSI1N1': 'PSI1N1 - Noite',
        'ECON3T1': 'ECON3T1 - Tarde',

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },

  {
    id: 'class_id',
    label: 'Turma do Candidato',
    type: 'integer',
    input: 'select',
    values: {
        10: 'BIO1M1 - Manhã',
        11: 'BIO1T1 - Tarde',
        12: 'BIO1N1 - Noite',
        13: 'DTO1M1 - Manhã',
        14: 'DTO1T1 - Tarde',
        15: 'DTO1N1 - Noite',
        16: 'GEO1M1 - Manhã',
        17: 'GEO1T1 - Tarde',
        18: 'HIS1N1 - Noite',
        19: 'PED1M1 - Manhã',
        20: 'PED1T1 - Tarde',
        21: 'PED1N1 - Noite',
        22: 'PSG1M1 - Manhã',
        23: 'PSG1T1 - Tarde',
        24: 'PSG1N1 - Noite',
        25: 'SOC1M1 - Manhã',
        26: 'SOC1T1 - Tarde',
        27: 'DEF1M1 - Manhã',
        28: 'INF1T1 - Tarde',
        29: 'CEE1M1 - Manhã',
        30: 'CEE1T1 - Tarde',
        31: 'CEE1N1 - Noite',
        32: 'GRH1M1 - Noite',
        33: 'GRH1T1 - Tarde',
        34: 'GRH1N1 - Noite',
        35: 'PSI1N1 - Noite',
        36: 'RI1M1 - Manhã',
        37: 'RI1T1 - Tarde',

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
/*
  {
    id: 'value',
    label: 'Nota',
    type: 'string'
  }, 
*/
 
],

  //rules: rules_basic
});

$('#btn-get').on('click', function() {
  var result = $('#builder-basic').queryBuilder('getRules');
  $('#exampleModalCenter').modal('show');
  //if (!$.isEmptyObject(result)) {
    //alert(JSON.stringify(result, null, 2));
  //}
  //console.log(result);
    $.ajax({
        url: "{{ route('gradesgetResults') }}",
        type: "POST",
        data: (JSON.stringify(result)),
        processData:true,
        contentType: 'application/json; charset=utf-8',
        success: function (response)
        {
            $('#exampleModalCenter').modal('hide');
            //console.log(response);
            // table.draw();

        },
        error: function(xhr, status, error){
        $('#exampleModalCenter').modal('hide');
        alert("Erro!" + xhr.status);
    },
    }).done(
        function(data)
        {
            //table.draw();
             $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
             $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
        }
    )
});
</script>
