<div class="modal fade" id="modalMudancaCurso" tabindex="-1" role="dialog" aria-labelledby="modalMudancaCursoLabel"
    aria-hidden="true">
    <form class="modal-dialog modal-lg"  action="{{route('mudanca.curso')}}" method="POST">
        @csrf
        <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px;">
            <div class="modal-header bg-info text-light">
                <h5 class="modal-title" id="modalMudancaCursoLabel">Aviso | Mudança de curso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">             
                <div class="">
                    @php 
                        $formatTagP = "text-align: justify; text-indent: 10px;"; 
                        $formatInput = "border: none; background: white;";
                    @endphp

                    <p style="{{$formatTagP}}">
                        Caro utilizador (<strong>{{Auth::user()->name}}</strong>), a forLEARN informa que o curso 
                        (<span id="msg_curso"></span>) no ano curricular (<span id="msg_ano_curricular"></span>)
                        do ano lectivo (<span id="msg_ano_lectivo"></span>), encontra-se bloqueado, para resolução 
                        desta situação deverás fazer mudança de curso ou desbloquear o ano curricular, abaixo é apresentado
                        alguns pontos se levar em consideração a mudança de curso:
                    </p>
                    <ul>
                        <li>
                            Neste processo de mudança de curso o número de matricula será alterado com base ao novo curso
                            escolhido.
                        </li>
                    </ul>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">Nome:</label>
                        <input type="text" name="m_nome" id="m_nome" readonly style="{{$formatInput}}" disabled/>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">Matricula:</label>
                        <input type="text" name="m_matricula" id="m_matricula" readonly style="{{$formatInput}}" disabled/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">Curso:</label>
                        <input type="text" name="m_curso" id="m_curso" readonly style="{{$formatInput}}" disabled/>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">Ano curricular:</label>
                        <input type="text" name="m_ano_curricular" id="m_ano_curricular" readonly style="{{$formatInput}}" disabled/>
                    </div>
                </div>
                <div class="form-group">
                    <label>Seleciona o curso que se pretende mudar:</label> 
                    <select name="id_course_mudanca" id="m_selector_curso" class="form-control form-control-sm" style="width: 100%!important;" required></select>
                </div>
                <div hidden>
                    <input type="hidden" name="id_user" id="m_id_user"/>
                    <input type="hidden" name="id_course" id="m_id_course"/>
                    <input type="hidden" name="course_year" id="m_course_year"/>                      
                    <input type="hidden" name="id_matricula" id="m_id_matricula"/>
                    <input type="hidden" name="num_matricula" id="m_num_matricula"/>
                    <input type="hidden" name="id_lective_year" id="m_id_lective_year"/>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">cancelar</button>
                <button type="submit" class="btn btn-success">continuação com a mudança</button>
            </div>
        </div>
    </form>
</div>
