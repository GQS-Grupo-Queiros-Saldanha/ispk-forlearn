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
    id: 'display_name',
    label: 'Emolumento',
    type: 'integer',
    input: 'select',
    values: {
        'Inscrição ao Exame de Acesso': 'Exame de Acesso',
        'Confirmação de matrícula': 'Matrícula',
        'Anulação de Matrícula': 'Anulação de Matrícula',
        'Certificado': 'Certificado',
        'Decoração da Sala Para Defesa de Fim de Curso': 'Decoração da Sala Para Defesa de Fim de Curso',
        'Decoração da Sala Para Defesa de Fim de Curso Por Terceiros': 'Decoração da Sala Para Defesa de Fim de Curso Por Terceiros',
        'Declaração de Frequência': 'Declaração de Frequência',
        'Declaração de Frequência Urgente': 'Declaração de Frequência Urgente',
        'Declaração de Licenciatura': 'Declaração de Licenciatura',
        'Declaração com Notas': 'Declaração com Notas',
        'Declaração com Notas Urgente': 'Declaração com Notas Urgente',
        'Diploma': 'Diploma',
        'Declaração Sem Notas': 'Declaração Sem Notas',
        'Declaração Sem Notas Urgente': 'Declaração Sem Notas Urgente',
        'Emissão de Cartão de Estudante': 'Emissão de Cartão de Estudante',
        'Exame Extraordinário': 'Exame Extraordinário',
        'Exame de Recuperação': 'Exame de Recuperação',
        'Folha de Prova': 'Folha de Prova',
        'Integração Curricular': 'Integração Curricular',
        'Inscrição Por Exame Cadeira Em Atraso': 'Inscrição Por Exame Cadeira Em Atraso',
        'Inscrição Por Frequência Cadeira Em Atraso': 'Inscrição Por Frequência Cadeira Em Atraso',
        'Mudança de Curso': 'Mudança de Curso',
        'Melhoria de Notas': 'Melhoria de Notas',
        'Pedido de Equivalência Por Disciplina': 'Pedido de Equivalência Por Disciplina',
        'Pedido de Transferência': 'Pedido de Transferência',
        'Pré-Matrícula': 'Pré-Matrícula',
        'Pagamento de Propina - Curso de Licenciatura de Biologia': 'Propina - Curso de Lic. de Biologia',
        'Pagamento de Propina - Curso de Licenciatura em Ciências Económicas e Empresariais': 'Propina - Curso de Lic. em C. Econ. e Empresariais',
        'Pagamento de Propina - Curso Licenciatura em Direito': 'Propina - Curso Lic. em Direito',
        'Pagamento de Propina - Curso Licenciatura em Educação Física e Desporto': 'Propina - Curso Lic. em Ed. Física e Desporto',
        'Pagamento de Propina - Curso Licenciatura em Ensino de Geografia': 'Propina - Curso Lic. em Ensino de Geografia',
        'Propina - Licenciatura em Gestão de Recursos Humanos': 'Propina - Lic. em Gestão de Recursos Humanos',
        'Proprina - Curso Licenciatura em Psicologia (Psicologia Jurídica)': 'Propina - Curso Lic. em Psicologia Jurídica',
        'Pagamento de Propina - Curso Licenciatura em Ensino de História': 'Propina - Curso Lic. em Ensino de História',
        'Pagamento de Propina - Curso Licenciatura em Ensino de Pedagogia': 'Propina - Curso Lic. em Ensino de Pedagogia',
        'Pagamento de Propina - Curso Licenciatura em Ensino de Psicologia': 'Propina - Curso Lic. em Ensino de Psicologia',
        'Pagamento de Propina - Curso Licenciatura em Relações Internacionais': 'Propina - Curso Lic. em Relações Internacionais',
        'Pagamento de Propina - Curso Licenciatura em Ensino de Sociologia': 'Propina - Curso Lic. em Ensino de Sociologia',
        'Pagamento de Propina - Curso Licenciatura em Engenharia Informática': 'Propina - Curso Lic. em Engenharia Informática',
        'Proprina - Curso Licenciatura Cieñcia e  (Constabilidade e Auditorio)': 'Propina - Lic. em CEE (Contabilidade e Auditoria)',
        'Propina - Curso de Licenciatura em CEE (Economia)': 'Propina - Lic. em CEE (Economia)',
        'Propina - Curso de Licenciatura em CEE (Gestão de empresas)': 'Licenciatura em CEE (Gestão de empresas)',
        'Inscrição ao Exame de Recurso': 'Inscrição ao Exame de Recurso',
        'Revisão de Prova': 'Revisão de Prova'
    },
    operators: ['equal', 'not_equal']
  },

   
  {
    id: 'status',
    label: 'Estado',
    type: 'string',
    input: 'select',
    values: {
        total: 'Pago',
        pending: 'Por pagar',
        partial: 'Parcial'
    },
    operators: ['equal']
  },

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

  {
    id: 'code',
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

/*
{
    id: 'code',
    label: 'Turma do Estudante',
    type: 'integer',
    input: 'select',
    values: {
        10: 'BIO1M1 - Biologia - Manhã',
        11: 'BIO1T1 - Biologia - Tarde',
        12: 'BIO1N1 - Biologia - Noite',
        13: 'DTO1M1 - Direito  - Manhã',
        14: 'DTO1T1 - Direito  - Tarde',
        15: 'DTO1N1 - Direito  - Noite',
        16: 'GEO1M1 - Geografia - Manhã',
        17: 'GEO1T1 - Geografia - Tarde',
        18: 'HIS1N1 - História  - Noite',
        19: 'PED1M1 - Pedagogia - Manhã',
        20: 'PED1T1 - Pedagogia - Tarde',
        21: 'PED1N1 - Pedagogia - Noite',
        22: 'PSG1M1 - Psicologia - Manhã',
        23: 'PSG1T1 - Psicologia - Tarde',
        24: 'PSG1N1 - Psicologia - Noite',
        25: 'SOC1M1 - Sociologia - Manhã',
        26: 'SOC1T1 - Sociologia - Tarde',
        27: 'DEF1M1 - Educação Fisíca e Desportos - Manhã',
        28: 'INF1T1 - Informática - Tarde',
        29: 'CEE1M1 - Ciências Económicas Empresariais - Manhã',
        30: 'CEE1T1 - Ciências Económicas Empresariais - Tarde',
        31: 'CEE1N1 - Ciências Económicas Empresariais - Noite',
        32: 'GRH1M1 - Gestão de Recursos Humanos - Noite',
        33: 'GRH1T1 - Gestão de Recursos Humanos - Tarde',
        34: 'GRH1N1 - Gestão de Recursos Humanos - Noite',
        35: 'PSI1N1 - Psicologia Jurídica - Noite',
        36: 'RI1M1  - Relações Internacionais - Manhã',
        37: 'RI1T1  - Relações Internacionais - Tarde',

        38: 'BIO2M1  - Biologia2 - Manhã',
        39: 'BIO2T1  - Biologia2 - Tarde',
        40: 'BIO2N1  - Biologia2 - Noite',
        41: 'BIO3M1  - Biologia3 - Manhã',
        42: 'BIO3T1  - Biologia3 - Tarde',
        43: 'BIO4M1  - Biologia4 - Manhã',
        44: 'BIO4N1  - Biologia4 - Noite',
        45: 'CEE2M1  - Ciências Económicas Empresariais2 - Manhã',
        46: 'CEE2N1  - Ciências Económicas Empresariais2 - Noite',
        47: 'COA4M1  - Ciências Económicas Empresariais4 - Manhã',
        48: 'COA4N1  - Ciências Económicas Empresariais4 - Noite',
        50: 'DEF2M1  - Educação Fisíca e Desportos2 - Manhã',
        51: 'DEF2T1  - Educação Fisíca e Desportos2 - Tarde',
        53: 'DEF3T1 - Educação Fisíca e Desportos3 - Tarde',
        54: 'DTO2M1 - Direito2  - Manhã',
        55: 'DTO2N1 - Direito2  - Noite',
        56: 'DTO2T1 - Direito2  - Tarde',
        57: 'DTO3M1 - Direito3  - Manhã',
        58: 'DTO3N1 - Direito3  - Noite',
        59: 'DTO3T1 - Direito3  - Tarde',
        60: 'DTO4M1 - Direito4  - Manhã',
        61: 'DTO4T1 - Direito4  - Tarde',
        62: 'DTO4N1 - Direito4  - Noite',
        63: 'DTO5T1 - Direito5  - Tarde',
        64: 'DTO5N1 - Direito5  - Noite',
        65: 'GEO2T1 - Geografia2 - Tarde',
        66: 'GEO2N1 - Geografia2 - Noite',
        67: 'GEO3T1 - Geografia3 - Tarde',
        68: 'GEO4T1 - Geografia4 - Tarde',
        69: 'GRH2M1 - Gestão de Recursos Humanos2 - Manhã',
        70: 'GRH2N1 - Gestão de Recursos Humanos2 - Noite',
        71: 'GRH2T1 - Gestão de Recursos Humanos2 - Tarde',
        72: 'GRH3M1 - Gestão de Recursos Humanos3 - Manhã',
        73: 'GRH3T1 - Gestão de Recursos Humanos3 - Tarde',
        74: 'GRH4M1 - Gestão de Recursos Humanos4 - Manhã',
        75: 'GRH4T1 - Gestão de Recursos Humanos4 - Tarde',
        76: 'GRH4N1 - Gestão de Recursos Humanos4 - Noite',
        77: 'HIS2M1 - História2 - Manhã',
        78: 'HIS2N1 - História2 - Noite',
        79: 'HIS3T1  - História3 - Tarde',
        81: 'HIS4N1 - História4 - Noite',
        82: 'INF2M1  - Informática2 - Manhã',
        83: 'INF2N1  - Informática2 - Noite',
        84: 'INF3M1  - Informática3 - Manhã',
        85: 'INF3N1  - Informática3 - Noite',
        86: 'INF4M1  - Informática4 - Manhã',
        87: 'INF5N1  - Informática5 - Noite',
        88: 'PED2M1  - Pedagogia2 - Manhã',
        89: 'PED2T1  - Pedagogia2 - Tarde',
        90: 'PED2N1  - Pedagogia2 - Noite',
        91: 'PED3M1  - Pedagogia3 - Manhã',
        92: 'PED3T1  - Pedagogia3 - Tarde',
        93: 'PED4M1  - Pedagogia4 - Manhã',
        94: 'PED4T1  - Pedagogia4 - Tarde',
        95: 'PED4N1  - Pedagogia4 - Noite',
        96: 'PSG2M1  - Psicologia2 - Manhã',
        97: 'PSG2T1 - Psicologia2 - Tarde',
        98: 'PSG2N1 - Psicologia2 - Noite',
        99: 'PSG3M1 - Psicologia3 - Manhã',
        100: 'PSG3T1 - Psicologia3 - Tarde',
        101: 'PSG4M1 - Psicologia4 - Manhã',
        102: 'PSG4T1 - Psicologia4 - Tarde',
        103: 'PSG4N1 - Psicologia4 - Noite',
        104: 'PSJ2N1 - Psicologia Jurídica2 - Noite',
        105: 'PSJ3N1 - Psicologia Jurídica3 - Noite',
        106: 'PSJ4N1 - Psicologia Jurídica4 - Noite',
        107: 'PSJ5N1 - Psicologia Jurídica5 - Noite',
        108: 'RI2M1 - Relações Internacionais2 - Manhã',
        109: 'RI3T1 - Relações Internacionais3 - Tarde',
        110: 'RI3N1 - Relações Internacionais3 - Noite',
        111: 'RI4M1 - Relações Internacionais4 - Manhã',
        112: 'RI4N1 - Relações Internacionais4 - Noite',
        113: 'SOC2M1 - Sociologia2 - Manhã',
        114: 'SOC2T1 - Sociologia2 - Tarde',
        115: 'SOC3M1 - Sociologia3 - Manhã',
        116: 'SOC3N1 - Sociologia3 - Noite',
        117: 'SOC4T1 - Sociologia4 - Tarde',

        118: 'GEE3M - Ciências Económicas Empresariais3 - Manhã',
        119: 'ECON3T1 - Ciências Económicas Empresariais3 - Tarde',
        120: 'COA3N1 - Ciências Económicas Empresariais3 - Noite',
        121: 'GEE4N - Ciências Económicas Empresariais4 - Noite',
        122: 'DEF4T1 - Educação Fisíca e Desportos4 - Tarde',
        123: 'HIS2T1  - História2 - Tarde',

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },

  {
    id: 'month',
    label: 'Mês',
    type: 'integer',
    input: 'select',
    values: {
        1: 'Janeiro',
        2: 'Fevereiro',
        3: 'Março',
        4: 'Abril',
        5: 'Maio',
        6: 'Junho',
        7: 'Julho',
        8: 'Agosto',
        9: 'Setembro',
        10: 'Outubro',
        11: 'Novembro',
        12: 'Dezembro'
    },
    operators: ['equal']
  },
  */
  /*

   {
    id: 'roles_id',
    label: 'Cargo',
    type: 'integer',
    input: 'select',
    values: {
        6:  'Estudante',
        15: 'Candidato a Estudante'
    },
    operators: ['equal']
  },
   */
/*
 {
    id: 'article_id',
    label: 'Emolumento',
    type: 'integer',
    input: 'select',
    values: {
        19: 'Anulação de Matrícula',
        20: 'Certificado',
        8: 'Confirmação de matrícula',
        28: 'Decoração da Sala Para Defesa de Fim de Curso',
        29: 'Decoração da Sala Para Defesa de Fim de Curso Por Terceiros',
        23: 'Declaração de Frequência',
        24: 'Declaração de Frequência Urgente',
        27: 'Declaração de Licenciatura',
        21: 'Declaração com Notas',
        22: 'Declaração com Notas Urgente',
        30: 'Diploma',
        25: 'Declaração Sem Notas',
        26: 'Declaração Sem Notas Urgente',
        31: 'Emissão de Cartão de Estudante',
        32: 'Exame Extraordinário',
        34: 'Exame de Recuperação',
        35: 'Folha de Prova',
        6: 'Inscrição ao Exame de Acesso',
        37: 'Integração Curricular',
        41: 'Inscrição Por Exame Cadeira Em Atraso',
        42: 'Inscrição Por Frequência Cadeira Em Atraso',
        38: 'Mudança de Curso',
        33: 'Melhoria de Notas',
        39: 'Pedido de Equivalência Por Disciplina',
        40: 'Pedido de Transferência',
        7: 'Pré-Matrícula',
        4: 'Pagamento de Propina - Curso de Lic. de Biologia',
        5: 'Pagamento de Propina - Curso de Lic. em C. Econ. e Empresariais',
        15: 'Pagamento de Propina - Curso Lic. em Direito',
        18: 'Pagamento de Propina - Curso Lic. em Ed. Física e Desporto',
        11: 'Pagamento de Propina - Curso Lic. em Ensino de Geografia',
        16: 'Propina - Lic. em Gestão de Recursos Humanos',
        44: 'Pagamento de Propina - Curso Lic. em Psicologia Jurídica',
        12: 'Pagamento de Propina - Curso Lic. em Ensino de História',
        10: 'Pagamento de Propina - Curso Lic. em Ensino de Pedagogia',
        13: 'Pagamento de Propina - Curso Lic. em Ensino de Psicologia',
        14: 'Pagamento de Propina - Curso Lic. em Relações Internacionais',
        9: 'Pagamento de Propina - Curso Lic. em Ensino de Sociologia',
        17: 'Pagamento de Propina - Curso Lic. em Engenharia Informática',
        45: 'Propina - Licenciatura em CEE (Contabilidade e Auditoria)',
        46: 'Propina - Lic. em CEE (Economia)',
        47: Propina - Curso de Licenciatura em CEE (Gestão de empresas),
        36: 'Inscrição ao Exame de Recurso',
        43: 'Revisão de Prova'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
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
        url: "{{ route('paymentgetResults') }}",
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
