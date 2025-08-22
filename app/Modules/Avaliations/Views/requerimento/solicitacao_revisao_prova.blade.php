@extends('layouts.generic_index_new', ['breadcrumb_super' => true])
@section('title', __('Marcação de Revisão de Prova'))

@section('page-title')
    @lang('Marcação de Revisão de Prova')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('requerimento.index') }}">Requerimentos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Marcação de Revisão de Prova</li>
@endsection

@section('selects')
    <div class="mb-2 mt-3">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->id }}" selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->id }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
@endsection

@section('body')
    {!! Form::open(['route' => ['student_tfc_store']]) !!}
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            <label>Selecionar curso</label>
                            <select name="course_id" id="courses" class="selectpicker form-control form-control-sm">
                                <option value="" selected></option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->currentTranslation->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group col">
                            <label>Estudante</label>
                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control']) }}
                        </div>
                    </div>
                </div>

                <input type="hidden" id="lectiveY" value="" name="anoLectivo">
            </div>
            
            <hr>
            
            <div class="float-right">
                <button type="submit" class="btn btn-success mb-3">
                    <i class="fas fa-plus-circle"></i>
                    Requerer
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        // Inicialização das variáveis
        const anoLectivo = $("#lectiveY");
        anoLectivo.val($("#lective_year").val());
        
        console.log('Ano lectivo selecionado: ' + anoLectivo.val());

        // Evento de mudança no ano lectivo
        $("#lective_year").change(function() {
            anoLectivo.val($(this).val());
            console.log('Ano lectivo alterado para: ' + anoLectivo.val());
        });

        // Evento de mudança no curso selecionado
        $("#courses").change(function() {
            const course_id = $(this).val();
            const lective_year_matriculation = $("#lective_year").val();

            console.log('Curso selecionado: ' + course_id);
            
            // Requisição AJAX para buscar estudantes finalistas
            $.ajax({
                url: "/avaliations/requerimento/getFinalists/" + course_id + "/" + 
                      lective_year_matriculation + "?type=finalists",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                
                // Sucesso na requisição
                success: function(result) {
                    console.log('Estudantes carregados: ' + result.length);
                    
                    $("#students").prop('disabled', true);
                    $("#students").empty();
                    
                    // Adiciona opção vazia
                    $("#students").append('<option selected value=""></option>');
                    
                    // Preenche o dropdown com os estudantes
                    $.each(result, function(index, row) {
                        $("#students").append(
                            '<option value="' + row.user_id + '">' + 
                            row.name + " #" + row.student_number + 
                            " (" + row.email + ")" + '</option>'
                        );
                    });
                    
                    // Habilita e atualiza o dropdown
                    $("#students").prop('disabled', false);
                    $("#students").selectpicker('refresh');
                },
                
                // Erro na requisição
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar estudantes: ' + error);
                    alert('Ocorreu um erro ao carregar os estudantes. Por favor, tente novamente.');
                }
            });
        });
    </script>
@endsection