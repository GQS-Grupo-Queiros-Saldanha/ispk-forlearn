<title>Avaliações | forLEARN® by GQS</title>
@php
    $isCreate = auth()
        ->user()
        ->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']);
@endphp
@extends('layouts.generic_index_new')
@section('page-title', ' Restaurar mac')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Dispensados - MAC</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @if ($isCreate)
        <a id="criarCalendario" href="" class="btn btn-success mb-3 ml-4 d-none">
            @icon('fas fa-plus-square')
            
        </a>
    @endif
    
    <div class="row">
        <div class="col-6 p-2">
            <label>Selecione o(s) curso(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Curso_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_curso"
                tabindex="-98" >
                @foreach ($courses as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->currentTranslation['display_name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a(s) discipina(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                id="Disciplina_id_Select" data-actions-box="true"  name="id_disciplina"
                tabindex="-98" >
            </select>
        </div>
        <div class="col-6 p-2">
            {{-- <label>Selecione a(s) tuma(s)</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" id="Turma_id_Select"
                data-actions-box="true" data-selected-text-format="values" name="id_turma[]" tabindex="-98" multiple>
            </select> --}}
        </div>
        <div class="col-6 p-2">
            {{-- <label>Selecione à pauta:</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" id="Escala_id_Select"
                data-actions-box="false" data-selected-text-format="values" name="pauta_type" tabindex="-98">
                @forelse ($Pautas as $item)
                    <option value="{{ $item->PautaCode }},{{ $item->NamePauta }}">
                        {{ $item->NamePauta }}
                    </option>
                @empty
                    Sem nenhuma estatística no sistema!
                @endforelse
            </select> --}}
        </div>
    </div>


    <table id="calendarie-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nº matricula</th>
                <th>@lang('Users::users.name')</th>
                <th>Turma</th>
                <th>Disciplina</th>
                <th>Nota do mac</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            urlCriarCalendario();
 
            var Disciplina_Select = $("#Disciplina_id_Select");
            var Curso_id_Select = $("#Curso_id_Select");


            //Pegar nos cursos //E selecionar o ano curricular
            Curso_id_Select.change(function() {
                let id_cursos = Curso_id_Select.val();
                DisciplinaCurso(id_cursos)
            });

                //Pega dsciplina e as turmas
                function DisciplinaCurso(id_cursos) {
                    $.ajax({
                    url: "/avaliations/PegarDisciplina/"+id_cursos,
                    type: "GET",
                    data: {_token: '{{ csrf_token() }}' },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (id_cursos == "") {
                            return false;
                        }
                    }
                    }).done(function(data) {
                    Disciplina_Select.empty();

                    if (data.length) {
                        $.each(data, function(indexInArray, item) {
                            Disciplina_Select.append('<option value="' + item['id_disciplina'] +
                                '">' + item['code_disciplina'] + '-' + item['nome_disciplina'] +
                                '</option>');
                        });
                        Disciplina_Select.prop('disabled', false);
                        Disciplina_Select.selectpicker('refresh');
                    }

                });
               }
            
            Disciplina_Select.change(function() {
                let Disciplina_id = Disciplina_Select.val();
                console.log(Disciplina_id)
                $.ajax({
                    url: "/avaliations/recovery_mac_course_disciplina",
                    type: "GET",
                    data: {_token: '{{ csrf_token() }}' ,
                        course_ids:Curso_id_Select.val(),
                        disciplina_ids:Disciplina_Select.val(),
                        anoLectivo:$("#lective_year").val()
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (Disciplina_id == "") {
                            return false;
                        }
                      }
                    }).done(function(data) {
                    // Disciplina_Select.empty();

                    console.log(data);
                    if (data.length) {
                     
                    }

                });

            });

 















            function urlCriarCalendario() {
                var id_anoLectivo = $("#lective_year").val();
                var url = "{{ url('avaliations/calendarie/getCreate') . '/' }}" + id_anoLectivo;
                var Calendario = $("#criarCalendario").attr('href', url);
            }

            function arrayButtons() {
                let params = ['colvis', 'excel'];
                @if ($isCreate)
                    params.push({
                        text: '<i class="fas fa-plus-square"></i> Restaurar dados',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        attr: {
                            id: "btn_create_can"
                        },
                        action: function(e, dt, node, config) {
                            window.open($('#criarCalendario').attr('href'), "_blank");
                        }
                    });
                @endif
                return params;
            }

            // $('#calendarie-table').DataTable({
            //     ajax: '{!! route('calendarie.ajax') !!}',
            //     buttons: arrayButtons(),
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             orderable: false,
            //             searchable: false
            //         },
            //         {
            //             data: 'code',
            //             name: 'cl.code',
            //             visible: true,
            //             searchable: true
            //         }, {
            //             data: 'name',
            //             name: 'cl.display_name',
            //             searchable: true
            //         }, {
            //             data: 'data_inicio',
            //             name: 'cl.date_start',
            //             searchable: true
            //         }, {
            //             data: 'date_fim',
            //             name: 'cl.data_end',
            //             searchable: true
            //         },
            //         {
            //             data: 'periodo',
            //             name: 'dt.display_name',
            //             searchable: true
            //         },
            //         {
            //             data: 'us_created_by',
            //             name: 'u1.name',
            //             visible: true
            //         },
            //         {
            //             data: 'us_updated_by',
            //             name: 'u2.name',
            //             visible: false
            //         },
            //         {
            //             data: 'created_at',
            //             name: 'created_at',
            //             visible: false
            //         }, {
            //             data: 'updated_at',
            //             name: 'updated_at',
            //             visible: false
            //         }, {
            //             data: 'actions',
            //             name: 'action',
            //             orderable: false,
            //             searchable: false
            //         }
            //     ],
            //     "lengthMenu": [
            //         [10, 50, 100, 50000],
            //         [10, 50, 100, "Todos"]
            //     ],
            //     language: {
            //         url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
            //     }
            // });

            // $("#lective_year").change(function() {
            //     var lective_year = $("#lective_year").val();
            //     var url = "{{ url('avaliations/calendarie/getCreate') . '/' }}" + lective_year;
            //     var Calendario = $("#criarCalendario").attr('href', url);
            //     searchCalendarie("/avaliations/calendarie/getSCalendarie/" + lective_year);
            // })

            // function searchCalendarie(url) {
            //     $('#calendarie-table').DataTable().clear().destroy();
            //     $('#calendarie-table').DataTable({
            //         "ajax": {
            //             "url": url,
            //             "type": "GET",
            //             "data": {
            //                 "user_id": 451
            //             }
            //         },
            //         buttons: arrayButtons(),
            //         columns: [{
            //                 data: 'DT_RowIndex',
            //                 orderable: false,
            //                 searchable: false
            //             },
            //             {
            //                 data: 'code',
            //                 name: 'cl.code',
            //                 visible: true,
            //                 searchable: true
            //             }, {
            //                 data: 'name',
            //                 name: 'cl.display_name',
            //                 searchable: true
            //             }, {
            //                 data: 'data_inicio',
            //                 name: 'cl.date_start',
            //                 searchable: true
            //             }, {
            //                 data: 'date_fim',
            //                 name: 'cl.data_end',
            //                 searchable: true
            //             },
            //             {
            //                 data: 'simestre',
            //                 name: 'dt.display_name',
            //                 searchable: true
            //             },
            //             {
            //                 data: 'us_created_by',
            //                 name: 'u1.name',
            //                 visible: true
            //             },
            //             {
            //                 data: 'us_updated_by',
            //                 name: 'u2.name',
            //                 visible: false
            //             },
            //             {
            //                 data: 'created_at',
            //                 name: 'created_at',
            //                 visible: false
            //             }, {
            //                 data: 'updated_at',
            //                 name: 'updated_at',
            //                 visible: false
            //             }, {
            //                 data: 'actions',
            //                 name: 'action',
            //                 orderable: false,
            //                 searchable: false
            //             }
            //         ],
            //         "lengthMenu": [
            //             [10, 50, 100, 50000],
            //             [10, 50, 100, "Todos"]
            //         ],
            //         language: {
            //             url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
            //         }
            //     });
            // }

        });
        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
