<div class="form-group col-12">
    <div class="row">
        <div class="form-group col-2">
            <label>Transição obrigatória</label>
        <!-- </div> -->
        @if (in_array($action, ['create', 'edit'], true))
            @php
            $checked = '';
            if(isset($discipline))
                $checked = isset($discipline->mandatory_discipline) ? 'checked' : '';
            @endphp
            <!-- <div class="form-group col-0"> -->

                <input style='margin-left:-200px;background-color:red' type="checkbox" name="mandatory_discipline" {{ $checked }}>
                @else

                @if (isset($discipline))
               {{ $discipline->mandatory_discipline !== null ? 'Sim' : 'Não' }}
                
                @endif
                @endif
            
            </div>


    </div>

</div>
