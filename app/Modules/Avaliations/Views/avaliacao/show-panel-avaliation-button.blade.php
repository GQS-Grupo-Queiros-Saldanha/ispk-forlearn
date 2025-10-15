<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

{{-- ESTILOS DOS MENUS BOTÕES MULTIPLAS ESCOLHAS --}}
<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .circulo {
        border-radius: 0.5rem !important;
    }

    button.element, a.element{
        height: 30px !important;
    }

</style>


<div  class="card p-0 mb-3">
        <div class="d-flex gap-1 align-items-center">
            {{-- MOSTRA AS OPÇÕES DO MENU BOTÃO LANÇAR NOTAS --}}
            <div class="dropdown p-3">
                <button class="circulo btn btn-secondary dropdown-toggle rounded element" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 45px">              
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/grades/teacher"> Exame de acesso</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/grades/student"> Nota do candidato</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/old_student"> Transação de notas</a> 
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/avaliacao_aluno/create"> Atribuir notas</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/other_avaliations"> Atribuir OA</a>
                    {{-- <a class="dropdown-item fa-solid fa-arrow-right" style="padding-bottom: 5%" target="_blank" href="/avaliations/old_student_final_grade"> Atribuir Notas TFC</a> --}}
                
                </div>
            </div>

            {{-- MOSTRA AS OPÇÕES DO MENU BOTÃO PAUTA --}}
            <div class="dropdown p-3">
                <button class="circulo btn btn-secondary dropdown-toggle rounded element" type="radio" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 45px">              
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/show_final_grades"> Exibir nota única</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/grade"> Exibir notas do estudantes</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/show_summary_grades"> Exibir sumário de notas</a> 
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/avaliacao_aluno/show"> Exibir avaliações</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_grades_mac/10"> Exibir pauta de frequência</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_grades_st"> Exibir pauta final</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_exame_grades/20"> Exibir pauta exame</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_recurso_grades/0"> Exibir pauta recurso</a>
                    <hr>                   
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_grades_mac/15"> Publicar pauta de frequência</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_grades_coordenador"> Publicar pauta final</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_exame_grades/25"> Publicar pauta exame</a>
                    <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/discipline_recurso_grades/1"> Publicar pauta recurso </a>
                </div>
            </div>

            {{-- MOSTRA AS OPÇÕES DO MENU BOTÃO CONFIGURAÇÕES --}}
            @if(auth()->user()->hasAnyRole(['superadmin']))
                <div class="dropdown p-3">
                    <button class="circulo btn btn-secondary dropdown-toggle rounded element" type="radio" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 45px">              
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">                
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/tipo_avaliacao"> Tipos de avaliações</a>
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/tipo_metrica"> Tipos de métrica</a>
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/avaliacao"> Avaliação</a>
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/school-exam-calendar"> Calendário de provas</a>
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/plano_estudo_avaliacao"> Plano de estudos e avaliação</a>
                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank" href="/avaliations/pauta_student_config"> Limite de pagamento de propina</a>                
                    </div>
                </div>
            @endif  
            
            {{-- BOTÕES --}}
            @if(!auth()->user()->hasAnyRole(['teacher']))                         
                {{-- BOTÃO Percurso Académico --}}
                <div class="p-3">
                    <a data-toggle="tooltip" data-placement="bottom" title="Percurso Académico" target="_blank"  class="p-2 pr-3 pl-3 btn btn-sm id_studentPercursoAcademico element" href="/avaliations/curricular_path" style="background-color: #ffa500; color:white; height: 40px;"><i class="fa-solid fa-p"></i></a>                                            
                </div>
                            
                {{-- BOTÃO Marcação de Exame --}}
                <div class="p-3">
                    <a data-toggle="tooltip" data-placement="bottom" title="Marcação de Exame" target="_blank"  class="p-2 pr-3 pl-3 btn btn-sm id_studentMarcacaoExame element" href="/avaliations/schedule_exam" style="background-color: #006eff; color:white; height: 40px;"><i class="fa-solid fa-circle-h"></i></a>                                            
                </div>
            @endif

            {{-- BOTÃO HOME --}}
            <div class="p-3" id="home_button">
                <a data-toggle="tooltip" data-placement="bottom" title="Voltar ao Início" target=""  class="p-2 pr-3 pl-3 btn btn-sm id_Home element" href="/avaliations/panel_avaliation" style="background-color: #81ee77; color:rgb(10, 10, 10); height: 40px;"><i class="fa-solid fa-house-chimney"></i></a>             
            </div>
            
            {{-- LISTA OS ESTUDANTES NA PAUTA --}}
            @if (isset($pauta))            
                <div class="p-3">
                    <!-- Rounded switch -->
                    <label class="switch">
                        <input type="checkbox" id="selector_pauta">
                        <span class="slider round"></span>
                    </label>
                </div>
            @endif
        </div>
</div> 



@section('scripts')
    @parent
    <script>
        
        var BTclickGrades = 0;
        function subMenuBotomGrades() {
            if (BTclickGrades == 0) {
                $("#submenugrades").attr('class',"")
                $("#submenugrades").attr('class',"fa fa-chevron-right")
                $("#subMenuGrades").attr('hidden',true)
                BTclickGrades = 1;
            }
            else{
                $("#submenugrades").attr('class',"")
                $("#submenugrades").attr('class',"fa fa-angle-down")
                $("#subMenuGrades").attr('hidden',false) 
                BTclickGrades = 0;        
            }
            
            // OUCULTA O MENU PAUTAS
            $("#submenupautas").attr('class',"")
            $("#submenupautas").attr('class',"fa fa-chevron-right")
            $("#subMenuPautas").attr('hidden',true)
            // OUCULTA O MENU CONFIGURAÇÕES
            // $("#submenuconfiguration").attr('class',"")
            // $("#submenuconfiguration").attr('class',"fa fa-chevron-right")
            // $("#submenuconfiguration").attr('hidden',true)
        }

        var BTclickPautas = 0;
        function subMenuBotomPautas() {  
            if (BTclickPautas == 0) {
                $("#submenupautas").attr('class',"")
                $("#submenupautas").attr('class',"fa fa-chevron-right")
                $("#subMenuPautas").attr('hidden',true)
                BTclickPautas = 1;
            }
            else{
                $("#submenupautas").attr('class',"")
                $("#submenupautas").attr('class',"fa fa-angle-down")
                $("#subMenuPautas").attr('hidden',false)
                BTclickPautas = 0;        
            }  
            
            // OUCULTA O MENU NOTAS
            $("#submenugrades").attr('class',"")
            $("#submenugrades").attr('class',"fa fa-chevron-right")
            $("#subMenuGrades").attr('hidden',true)
            // OUCULTA O MENU CONFIGURAÇÕES
            // $("#submenuconfiguration").attr('class',"")
            // $("#submenuconfiguration").attr('class',"fa fa-chevron-right")
            // $("#submenuconfiguration").attr('hidden',true)    
        }

        var BTclickConfig = 0;
        function subMenuBotomConfiguration() {  
            if (BTclickConfig == 0) {
                $("#submenuconfiguration").attr('class',"")
                $("#submenuconfiguration").attr('class',"fa fa-chevron-right")
                $("#submenuconfiguration").attr('hidden',true)
                BTclickConfig = 1;
            }
            else{                         
                $("#submenuconfiguration").attr('class',"")
                $("#submenuconfiguration").attr('class',"fa fa-angle-down")
                $("#subMenuConfiguration").attr('hidden',false) 
                BTclickConfig = 0;        
            }
            // OUCULTA O MENU NOTAS
            $("#submenugrades").attr('class',"")
            $("#submenugrades").attr('class',"fa fa-chevron-right")
            $("#subMenuGrades").attr('hidden',true)
            // OUCULTA O MENU PAUTAS
            $("#submenupautas").attr('class',"")
            $("#submenupautas").attr('class',"fa fa-chevron-right")
            $("#subMenuPautas").attr('hidden',true)
        }   

    </script>

@endsection
