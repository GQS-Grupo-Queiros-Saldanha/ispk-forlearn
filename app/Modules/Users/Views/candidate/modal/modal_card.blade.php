<div class="modal fade" id="modal-card" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Opções de impressão</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
 
          <div class="modal-body" style="height:150px;">
              
                  <a href="{{ route('cards.student',$user->id) }},1" class="btn btn-primary text-white" target="_blank"><i class="fas fa-file-pdf"></i> Anual</a>
                  <a href="{{ route('cards.student',$user->id) }},2" class="btn btn-success text-white" target="_blank"><i class="fas fa-file-pdf"></i> Geral</a>
                
          </div>
          
      </form>
    </div>
  </div>
</div>