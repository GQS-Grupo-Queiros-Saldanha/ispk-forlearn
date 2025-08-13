<title>Candidaturas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Candidaturas')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Candidaturas</li>
@endsection
@section('buttons')
    <div class="d-none" hidden>
        <a href="{{ route('candidates.create') }}" class="btn btn-primary mb-3" id="candidates-create"></a>
    </div>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="">
        <label>Selecione a fase</label>
        <select name="fase" id="fase_candidate_select" class="selectpicker form-control form-control-sm"
            style="width: 100% !important;">
        </select>
    </div>
@endsection
@section('body')
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Fase</th>
                <th>Candidato</th>
                <th>@lang('Users::users.name') do candidato</th>
                <th>@lang('Users::users.email')</th>
                <th>Curso</th>
                <th>Pagamento</th>
                <th>Matriculado</th>
                <th>BI</th>
                <th>Fotografia</th>
                <th>Certificado</th>             
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>ACÇÕES POSSÍVEIS</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::candidate.modal.modal_gerar_pdf')
    @include('Users::candidate.modal.modal_escolher_curso')
    @include('Users::candidate.modal.modal_users_historico')
    @include('Users::candidate.modal.modal_fase_transferencia')
    @isset($lectiveCandidateNext->id)
        <div id="fase" hidden>{{ $lectiveCandidateNext->id }}</div>
    @endisset
@endsection
@section('body')
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Fase</th>
                <th>Candidato</th>
                <th>@lang('Users::users.name') do candidato</th>
                <th>@lang('Users::users.email')</th>
                <th>Curso</th>
                <th>Pagamento</th>
                <th>Matriculado</th>
                <th>BI</th>
                <th>Fotografia</th>
                <th>Certificado</th>                  
                {{-- <th>Ano lectivo</th> --}}
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>ACÇÕES POSSÍVEIS</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::candidate.modal.modal_gerar_pdf')
    @include('Users::candidate.modal.modal_escolher_curso')
    @include('Users::candidate.modal.modal_users_historico')
    @include('Users::candidate.modal.modal_fase_transferencia')
    @isset($lectiveCandidateNext->id)
        <div id="fase" hidden>{{ $lectiveCandidateNext->id }}</div>
    @endisset
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            const ident = $('#form #user');
            const form = $('#form');
            const message = $('#message');
            const faseNova = $('#fase_nova');
            const modalFase = $('#modalFase');
            const modalEscolher = $('#modalEscolher');
            const modalHistorico = $('#modalHistorico');
            const formMethod = $("#form [name='_method']");
            const lectiveCandidateId = $('#lective_candidate_id');
            const faseCandidateSelect = $("#fase_candidate_select");
            const candidateUrl = $('#candidates-create');
            const table = $('#users-table');

            initLoadData();

            function initLoadData() {
                let lective_year = $("#lective_year").val();
                let url = '{!! route('candidates.ajax') !!}';
                if (lective_year != "") {
                    lectiveFase(lective_year);
                    let fase = $('#fase').html();
                    if(fase && fase != ""){
                        url ='/users/candidatura_fases_ajax_list_users/'+ fase;
                    }
                }
                loadDataUserCandidate(url);
            }

            function closeOrBtn() {
                const btnMains = document.querySelectorAll(".btn-main");
                btnMains.forEach(btnMain => {
                    if (btnMain.classList.contains('btn-primary')) {
                        btnMain.classList.remove('btn-primary')
                        btnMain.classList.add('btn-danger')
                        if(!btnMain.classList.contains('btn-text')){
                            btnMain.innerHTML = `<i class="fas fa-plus-square"></i> Candidaturas fechadas`
                        }
                        btnMain.href = "#"
                    }
                });
            }


            function verifyLectiveYearClosed() {
                const yearLective = $("#lective_year");
                const option = $(`#lective_year option[value="${yearLective.val()}"]`)[0]
                const is_termina = option.dataset.terminado.trim();
                if (is_termina == "0") {
                    const btnTerminado = document.querySelector("#btnTerminado");
                    if (btnTerminado && btnTerminado.innerHTML.trim() == "1") {
                        closeOrBtn();
                        return true;
                    }
                    const faseSelect = $("#fase_candidate_select");
                    const optionFase = $(`#fase_candidate_select option[value="${faseSelect.val()}"]`)[0]
                    const is_termina_fase = optionFase.dataset.terminado.trim()
                    if (is_termina_fase == "1") {
                        closeOrBtn();
                        return true;
                    }
                    return false;
                } else {
                    closeOrBtn();
                    return true;
                }
            }

            table.on('draw.dt', function() {
                verifyLectiveYearClosed();
            });

            function loadDataUserCandidate(url) {
                let lective_year = $("#lective_year").val();
                let tam = table.children('tbody').length;
                if (tam > 0) table.DataTable().clear().destroy();
                table.DataTable({
                    ajax: url,
                    buttons: ['colvis', 'excel', {
                        text: '<i class="fas fa-plus-square"></i> Criar candidato a estudante',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            if (!verifyLectiveYearClosed()) {
                                window.open(candidateUrl.attr('href'), "_blank");
                            }
                        }
                    }, {
                        text: '<i class="fas fa-plus-square"></i> Cria candidato a outra licenciatura',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            if (!verifyLectiveYearClosed()) {
                                window.open('{!! route('candidatura.graduado') !!}', "_blank");
                            }
                        }
                    }
                    @if(auth()->user()->hasAnyRole(['superadmin', 'staff_candidaturas_chefe']))
                    ,
                   
                    {
                        text: 'Gerar registro primário de acesso',
                        className: 'btn-primary main ml-1 rounded btn-text',
                        action: function(e, dt, node, config) {
                          
                                let url = 'users/excel-candidatos/' + lective_year;
                                window.open(url, "_self");
                           
                        }
                    }
                    @endif
                ],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'fase',
                        name: 'fase',
                        searchable: true
                    }, {
                        data: 'cand_number',
                        name: 'cand_number',
                        searchable: true
                    }, {
                        data: 'name_name',
                        name: 'name_name',
                        searchable: true
                    }, {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    }, {
                        data: 'cursos',
                        name: 'cursos',
                        searchable: true
                    }, {
                        data: 'states',
                        name: 'states',
                        searchable: true
                    }, {
                        data: 'matriculation',
                        name: 'matriculation',
                        searchable: true
                    }, {
                        data: 'bi_doc',
                        name: 'bi_doc',
                        searchable: false,
                        orderable: true,
                    }, {
                        data: 'foto',
                        name: 'foto',
                        searchable: false,
                        orderable: true,
                    }, {
                        data: 'diploma',
                        name: 'diploma',
                        searchable: false,
                        orderable: true,
                    }, {
                        data: 'us_created_by',
                        name: 'u1.name',
                        visible: true,
                        searchable: false
                    }, {
                        data: 'us_updated_by',
                        name: 'us_updated_by',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }],
                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

            $("#lective_year").change(function() {
                let lective_year = $("#lective_year").val();
                $('#ano_lective').val(this.value);
                if (lective_year != "") {
                    lectiveFase(lective_year);
                    loadDataUserCandidate(`/users/candidates/getStudentsBy/${lective_year}`);
                }
            })

            faseCandidateSelect.change(function() {
                let objSelected = $(this);
                let id_fase = faseCandidateSelect.val();
                let option = faseCandidateSelect.children("[value='" + id_fase + "']")[0];
                let faseNum = parseInt(option.innerHTML.replace('ª fase', ''));

                faseNova.val(++faseNum);
                lectiveCandidateId.val(id_fase);

                form.attr('action', '{{ route('fase.candidatura.trans.user') }}');
                formMethod.val('PUT');

                let url = id_fase == "" ? '{!! route('candidates.ajax') !!}' : '/users/candidatura_fases_ajax_list_users/' + id_fase;
                if(faseNova){
                    loadDataUserCandidate(url);
                }else{
                    warning('Não foi encontrado a fase seguinte');
                }
            })

            function lectiveFase(lective_year) {
                if (lectiveFase != null && lective_year != "") {
                    $.ajax({
                        url: "/users/candidatura_fases_ajax_lective/" + lective_year,
                        type: 'GET',
                        success: function(data) {
                            $("#fase_candidate_select").empty();
                            if (data.length) {
                                $("#fase_candidate_select").append(
                                    '<option value="" data-terminado="0">Selecione a fase</option>');
                                $.each(data, function(indexInArray, value) {
                                    let html = ``;
                                    let fase = $('#fase').html();
                                    if(fase == value.id){
                                        html = `<option value="${value.id}" data-terminado="${value.is_termina}" selected>${value.fase} ª fase</option>`;
                                    }else{
                                        html = `<option value="${value.id}" data-terminado="${value.is_termina}">${value.fase} ª fase</option>`;
                                    }
                                    $("#fase_candidate_select").append(html);
                                });
                                $("#fase_candidate_select").prop('disabled', false);
                                $("#fase_candidate_select").selectpicker('refresh');
                            }
                        }
                    });
                }
            }

            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        })();
    </script>
@endsection
