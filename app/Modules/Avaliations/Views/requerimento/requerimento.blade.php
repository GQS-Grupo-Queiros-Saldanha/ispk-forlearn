<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<style>
    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }

    .modal-request .modal-content {
        border-radius: 10px !important;
        width: 1000px;
        margin: 0 auto;
    }

    .modal-button {
        border-radius: 5px;
    }

    .modal-request .modal-header {
        padding-left: 36px;
    }

    .modal-request .modal-title {
        padding: 10px 36px 10px 20px;
        border-left: 6px solid #e5842e;
        font-weight: 700;
    }

    .modal-request .modal-body textarea {

        font-size: 14px;
        width: 96%;
        margin-left: 2% !important;
        padding: 10px 10px 10px 10px;
    }

    .modal-request span {
        color: black;
    }
</style>




<!-- Modal Marcação de Exame -->


<div class="modal fade bd-example-modal-lg modal-request" id="modal-requerimento" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <br>
                <div class="row" style="width: 100%;">

                    <div class="col-10">
                        <h4 class="modal-title" id="exampleModalLabel">REQUERIMENTO > AVALIAÇÃO</h4>
                    </div>

                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('schedule_exam.store_requerimento') }}" method="post">
                @csrf

                @method('POST')


                <div class="modal-body">
                    <div class="col-12">

                        <div class="row">

                            <div style="width: 100%">
                                <div class="col-12">

                                    <div class="row">
                                        <div class="col-6 d-none">
                                            <input type="number" id="student" name="students">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6 ">
                                            <div class="form-group col ">
                                                <label for="exam">Tipo de avaliação</label>
                                                <select class="selectpicker form-control form-control-sm" name="exam"
                                                    id="exam" data-actions-box="true" data-live-search="true">
                                                    <option></option>
                                                    <option value="1">Exame de recurso</option>
                                                    <option value="2">Exame especial</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-6 lista-disciplina">
                                            <div class="form-group col ">
                                                <label for="disciplines">Disciplina</label>
                                                <select class="selectpicker form-control form-control-sm"
                                                    name="disciplines[]" id="disciplines" data-actions-box="true"
                                                    data-live-search="true" multiple>

                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-12">
                                            <div id="group">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success modal-button" id="btn-requeride" disabled
                        style="display: none;">@icon('fas fa-plus-circle')
                        Requerer</button>
                    <button type="button" class="btn btn-secondary modal-button" data-dismiss="modal">@icon('fas fa-plus-close')
                        Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>


@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');


        var tipo_avaliacao = $("#exam");
        var disciplinas = $("#disciplines");
        var botao_requerer = $("#btn-requeride");
        var student = $("#student");
        var lista = $(".lista-disciplina");
        lista.hide()




        // Verificar o estado de mensalidade do estudante

        function mensalidade() {


            $.ajax({
                url: "/avaliations/requerimento_ajax",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',


            }).done(function(data) {

                student.val(parseInt((data["proprinas"].codigo)));


                if (data["proprinas"].estado == "total") {

                    botao_requerer.show();
                } else {

                }

            });

        }

        mensalidade();

        // Quando o tipo de avaliação for alterada

        tipo_avaliacao.change(function() {



            var exam = $(this).val();

            $("#group").empty();
            $.ajax({
                url: "/avaliations/get_exam_info/" + exam + "/" + +student.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',

                success: function(result) {
                    bodyData = '';
                    if (result == 501) {
                        lista.hide();
                        botao_requerer.prop('disabled', true);
                        bodyData =
                            "<label>O Sistema não foi detetou nenhum recurso para este(a) estudante no presente ano lectivo, em caso de dúvida, verifique o percurso acadêmico do mesmo.</label>"
                    } else if (result == 502) {
                        lista.hide();
                        botao_requerer.prop('disabled', true);
                        bodyData =
                            "<label>Não foi encontrada nenhuma matrícula no ano lectivo corrente deste aluno, verifica se o mesmo encontra-se matriculado no presente ano, só então poderá marcar um exame de recurso.</label>"
                    } else if (result == 505) {
                        lista.hide();
                        botao_requerer.prop('disabled', true);
                        bodyData =
                            "<label>Aviso ! a marcação de <b>EXAME ESPECIAL</b> encontra-se indisponível, porfavor contacte o apoio a <b>forLEARN</b> para liberar esta funcionalidade.</label>"
                    } else {
                        disciplinas.empty();
                        $.each(result, function(index, item) {
                            // console.log(item.discipline_code)

                            disciplinas.append('<option value="' + item.discipline_id + '">' +
                                item.discipline_code + '-' + item.discipline_name +
                                '</option>');
                        });

                        disciplinas.selectpicker("refresh");
                        botao_requerer.prop('disabled', false);
                        lista.show()
                    }

                    $("#group").append(bodyData);
                },
                error: function(dataResult) {
                    alert('Erro na busca das disciplina em exame de recurso, tente novamente: código do erro ' +
                        result);
                }

            });
        });

        
    </script>
@endsection
