<div class="modal fade" id="anulate_matricula" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
 
        <div class="modal-header bg-danger text-light">
            <h5 class="modal-title" id="exampleModalLabel">ALERTA | Anulação de transferência por equivalência</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 "> 
                    
                    <p class="text-danger" style="font-weight:bold; !important"> Caro utilizador ({{auth()->user()->name}}), deseja realmente eliminar este pedido de transferência por equivalência do estudante:</p>

                    <p style="padding:5px; !important" id="idTExto">  
                        Nome completo: <h4 id="nome"></h4>                      
                    </p>                    

                    <form id="processar_anaulacao" method="POST" enctype="multipart/form-data" accept-charset="UTF-8" action="{{route('anulate.equivalence.store')}}" class="pb-4">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6" hidden>
                                <label for="inputPassword4">Nome:</label>
                                <input readonly type="hidden" id="id_transf" name="id_equivalencia">
                                <input readonly type="hidden" class="form-control" name="nome_completo" id="nome_completo" >
                            </div>                        

                            <div class="form-group col-md-12"  >

                            </div>
                        </div>

                        <div class="modal-footer" >
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>
                          
                            <nav id="ocultar_btn">
                               <button type="submit" class="btn btn-success">Continuar com anulação</button>
                            </nav>
                        </div>

                    </form>
                    

                </div>                
            </div>
        </div>
       
      </div>
    </div>
</div>