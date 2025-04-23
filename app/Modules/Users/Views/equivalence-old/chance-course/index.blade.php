<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Mudança de curso')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Mudança de curso</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="change-curso-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th id="dado">#</th>
                <th>Curso inicial</th>
                <th>Curso novo</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::equivalence.chance-course.modal_create')
    @include('Users::equivalence.chance-course.modal_equivalence_change')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            let btnAdd = $('#btn_add');
            let listEquivalencia = $('#list_equivalencia');
            let array1 = [];
            let array2 = [];
            let array_ids = [];

            Tabela();

            $("#modal_create_save").click(function() {
                $("#formChangeCourse").submit();
            });

            $("#close_modal_create").click(function() {
                $("#CreateCursoChange").modal('hide');
            });

            let id_anoLective = $("#lective_years");

            id_anoLective.bind('change keypress', function() {
                id_anoLective = $("#lective_years").val();
                console.log(id_anoLective)
            });


            $(".new_matricula").click(function(e) {
                id_anoLective = $("#lective_years").val();
                $(this).attr('href', 'confirmation_matriculation/create/' + id_anoLective);
            });

            $("#modal_create_save_ajax").click(function(e) {
                if (array_ids.length > 0) {
                    $.ajax({
                        url: "{{ route('courses.change.disciplina.store') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            items: array_ids
                        },
                        success: function(response) {
                            if (response.status && response.total == 'yes') {
                                alert('foi inserido com successo, todos items');
                            }
                            if (response.total == 'parcial') {}
                        }
                    });
                    array1 = [];
                    array2 = [];
                    array_ids = [];
                    $('#list_equivalencia').html(" ");
                }
            });

            $("#close_modal_create").click(function(e) {
                $("#CreateCursoChangeEquivalence").modal('hide');
            });

            btnAdd.click(function(e) {
                let select1 = $('#id_curso_1');
                let select2 = $('#id_curso_2');
                if (select1.val() != null && select2.val() != null && !array1.includes(select1.val()) && !array2
                    .includes(select2.val())) {
                    array1.push(select1.val());
                    array2.push(select2.val());
                    let obj = {
                        id_change_course: btnAdd.val(),
                        first_discipline_course: select1.val(),
                        second_discipline_course: select2.val()
                    };
                    array_ids.push(obj);
                    let nome1 = "";
                    let nome2 = "";
                    select1.children().each(function(index) {
                        if (this.value == select1.val()) nome1 = this.innerHTML;
                    });
                    select2.children().each(function(index) {
                        if (this.value == select2.val()) nome2 = this.innerHTML;
                    });
                    let html = "<li class='list-group-item p-1' id='list-" + array_ids.length + "'>" +
                        "<span class='cursor-pointer del-item' size='" + array_ids.length +
                        "' first-disc-course='" + obj.first_discipline_course + "' second-disc-course='" + obj
                        .second_discipline_course + "'>" +
                        "<i class='fas fa-times-square text-danger mr-2'></i>" +
                        "</span>" +
                        "<span class='h3 min-w'>" + nome1 + "</span>" +
                        "<span class='text-success ml-2 mr-2'> = </span>" +
                        "<span class='h3 min-w'>" + nome2 + "</span>" +
                        "</li>";
                    listEquivalencia.append(html);
                    if (array_ids.length > 0)
                        eliminator();
                } else {
                    if (select1.val() != null && array1.includes(select1.val()))
                        alert("valor da primeira caixa de seleção já foi escolhido");
                    if (select2.val() != null && array2.includes(select2.val()))
                        alert("valor da segunda caixa de seleção já foi escolhido");
                }
            });

            function eliminator() {
                $('.del-item').click(function(e) {
                    let obj = $(this);
                    let item = $('#list-' + obj.attr('size'));
                    array1 = array1.filter(function(e) {
                        return e != obj.attr('first-disc-course');
                    });
                    array2 = array2.filter(function(e) {
                        return e != obj.attr('second-disc-course');
                    });
                    array_ids = array_ids.filter(function(e) {
                        return e.first_discipline_course != obj.attr('first-disc-course') && e
                            .second_discipline_course != obj.attr('second-disc-course');
                    })
                    item.remove();
                });
            }

            $("#lective_years").change(function() {
                $('#change-curso-table').DataTable().clear().destroy();
                Tabela();
            });

            function Tabela() {
                let Id_lective = $("#lective_years").val();

                let AnoDataTable = $('#change-curso-table').DataTable({
                    ajax: {
                        url: "courses-change-ajax/" + Id_lective,
                        "type": "GET"
                    },buttons: ['colvis', 'excel',{
                        text: '<i class="fas fa-plus-square"></i> Novo',
                        className: 'btn-primary main ml-1 rounded btn-main new_change_course',
                        action: function(e, dt, node, config) {
                            let text_year = $("#lective_years").val();
                            $("#TituloCreatechange").text("Associar cursos");
                            $("#InputYear").val(text_year);
                            $("input").text('');
                            $("#CreateCursoChange").modal('show');
                        }
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false

                        }, {
                            data: 'curso_de',
                            name: 'ct1.display_name'
                        }, {
                            data: 'curso_para',
                            name: 'ct2.display_name'
                        },
                        {
                            data: 'criado_por',
                            name: 'u1.name',
                            visible: false
                        }, {
                            data: 'actualizado_por',
                            name: 'u2.name',
                            visible: false
                        }, {
                            data: 'criado_a',
                            name: 'change.created_at',
                            visible: false
                        }, {
                            data: 'actualizado_a',
                            name: 'change.updated_at',
                            visible: false
                        }, {
                            data: 'actions',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }


                    ],

                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
                AnoDataTable.page('first').draw('page');
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            }
        })();
    </script>
@endsection
