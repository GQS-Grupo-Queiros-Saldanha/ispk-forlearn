<div class="form-group col-12">
    <div class="row">
        <div class="form-group col-2">
            <label>Disciplina de Trabalho de Fim de Curso</label>
        <!-- </div> -->
        @if (in_array($action, ['create', 'edit'], true))
            @php
            $checked = '';
            if(isset($discipline))
                $checked = $discipline->tfc == 1 ? 'checked' : '';
            @endphp
            <!-- <div class="form-group col-0"> -->

                <input style='margin-left:-200px;background-color:red' type="checkbox" name="tfc" {{ $checked }}>
                @else

                @if (isset($discipline))
               {{ $discipline->tfc == 1 ? 'Sim' : 'Não' }}
                
                @endif
                @endif
            
            </div>


    </div>

</div>
