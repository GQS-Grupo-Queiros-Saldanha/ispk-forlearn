@php $col = "col-md-6 p-1"; @endphp
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <form class="modal-dialog model-lg modal-dialog-centered" role="document" id="form-avaliacao-config" method="POST">
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
                <input type="text" class="form-control" id="lective_year_desc" readonly disabled>
                <input type="hidden" class="form-control" name="lective_year" id="lective_year_param"/>
            </div>
            <div class="col-md-6 p-2">
                <label for="strategy" class="form-label">Estratégia</label>
                <select class="form-control" name="strategy" id="strategy" required>
                    @isset($strategies)
                        @foreach($strategies as $value => $text)
                            <option value="{{$value}}">{{$text}}</option>
                        @endforeach
                    @endisset
                </select>
            </div>            
            <div class="col-md-6 p-2">
                <label for="exame_nota" class="form-label">Nota para dispensar no exame</label>
                <input type="number" class="form-control" id="exame_nota" name="exame_nota" max="20" min="0" value="10" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="exame_nota_inicial" class="form-label">Nota mínima para ir a exame</label>
                <input type="number" class="form-control" id="exame_nota_inicial" name="exame_nota_inicial" max="20" min="0" value="7" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="exame_nota_final" class="form-label">Nota máxima para ir a exame</label>
                <input type="number" class="form-control" id="exame_nota_final" name="exame_nota_final" max="20" min="0" value="13" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="mac_nota_recurso" class="form-label">Nota máxima para ir a recurso</label>
                <input type="number" class="form-control" id="mac_nota_recurso" name="mac_nota_recurso" max="20" min="0" value="7" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="mac_nota_dispensa" class="form-label">Nota para dispensar em MAC</label>
                <input type="number" class="form-control" id="mac_nota_dispensa" name="mac_nota_dispensa" max="20" min="0" value="14" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="percentagem_mac" class="form-label">Percentagem da mac</label>
                <input type="number" class="form-control" id="percentagem_mac" name="percentagem_mac" max="100" min="0" value="40" required>
            </div>
            <div class="col-md-6 p-2">
                <label for="percentagem_oral" class="form-label">Percentagem do exame</label>
                <input type="number" class="form-control" id="percentagem_oral" name="percentagem_oral" max="100" min="0" value="40" required>
            </div>
            <!-- <div class="col-md-6 p-2">
                <label for="exame_oral_final" class="form-label">Nota de oral máxima</label>
                <input type="number" class="form-control" id="exame_oral_final" name="exame_oral_final" max="20" min="0" value="14" required>
            </div> -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
            @icon('fas fa-trash-alt')
            <span>Cancelar</span>
        </button>
        <button type="submit" class="btn btn-success btn-sm" id="btn-save">
            @icon('fas fa-save')
            <span>Salvar</span>            
        </button>
      </div>
    </div>
  </form>
</div>