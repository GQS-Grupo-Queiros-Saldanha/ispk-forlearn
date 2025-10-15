<div class="modal fade" id="CreateCursoMudarEquivalence" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel"></h3>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h3 id="TituloCreatechangeT"></h3>
                                <div class="alert-info p-2" id="AlertaModa">
                                    <label for="" id="alertMessage">Esta associação é referente as disciplinas
                                        que equivalem em cursos diferentes. </label>
                                </div>
                            </div>
                        </div>

                        <br>

                        {!! Form::open(['route' => 'courses_change.store', 'files' => true, 'id' => 'formChangeCourse']) !!}

                        <div class="row">
                            <div class="col-6">
                                <div class="">
                                    <label>Curso Inicial</label>
                                    <input type="text" readonly name="courseP" class="form-control" id="courseP">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label>Curso equivalente</label>
                                    <input type="text" readonly name="courseS" class="form-control" id="courseD">
                                </div>
                            </div>
                        </div>
                        <P>Disciplina(s)</P>
                        <div class="row mb-2">
                            <div class="col-6 pt-1">
                                <label>1#Histioria </label>
                                <select data-live-search="true" required name="id_curso_1"
                                    class="selectpicker form-control form-control-sm" id="id_curso_1">
                                    {{-- @foreach ($courses as $item)
                                        <option value="{{ $item->id }}"> {{ $item->currentTranslation['display_name'] }} </option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-6 pt-1">
                                <label>2#Histioria </label>
                                <select data-live-search="true" required name="id_curso_2"
                                    class="selectpicker form-control form-control-sm" id="id_curso_2">
                                    {{-- @foreach ($courses as $item)
                                        <option value="{{ $item->id }} "> {{ $item->currentTranslation['display_name'] }} </option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>

                        <p class="btn btn-primary" id="btn_add" type="button">
                            adicionar
                        </p>

                        <div id="list_equivalencia">

                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="{{ route('courses.disciplina.store') }}" class="btn btn-success" id="modal_create_save">Guardar</a>
                <button type="button" class="btn btn-primary" id="close_modal_create">Fechar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(() => {
        $("#formChangeCourse").click(() => {
            $.ajax(
                method: "POST",
                url: "{{ route('courses.disciplina.store') }}",
                data: {}
            ).done((response) => {
                $("#alertMessage").text("Deu tudo certo!");
            }).fail((err) => {
                $("#alertMessage").html("<span>Deu alguma coisa errada: " + err + "</span>");
            });
            console.log(response);
        });
    });
</script>
