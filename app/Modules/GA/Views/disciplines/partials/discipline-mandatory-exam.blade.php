<div class="form-group col-2">
    <label>Exame obrigatório</label>
    @if(in_array($action, ['create','edit'], true))
        <select name="mandatory_exam" class="form-control">
            <option value="0">Não</option>
            <option value="1">Sim</option>
        </select>

    @else
        @if ($hasMandatoryExam->count() > 0)
            @if ($hasMandatoryExam->first()->has_mandatory_exam == 0)
                Não
            @else
                Sim
            @endif
        @else
            N/A
        @endif
    @endif
</div>
