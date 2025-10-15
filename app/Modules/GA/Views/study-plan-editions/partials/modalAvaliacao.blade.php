{{-- Modal para associacao de uma métrica a uma avalicao --}}
<div class="modal fade" id="insertMetrica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Associar Avaliações - </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
      

            {!! Form::open(['route' => ['study_plan_avaliacao']]) !!}
            <input type="hidden" id="disciplinaAvl" name="id_disciplina" >
            <input type="hidden" id="planoAvl" name="id_plano">
            <div class="form-group col">
                <table id="" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Ativo</th>
                            <th>Avaliação</th>
                            
                        </tr>
                    </thead>
                    <tbody id="bodyData">

                    </tbody>
                </table>
                <div style="display: none" id="exameObrigatorio">
                    <label style="font-size: 1.1pc"  name="exame_obriga" >Exame obrigatório <input value="" id="checagemExame" type="checkbox"  name="exame_obriga"></label>  
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="CancelarModal" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
    </div>
</div>
{!! Form::close() !!}
</div>

