

  <div class="modal fade" id="modalPagoCursoTodos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

      <form action="{{route('candidate.courses_default')}}" method="POST">
        @csrf

      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel">Escolha do curso:</h3> <br>
       
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                 <label>Esta escolha definirá o curso que aparecerá como padrão no momento de efectuar a matrícula do mesmo.</label>
                                <select name="course_default" id="course-default" class="form-control"></select>
                            </div>
                            <input type="hidden" name="id_user" id="id_user">
                        </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">    
          <button type="submit" class="btn btn-success" id="modal_create_save">Guardar</button>
          <button type="button" class="btn btn-primary" id="close_modal_create">Fechar</button>
        </div>

        

        </form>
      </div>
    </div>
  </div>

 {{-- <script>
    $('.btn-analisar').click(function (e){
        console.log("ola");
    });
 </script> --}}