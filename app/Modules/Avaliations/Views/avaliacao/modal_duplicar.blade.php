<form action="{{route('avaliation.duplicar') }}" method="post">
    @csrf
    <div class="modal fade" id="modal-copiar-avaliation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg rounded mt-5" role="document">
        <div class="modal-content rounded" style="background-color: #e9ecef">
            <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLongTitle">Informação</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <p class="lead">Caro utilizador/ {{auth()->user()->name}}  pretende copiar a configuração desta avaliação para o proximo ano lectivo.</p>
                <hr class="my-4">
                <p>Por favor seleciona o ano lectivo que pretende colar a copia das configurações da avaliação</p>
                <div class="col-6 mt-2 mb-4 pl-0" style="width:200px; !important">
                    <label> Ano lectivo </label>
                    <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
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
                    <input type="hidden" name="flag_avaliation" value="" id="id_input_avaliation" readonly>
                </div>
                <button style="border-radius: 6px; background: #20c7f9" type="submit" class="btn btn-lg text-white mt-2">Gravar</button>
            </div>
        </div>
        </div>
    </div>

        
</form>