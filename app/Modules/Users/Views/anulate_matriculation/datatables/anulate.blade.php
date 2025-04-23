<div class="modal fade" id="anulate_matricula" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        {{-- <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div> --}}
        {{-- <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Anulação de matrícula</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> --}}

        <div class="modal-header bg-danger text-light">
            <h5 class="modal-title" id="exampleModalLabel">ALERTA | Anulação de matrícula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 "> 
                    
                    <p class="text-danger" style="font-weight:bold; !important"> Caro utilizador ({{auth()->user()->name}}), a acção de <label id="acaoID" class="text-danger" style="font-weight:bold; !important"></label> anulação de matrícula é de sua inteira responsabilidade, antes de prosseguir com esta ação confirma as informações abaixo:</p>

                    <p style="padding:5px; !important" id="idTExto">                        
                        Ano lectivo: <h4 id="ano_lectivo"></h4>
                        Nº confirmação: <h4 id="n_mat"></h4>
                        Nome completo: <h4 id="nome"></h4>
                        <span id="turma-vw">
                            Turma: <h4 id="turmas"></h4>
                        </span>

                        {{-- Deseje proseguir com a anulação de matrícula do(a) estudante <h6 id="nome"></h6> com o número de matricula <h5 id="n_mat"></h5>
                        , estudante da turma <h4 id="turmas"></h4> e "ano lectivo". --}}
                    </p>                    

                    <form id="processar_anaulacao" method="POST" enctype="multipart/form-data" accept-charset="UTF-8" action="{{route('anulate.matriculation.store')}}" class="pb-4">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6" hidden>
                                <label for="inputPassword4">Nome:</label>
                                <input readonly type="hidden" class="form-control" name="nome_completo" id="nome_completo" >
                            </div>                        

                            <div class="form-group col-md-2" hidden>
                                <label for="inputPassword4">Nº confirmação</label>
                                <input readonly type="hidden" class="form-control" name="n_confirmacao" id="n_confirmacao" >
                            </div> 

                            <div class="form-group col-md-4" hidden>
                                <label for="inputPassword4">Turma</label>
                                <input readonly type="hidden" class="form-control" name="turma" id="turma" >
                            </div>

                            <div class="form-group col-md-4" hidden>
                                <label for="inputPassword4">ID matrícula</label>
                                <input readonly type="hidden" class="form-control" name="matricula_id" id="matricula_id" >
                            </div>

                            <div class="form-group col-md-12"  >
                                <label for="inputPassword4">Observação</label>
                                
                                <textarea name="anulate_observetion" id="boxObservation" required cols="30" rows="7" class="form-control boxObservation" title="Escreva o motivo da anulação de matrícula aqui!">

                                    
                                </textarea>
                            </div>
                        </div>

                        {{-- <div id="editarhorasLaboral">
                                    <button type="submit" class="btn btn-success">Anular</button>
                             </div> 
                        --}}

                        <div class="modal-footer" >
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>
                          
                            <nav id="ocultar_btn">
                               <button type="submit" class="btn btn-success">Continuar com anulação</button>
                               <button type="submit" value="adm" name="admin_anulate"  style="color:white; background-color:#328cff;" class="btn btn-info"  style="color:white; background-color:#328cff;" title="Esta anulação não o estudante não é sujeito a pagar um emolumento 'Anulação de matrícula'
                                ">Anulação administrativa</button>
                            </nav>
                        </div>

                    </form>
                    

                </div>                
            </div>
        </div>
       
      </div>
    </div>
</div>