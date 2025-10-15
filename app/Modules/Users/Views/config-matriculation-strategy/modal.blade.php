@php $col = "col-md-6 p-1"; @endphp
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <form class="modal-dialog model-lg modal-dialog-centered" role="document" id="form-matriculation-config" method="POST">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 p-2">
                        @csrf
                        @method('POST')
                        <label for="strategy" class="form-label">Ano lectivo</label>
                        <select name="LectiveYear" id="LectiveYear" class="form-control">
                           
                            @foreach ($lectiveYears as $item)
                                <option value="{{ $item->id }}"> {{ $item->currentTranslation->display_name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6 p-2">
                        <label for="exame_nota" class="form-label">Disciplinas em atraso 1º ano</label>
                        <input type="number" class="delay form-control" id="discipline_delay_first"
                            name="discipline_delay_first" max="5" min="0" value="0" required>
                    </div>
                    <div class="col-md-6 p-2">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" class="precedency" value="1"
                                id="precedency_first" name="precedency_first">
                            <label class="form-check-label" for="precedency_first">
                                N/Precedência
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 p-2">
                        <label for="exame_nota" class="form-label">Disciplinas em atraso 2º ano</label>
                        <input type="number" class="delay form-control" id="discipline_delay_second"
                            name="discipline_delay_second" max="5" min="0" value="0" required>
                    </div>
                    <div class="col-md-6 p-2">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" class="precedency" value="1"
                                id="precedency_second" name="precedency_second">
                            <label class="form-check-label" for="precedency_second">
                                N/Precedência
                            </label>
                        </div>
                    </div>


                    <div class="col-md-6 p-2">
                        <label for="exame_nota" class="form-label">Disciplinas em atraso 3º ano</label>
                        <input type="number" class="delay form-control" id="discipline_delay_thirth"
                            name="discipline_delay_thirth" max="5" min="0" value="0" required>
                    </div>
                    <div class="col-md-6 p-2">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" class="precedency" value="1"
                                id="precedency_thirth" name="precedency_thirth">
                            <label class="form-check-label" for="precedency_thirth">
                                N/Precedência
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 p-2">
                        <label for="exame_nota" class="form-label">Disciplinas em atraso 4º ano</label>
                        <input type="number" class="delay form-control" id="discipline_delay_fourth"
                            name="discipline_delay_fourth" max="5" min="0" value="0" required>
                    </div>
                    <div class="col-md-6 p-2">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" class="precedency" value="1"
                                id="precedency_fourth" name="precedency_fourth">
                            <label class="form-check-label" for="precedency_fourth">
                                N/Precedência
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 p-2">
                        <label for="exame_nota" class="form-label">Disciplinas em atraso 5º ano</label>
                        <input type="number" class="delay form-control" id="discipline_delay_fifth"
                            name="discipline_delay_fifth" max="5" min="0" value="0" required>
                    </div>
                    <div class="col-md-6 p-2">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" class="precedency" value="1"
                                id="precedency_fifth "name="precedency_fifth">
                            <label class="form-check-label" for="precedency_fifth">
                                N/Precedência
                            </label>
                        </div>
                    </div>



                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                    @icon('fas fa-trash-alt')
                    <span>Cancelar</span>
                </button>
                
                <button type="button" class="btn btn-success btn-sm" id="btn-save">
                    @icon('fas fa-save')
                    <span>Salvar</span>
                </button>
            </div>
        </div>
    </form>


    <div id="listStrategy">

        Ola mundo
    </div>
</div>


<script>
    // var qrcode = new QRCode(document.getElementById("qrcode"), {
    // text: "https://inspunyl.forlearn.ao/pt/users/configuracao-de-matricula",
    // width: 100,
    // height: 100,
    // colorDark: "#000000",
    // });
    // $("#form-matriculation-config").submit();

    //=================================================
    //numSelected

    //=================================================
</script>

@section('scripts-new')
    @parent
    <script>
        $(document).ready(() => {
            idLectiveY = $("#LectiveYear").val();

            GetStrategyAprove(idLectiveY);

            $("#LectiveYear").change(function() {
                var selectedValue = $(this).val();
                GetStrategyAprove(selectedValue);

            });
        });


        function GetStrategyAprove(Anolectivo) {

            $.ajax({
                type: "GET",
                url: "{{ route('get.numSelected') }}",
                data: {
                    value: Anolectivo
                },
                dataType: "json",
                success: function(response) {
                    if (response.data.length > 0) {
                        
                        $("#discipline_delay_first").val(response.data[0]
                            .discipline_in_delay);
                        $("#precedency_first").prop("checked", (response.data[0]
                            .precedence == true) ? true : false);

                        $("#discipline_delay_second").val(response.data[1]
                            .discipline_in_delay);
                        $("#precedency_second").prop("checked", (response.data[1]
                            .precedence == true) ? true : false);

                        $("#discipline_delay_thirth").val(response.data[2]
                            .discipline_in_delay);
                        $("#precedency_thirth").prop("checked", (response.data[2]
                            .precedence == true) ? true : false);

                        $("#discipline_delay_fourth").val(response.data[3]
                            .discipline_in_delay);
                        $("#precedency_fourth").prop("checked", (response.data[3]
                            .precedence == true) ? true : false);

                            $("#discipline_delay_fifth").val(response.data[4]
                            .discipline_in_delay);
                        $("#precedency_fifth").prop("checked", (response.data[4]
                            .precedence == true) ? true : false);

                    } else {
                        $(".delay").val(0);
                        $("#precedency_first").prop("checked", false);
                        $("#precedency_second").prop("checked", false);
                        $("#precedency_thirth").prop("checked", false);
                        $("#precedency_fourth").prop("checked", false);
                        $("#precedency_fifth").prop("checked", false);

                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error " + error);
                }
            });
           

        }
    </script>
@endsection
