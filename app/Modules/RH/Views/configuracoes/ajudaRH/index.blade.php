@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

<style>

    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }

    /*zoomIn.css  - 2*/
        .zoomit-ghost {
            top: 0;
            left: 0;
            z-index: 10;
            width: 100%;
            height: 100%;
            cursor: wait;
            display: block;
            position: absolute;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }

        .zoomit-zoomed {
            top: 0;
            left: 0;
            opacity: 0;
            z-index: 5;
            position: absolute;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            min-width: 100% !important;
            min-height: 100% !important;
            transition: transform 0.1s ease, opacity 0.2s ease;
        }
        
        .zoomit-container {
            overflow: hidden;
            position: relative;
            vertical-align: top;
            display: inline-block;
        }

        .zoomit-container img {
            vertical-align: top;
        }

        .zoomit-container.loaded .zoomit-ghost {
            cursor: crosshair;
        }

        .zoomit-container.loaded .zoomit-zoomed {
            opacity: 1;
        }
    /* fim zoomIn.css */

</style>

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>


<!-- Modal  que apresenta a opção de eliminar -->
<div class="modal fade" id="delete_subsidio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar este?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <form id="formRoute_delete-subsidio" method="POST" action="">
            @csrf
              <input type="hidden" name="getId" id="getId">
            <button type="submit" class="btn btn-primary">Ok</button>
          </form>
        </div>
      </div>
    </div>
</div>

<!-- Modal para editar o subsidio  -->
<div class="modal fade" id="editar_subsidio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Editar Subsídio</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">
                    <form id="formRoute-Edita-subsidio" method="POST" action="" class="pb-4">
                        @csrf
                        <div id="editarSubsidio">
                            <div class="form-group col-md-12">
                                <label for="inputEmail4">Nome</label>
                                <input required type="text" class="form-control" name="display_name" id="display_name" placeholder="Digite o nome da função">
                                <input  type="hidden" class="form-control" name="idSubsidio" id="idSubsidio" placeholder="">
                            </div>
                            
                            <div class="form-group col-md-12">
                                <label for="inputAddress">Descrição</label>
                                <input required type="text" class="form-control" name="descricao" id="descricao" placeholder="Descrição">
                            </div>

                            {{-- <button type="submit" class="btn btn-success">Gravar</button> --}}
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
      </div>
    </div>
</div>



<div class="content-panel">
    @include('RH::index_menu')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>CONFIGURAÇÕES RH</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>

    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col-md-10"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Ajuda</h5>
                        <div class="row col-md-12">                            
                        
                            <div class="nav flex-column nav-pills col-md-3 border-right pr-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link getQuetionHelp" id="v-pills-rrh-tab" data-toggle="pill" href="#v-pills-rrh" role="tab" aria-controls="v-pills-rrh" aria-selected="true">Como criar relatório de recurso humanos ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_staff-tab" data-toggle="pill" href="#v-pills-criar_staff" role="tab" aria-controls="v-pills-criar_staff" aria-selected="true">Como criar  um funcionário staff ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_contrato-tab" data-toggle="pill" href="#v-pills-criar_contrato" role="tab" aria-controls="v-pills-criar_contrato" aria-selected="true">Como criar um contrato ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-rescisao_contrato-tab" data-toggle="pill" href="#v-pills-rescisao_contrato" role="tab" aria-controls="v-pills-rescisao_contrato" aria-selected="true">Como criar rescisão de contrato ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-folha_pagamento_funcionario-tab" data-toggle="pill" href="#v-pills-folha_pagamento_funcionario" role="tab" aria-controls="v-pills-folha_pagamento_funcionario" aria-selected="true">Como vinsializar a folha de pagamento funcionário ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-processar_salario_singular-tab" data-toggle="pill" href="#v-pills-processar_salario_singular" role="tab" aria-controls="v-pills-processar_salario_singular" aria-selected="true">Como fazer o processamento do salário ?</a>
                                {{-- <a class="nav-link getQuetionHelp" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Processar salário geral</a> --}}
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_presenca_laboral-tab" data-toggle="pill" href="#v-pills-criar_presenca_laboral" role="tab" aria-controls="v-pills-criar_presenca_laboral" aria-selected="true">Como criar presenças laborais ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-atribuir_subsidios-tab" data-toggle="pill" href="#v-pills-atribuir_subsidios" role="tab" aria-controls="v-pills-atribuir_subsidios" aria-selected="true">Como atribuir subsídios ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_funcao-tab" data-toggle="pill" href="#v-pills-criar_funcao" role="tab" aria-controls="v-pills-criar_funcao" aria-selected="true">Como criar função ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-cargos-tab" data-toggle="pill" href="#v-pills-cargos" role="tab" aria-controls="v-pills-cargos" aria-selected="true">Como criar cargos ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-departmentos-tab" data-toggle="pill" href="#v-pills-departmentos" role="tab" aria-controls="v-pills-departmentos" aria-selected="true">Como criar departmentos ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-eventos-tab" data-toggle="pill" href="#v-pills-eventos" role="tab" aria-controls="v-pills-eventos" aria-selected="true">Como criar eventos ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-permissoes-tab" data-toggle="pill" href="#v-pills-permissoes" role="tab" aria-controls="v-pills-permissoes" aria-selected="true">Como criar permissões ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_imposto-tab" data-toggle="pill" href="#v-pills-criar_imposto" role="tab" aria-controls="v-pills-criar_imposto" aria-selected="true">Como criar imposto ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_subsidios-tab" data-toggle="pill" href="#v-pills-criar_subsidios" role="tab" aria-controls="v-pills-criar_subsidios" aria-selected="true">Como ciar subsídios ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-criar_horario_laboral-tab" data-toggle="pill" href="#v-pills-criar_horario_laboral" role="tab" aria-controls="v-pills-criar_horario_laboral" aria-selected="true">Como criar horário laboral ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-situacoes_profissionais-tab" data-toggle="pill" href="#v-pills-situacoes_profissionais" role="tab" aria-controls="v-pills-situacoes_profissionais" aria-selected="true">Como criar situações profissionais ?</a>
                                                                
                                {{-- <a class="nav-link getQuetionHelp" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Como funciona os imposto ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Como criar contrato ?</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Messages</a>
                                <a class="nav-link getQuetionHelp" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</a> --}}
                            </div>

                            <div class="ml-0 pl-0 col-md-9 " id="v-pills-tabContent">
                                
                                <div hidden class="pl-2 tab-pane fade " id="v-pills-rrh" role="tabpanel" aria-labelledby="v-pills-rrh-tab">                                    
                                    <h4><b>Sobre os relatórios de recursos humanos</b></h4>
                                    <p>
                                        A tela relatório de recursos humanos permiti ao utilizador gerar relatório, de um determinado grupo de funcionários 
                                        que estão associados a um determinado cargo...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/relatorio_de_recurso_humanos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/relatorio_de_recurso_humanos.png" alt="zoom in pic" >                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_staff" role="tabpanel" aria-labelledby="v-pills-criar_staff-tab">                                    
                                    <h4><b>Sobre criar staff</b></h4>
                                    <p>
                                        A tela <b>criar staff</b> permiti ao utilizador fazer o cadatramento de novos funcionários na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_staff.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_staff.png" alt="zoom in pic">                                        
                                    </p>
                                    <p>
                                        A tela <b>criar staff</b> permiti ao utilizador fazer o cadatramento de novos funcionários na plataforma...
                                        <br><br><b>1º -</b> Clicando no botão <i>criar novo staff</i>, uma nova janela será exibida e o utilizador poderá ensirir 
                                        os dados no funcionários;
                                        <br><br><b>2º - </b>Após inserir todos os dados, “<b><i>nome, primeiro e último nome, e-mail, número de bilhete de identidade, 
                                        cargo</i></b>”, o botão de criar staff é apresentado e o utilizador poderá assim cadastrar o novo funcionário;

                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_contrato" role="tabpanel" aria-labelledby="v-pills-criar_contrato-tab">                                    
                                    <h4><b>Sobre criar contrato de trabalho</b></h4>
                                    <p>
                                        A tela <b>criar contrato de trabalho</b> permiti ao utilizador fazer o cadatramento de novos contrtos de trabalho de um 
                                        determinado funcionário na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic2" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_contrato.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_contrato.png" alt="zoom in pic">                                        
                                    </p>
                                    <p>
                                        <br><b><i>Passos para efectuar o cadastrato de contratos:</i></b>
                                        <br><b>1º - </b>Utilizador têm de escolher o funcionário anteriormente cadastrado, clicando na opcção funcionário é possivel procurar e selecionar o funcionário pretendido;
                                        <br><b>2º - </b>Apóis escolher o funcionário é possivel escolher o cargo em que na qual deseja-mos efetuar a contratação do funcionário;
                                        <br><b>3º - </b>Em seguida escolhemos o tipo de contrato desejado, e informamos a data de contratação e a data de termino do contrato;
                                        <br><b>4º - </b>Clicamos no botão <b>gravar</b> e o contrato de trabalho será criado.
                                    </p>

                                    <p>
                                        <br><b><i>Passos para adcionar função:</i></b>
                                        <br>A tela <b>adicionar função</b> permiti ao utilizador fazer o cadatramento de novas funções de trabalho do 
                                        funcionário na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic2_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/add_funcao.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/add_funcao.png" alt="zoom in pic">                                        
                                    </p>
                                    <p>
                                        <br><b>1º - </b>Clicamos no botão <b><i>Add função</i></b>, um modal é apresentado para a criação da nova função do funcionário;
                                        <br><b>2º - </b>Deve-se selecionar a função desejada e definir a data de início e de termino do contrato da função;
                                        <br><b>3º - </b>É possivel adcionar uma nota, descritiva;
                                        <br><b>4º - </b>Clicamos no botão gravar e a nova função será assoiciada ao funcionário.
                                    </p>

                                    <p>
                                        <br><b><i>Passos para adcionar slário:</i></b>
                                        <br>Clicando sobre o botão <b><i>edição do salário</i></b> que fica por baixo do botão gravar, no painel do funcionário canto direito, 
                                        nela é possivel definir ou fazer alteração do salário do funcionário.                                        
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic2_2" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/contrato_salario.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/contrato_salario.png" alt="zoom in pic">                                        
                                    </p>
                                    <p>
                                        <br><b>1º - </b>A direita do formulário principal é apresentada um novo formulário onde nela é possivel escolher o cargo que 
                                        pretendense definir o salário;
                                        <br><b>2º - </b>Caso ocargo selecionado já possuir um salário ele ira aparecer na descrição <b><i>salário actual</i></b>, na 
                                        nova remuneração é possivel definir o novo salário do funcionário;
                                        <br><b>3º - </b>Póis estes passos devemos escolher as <b><i>horas laborias</b></i> e a <b><i>o mês e o ano</b></i>, em que esse salário ira se efetivar;
                                        <br><b>4º - </b>Depois de concluir com o preenchimento dos campos, clicamos no botão gravar e o novo salário é atribuido ao funcionário.
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-rescisao_contrato" role="tabpanel" aria-labelledby="v-pills-rescisao_contrato-tab">                                    
                                    <h4><b>Sobre criar rescisões de contrato</b></h4>
                                    <p>
                                        A tela <b>rescisões de contrato de trabalho</b> permiti ao utilizador efetuar rescisões de contratos de trabalho de um 
                                        determinado funcionário na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic_3" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/rescisao_do_contrato.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/contrato_salario.png" alt="zoom in pic">                                        
                                    </p>
                                    <p>
                                        <br><b><i>Passos para efectuar a rescisões de contratos:</i></b>                                         
                                        <br><br><b>1º -</b> Selecionamos o funcionário e depois escolhemos o cargo em que na qual pretendesse fazer a rescisão  do contrato; 
                                        <br><br><b>2º - </b>Após preencher os campos, podemos submeter o documento de rescisões de contrato, em seguida clicamos no botão <b><i>realizar rescisão do contrato de trabalho</i></b>.
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-folha_pagamento_funcionario" role="tabpanel" aria-labelledby="v-pills-folha_pagamento_funcionario-tab">                                    
                                    <h4><b>Sobre a folha de pagamento de funcionário</b></h4>
                                    <p>
                                        A tela <b>folha de pagamento de funcionários</b> permiti ao utilizador visualizar os recibos de pagamentos salarias, efetuados a um determinado funcionário...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic4" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/folha_de_pagamento_funcionario.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/folha_de_pagamento_funcionario.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para visualizar os recibos de pagamentos:</i></b>                                         
                                        <br><br><b>1º -</b> Selecionamos o funcionário e depois escolhemos um ou vários recibos de salários processados; 
                                        <br><br><b>2º - </b>Em seguida clicamos no botão <b><i>gerar PDF</i></b>, então o recibo ou recibos podem ser visualizados.
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-processar_salario_singular" role="tabpanel" aria-labelledby="v-pills-processar_salario_singular-tab">                                    
                                    <h4><b>Sobre processar salário singular</b></h4>
                                    <p>
                                        A tela <b>processamento de salário singular</b> permiti ao utilizador processar o salário de um ou vários funcionários referente a um dado mês e cargo...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic5" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/processar_salario_singular.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/processar_salario_singular.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para processar pagamentos:</i></b>                                         
                                        <br><br><b>1º -</b> Selecionamos um ou vários funcionários e depois escolhemos um ou vários cargos associados ao funcionário; 
                                        <br><br><b>2º - </b>Em seguida informamos o mês e ano referente ao pagamento salárial, também é possivel deixar uma nota informátivo;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Submeter</i></b>, então o salário é processado.

                                        <br><b><i>Passos para visualizar pagamentos:</i></b>                                         
                                        <br><br><b>1º -</b> No canto superior direito, podesse visualizar botão <b><i>G</i></b>, clicando sobre ele uma nova janela é apresentada; 
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic5_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/processar_salario_geral.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/processar_salario_geral.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º - </b>Em seguida informamos os funcionários, o cargo e a referencia do recibo;
                                        <br><br><b>2º - </b>A seguir clicamos no botão <b><i>Gerar PDF</i></b>, então o recibo podera ser visualizado.                            
                                        <br><br><br><b>OBS:</b>Nesta janela também é possivel fazer uma filtragem de recibos, por funcionário ou cargo...                            
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_presenca_laboral" role="tabpanel" aria-labelledby="v-pills-processar_salario_singular-tab">                                    
                                    <h4><b>Sobre criar presença laboral</b></h4>
                                    <p>
                                        A tela <b>criar presença laboral</b> permiti ao utilizador criar presenças, mantendo assim o controle de assiduidade dos funcionários...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic6" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_presenca_laboral.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_presenca_laboral.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar presença:</i></b>                                         
                                        <br><br><b>1º -</b> Selecionamos o funcionário e depois escolhemos o contrato em que na qual desejamos fazer a marcação da presença; 
                                        <br><br><b>2º - </b>Em seguida informamos os demais dados, como data da presença, honra de entrada e hora de saída;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então a presença é criada.
                                        <br><br><b>OBS: </b>No canto inferior podemos visualizar as presenças do funcionário selecionado, nela podesse editar a presença, no caso de se ter errado na inserção de algum dado ou mesmo apagar o registro de presença.                           
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-atribuir_subsidios" role="tabpanel" aria-labelledby="v-pills-atribuir_subsidios-tab">                                    
                                    <h4><b>Sobre atribuir subsídios</b></h4>
                                    <p>
                                        A tela <b>atribuir subsídios</b> permiti ao utilizador efectuar atribuição de subsídios ao funcionário em um determinado cargo...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic7" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/atribuir_subsidios.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/atribuir_subsidios.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para atribuir subsídios:</i></b>                                         
                                        <br><br><b>1º -</b> Selecionamos o funcionário e depois escolhemos o cargo em que na qual deseja-mos fazer atribuição de subsídios; 
                                        <br><br><b>2º - </b>Em seguida informamos os subsídios que deseja-mos atribuir ao cargo do funcionário, e o valor correspondente do subsídio;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o subsídio é associado ao crgo do funcionário.
                                        <br><br><b>OBS: </b>No canto inferior podemos visualizar o funcionário e os seus subsídios.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic7_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/subsidio_editar.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/subsidio_editar.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Lista de funcionários clicamos no botão <b><i>S</i></b>, uma janela é apresentada contendo todos os subsídios do funcionário correspondente a um determinado cargo; 
                                        <br><br><b>2º - </b>Para eliminar um determinado subsídio, clicamos no subsídio e uma outra janela é apresentada, a perguntar se deseja-mos eliminar o subsídio.                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_funcao" role="tabpanel" aria-labelledby="v-pills-criar_funcao-tab">                                    
                                    <h4><b>Sobre criar função</b></h4>
                                    <p>
                                        A tela <b>criar função</b> permiti ao utilizador criar funções de trabalho na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic8" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_funcao.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_funcao.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar funções:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de função, é possível visualizar todas as funções previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic8_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_funcao_1.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_funcao_1.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos o nome da função e uma descrição; 
                                        <br><br><b>2º - </b>Em seguida clicamos em <b><i>criar</i></b> e a função será então criada.                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-cargos" role="tabpanel" aria-labelledby="v-pills-cargos-tab">                                    
                                    <h4><b>Sobre criar cargos</b></h4>
                                    <p>
                                        A tela <b>criar cargos</b> permiti ao utilizador criar cargos de trabalho na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic9" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/cargos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/cargos.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar cargos:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de cargos, é possível visualizar todas os cargos previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic9_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_cargos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_cargos.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos o nome do cargo; 
                                        <br><br><b>2º -</b> Em línguas informamos o nome e descrição; 
                                        <br><br><b>3º - </b>Em seguida clicamos em <b><i>criar</i></b> e a cargo será então criada.                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-departmentos" role="tabpanel" aria-labelledby="v-pills-departmentos-tab">                                    
                                    <h4><b>Sobre criar departmento</b></h4>
                                    <p>
                                        A tela <b>criar departmento</b> permiti ao utilizador criar novos departmentos de trabalho na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic10" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/departamentos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/departamentos.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar departmentos:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de departmento, é possível visualizar todas os departmentos previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic10_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_departamento.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_departamento.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos o nome do departmento; 
                                        <br><br><b>2º -</b> Em línguas informamos o nome, descrição e a abreviação; 
                                        <br><br><b>3º - </b>Em seguida clicamos em <b><i>criar</i></b> e a departamento será então criada.                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-eventos" role="tabpanel" aria-labelledby="v-pills-eventos-tab">                                    
                                    <h4><b>Sobre criar eventos</b></h4>
                                    <p>
                                        A tela <b>criar evento</b> permiti ao utilizador criar novos eventos que irão de correr em um determinado período, dia, mês ou ano...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic11" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/eventos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/eventos.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar eventos:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de departmento, é possível visualizar todas os eventos previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic11_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_eventos.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_eventos.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos a duração do evento, data de início, data de termino e uma <b><i>url</i></b> caso o evento tenha uma referência na internet;                                         
                                        <br><br><b>2º - </b>Em seguida definimos as opções tais como <b><i>cor de fundo, cor da borda, cor do textor</i></b>;                                        
                                        <br><br><b>3º -</b> Depois informamos o nome e a descrição; 
                                        <br><br><b>4º - </b>Clicndo em <b><i>criar</i></b> o evento será então criada.

                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-permissoes" role="tabpanel" aria-labelledby="v-pills-permissoes-tab">                                    
                                    <h4><b>Sobre criar permissões</b></h4>
                                    <p>
                                        A tela <b>criar permissão</b> permiti ao utilizador criar novas permissões de utilizadores na plataforma...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic12" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/permissoes.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/permissoes.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar permissões:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de permissão, é possível visualizar todas os permissões previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic12_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_permissao.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_permissao.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos o nome do permissão; 
                                        <br><br><b>2º -</b> Em línguas informamos o nome e descrição; 
                                        <br><br><b>3º - </b>Em seguida clicamos em <b><i>criar</i></b> e a permissão será então criada.                                        
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_imposto" role="tabpanel" aria-labelledby="v-pills-criar_imposto-tab">                                    
                                    <h4><b>Sobre criar imposto</b></h4>
                                    <p>
                                        A tela <b>criar imposto</b> permiti ao utilizador criar impostos...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic13" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_imposto.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_imposto.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar imposto:</i></b>                                         
                                        <br><br><b>1º -</b> Informamos o nome do importo, como exemplo podemos simplemente inserir a sigla; 
                                        <br><br><b>2º - </b>Em seguida informamos a descrição;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o imposto é criado.                                     
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_imposto" role="tabpanel" aria-labelledby="v-pills-criar_imposto-tab">                                    
                                    <h4><b>Sobre criar imposto</b></h4>
                                    <p>
                                        A tela <b>criar imposto</b> permiti ao utilizador criar impostos...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic13" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_imposto.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_imposto.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar imposto:</i></b>                                         
                                        <br><br><b>1º -</b> Informamos o nome do importo, como exemplo podemos simplemente inserir a sigla; 
                                        <br><br><b>2º - </b>Em seguida informamos a descrição;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o imposto é criado.                                     
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_subsidios" role="tabpanel" aria-labelledby="v-pills-criar_subsidios-tab">                                    
                                    <h4><b>Sobre criar imposto</b></h4>
                                    <p>
                                        A tela <b>criar imposto</b> permiti ao utilizador criar impostos...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic14" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_subsidios.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_subsidios.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar subsídios:</i></b>                                         
                                        <br><br><b>1º -</b> Informamos o nome do subsídios, como exemplo podemos simplemente inserir a sigla; 
                                        <br><br><b>2º - </b>Em seguida informamos a descrição;
                                        <br><br><b>3º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o subsídios é criado.                                     
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-criar_horario_laboral" role="tabpanel" aria-labelledby="v-pills-criar_horario_laboral-tab">                                    
                                    <h4><b>Sobre criar horário laboral</b></h4>
                                    <p>
                                        A tela <b>criar horário laboral</b> permiti ao utilizador criar horários de trabalho...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic15" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_horario_laboral.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_horario_laboral.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar subsídios:</i></b>                                         
                                        <br><br><b>1º -</b> Informamos o total de dias de trabalho por mês; 
                                        <br><br><b>2º - </b>Em seguida informamos a hora de entrada e saída do primeiro período de trabalho;
                                        <br><br><b>3º - </b>Depois informamos a hora de entrada e saída do segundo período de trabalho;
                                        <br><br><b>4º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o horário de trabalho é criado é criado.
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade " id="v-pills-situacoes_profissionais" role="tabpanel" aria-labelledby="v-pills-situacoes_profissionais-tab">                                    
                                    <h4><b>Sobre criar situações profissionais</b></h4>
                                    <p>
                                        A tela <b>criar situações profissionais</b> permiti ao utilizador criar situações profissionais de trabalho...
                                    </p>
                                    <p class="mb-1 mt-1 text-center test">
                                        <img id="pic16" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/situacoes_profissionais.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/situacoes_profissionais.png" alt="zoom in pic">
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar situações profissionais:</i></b>                                         
                                        <br><br><b>1º -</b> Informamos o total de dias de trabalho por mês; 
                                        <br><br><b>2º - </b>Em seguida informamos a hora de entrada e saída do primeiro período de trabalho;
                                        <br><br><b>3º - </b>Depois informamos a hora de entrada e saída do segundo período de trabalho;
                                        <br><br><b>4º - </b>Após este processo clicamos no botão <b><i>Gravar</i></b>, então o horário de trabalho é criado é criado.
                                    </p>
                                    <p>
                                        <br><b><i>Passos para criar situações profissionais:</i></b>                                         
                                        <br><br><b>1º -</b> Na tela de situação profissional, é possível visualizar todas as situações profissionais previamente cadastradas; 
                                        <br><br><b>2º - </b>Clicando no botão <b><i>novo</i></b>.
                                        <p class="mb-1 mt-1 text-center test">
                                            <img id="pic16_1" style="width: 70%;"src="https://forlearn.ao/storage/attachment/rh_img/criar_situacao_profisional.png" data-zoomed="https://forlearn.ao/storage/attachment/rh_img/criar_situacao_profisional.png" alt="zoom in pic">
                                        </p>
                                        <br><br><b>1º -</b> Informamos o cógio da situação profissional; 
                                        <br><br><b>2º -</b> Em línguas informamos o nome e descrição; 
                                        <br><br><b>3º - </b>Em seguida clicamos em <b><i>criar</i></b> e a situação profissional será então criada.                                        
                                    </p>
                                </div>







                                <div hidden class="pl-2 tab-pane fade " id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">                                    
                                    <h4><b>Sobre os impostos.</b></h4>
                                    <p>
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                    </p>
                                    <center class="mb-1 mt-1 zoom-img">
                                        <img style="width: 70%;"src="https://forlearn.ao/storage/attachment/img_contratos.png" alt="" >
                                    </center>
                                    <p>
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <h4><b>Sobre o subsídios</b></h4>
                                    <p>
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                    </p>
                                    <center class="mb-1 mt-1 zoom-img">
                                        <img id="pic" style="width: 70%;"src="https://forlearn.ao/storage/attachment/img_contratos.png" data-zoomed="https://forlearn.ao/storage/attachment/img_contratos.png" alt="zoom in pic" >
                                    </center>
                                    <p>
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                    <h4><b>Criação de contrato</b></h4>
                                    <p>
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                    </p>
                                    <center class="mb-1 mt-1 zoom-img">
                                        <img style="width: 70%;"src="https://forlearn.ao/storage/attachment/img_contratos.png" alt="" >
                                    </center>
                                    <p>
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                    </p>
                                </div>

                                <div hidden class="pl-2 tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                    <h4><b>Processamento de Salário</b></h4>
                                    <p>
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                        Informática é uma área de informação digital,
                                    </p>
                                    <center class="mb-1 mt-1 zoom-img">
                                        <img style="width: 70%;"src="https://forlearn.ao/storage/attachment/img_contratos.png" alt="" >
                                    </center>
                                    <p>
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                        Informática é uma área de informação digital, Informática é uma área de informação digital, Informática é uma área de informação digital,
                                    </p>                                    
                                </div> 

                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>


@endsection
@section('scripts')
@parent

<script>

    $(".getQuetionHelp").click(function (e) { 
        var getDev=$(this).attr('href');
        $(".tab-pane").attr('hidden',true)
        $(getDev).attr('hidden',false)
    });   
    
    

    // JS ZOOM IMAGE - 2
        //zoomIn.js
        (function(factory) {
            if (typeof define === "function" && define.amd) {
            define(["jquery"], factory);
            } else {
            factory(jQuery);
            }
        })

        (function($) {
            $.fn.zoomIt = function(options) {
                // Default parameters
                var defaults = {
                    enabled: 1,
                    status: 0,
                    loaded: 0,
                    img: $(this),
                    offset: [0, 0],
                    class: {
                    container: "zoomit-container",
                    loaded: "loaded",
                    img: "zoomit-zoomed",
                    ghost: "zoomit-ghost"
                    },
                    // Get image src
                    src: function() {
                    return this.img.data("zoomed");
                    },
                    // Get zoom image src
                    getSrc: function() {
                    return typeof this.src == "function" ? this.src() : this.src;
                    },
                    // Image HTML
                    imgTag: null
                };

                // Merge options
                options = $.extend(defaults, options);

                // Execute Callback
                options.execute = function(e) {
                    if (typeof e === "string" && typeof options[e] === "function") {
                        options[e](options);
                    }
                };

                // Get container
                options.getContainer = function() {
                    return $('<div class="' + options.class.container + '"></div>');
                };

                // Get zoom image src
                options.getImgSrc = function() {
                    if (options.imgTag === null) {
                        options.imgTag = $("<img />")
                            .addClass(options.class.img)
                            .attr("src", this.getSrc());

                        // Alt Tag
                        if (typeof options.img.attr("alt") !== "undefined") {
                            options.imgTag.attr("alt", options.img.attr("alt"));
                        }
                    }

                    return options.imgTag;
                };

                // Get zoomed image instance
                options.getZoomInstance = function() {
                    return options.img.parent().find("." + options.class.img);
                };

                // Restrict a numerical value between 0 and 1
                options.restrict = function(val) {
                    if (val > 1) {
                    val = 1;
                    } else if (val < 0) {
                    val = 0;
                    }

                    return val;
                };

                // Get image dimensions
                options.getDimensions = function() {
                    // Set position
                    options.position = {
                    img: {
                        width: options.img.width(),
                        height: options.img.height(),
                        offset: options.img.offset()
                    },
                    zoom: {
                        width: options.getZoomInstance().width(),
                        height: options.getZoomInstance().height()
                    }
                };
                };

                // Position zoomed image element
                options.setPosition = function(event) {
                    // iOS Original Event (Pointer Position)
                    if (typeof event.originalEvent !== "undefined") {
                    event = event.originalEvent;
                    }

                    // Get image dimensions
                    if (options.loaded === 0) {
                    options.getDimensions();
                    }

                    // Add loaded class
                    options.img.parent().addClass(options.class.loaded);
                    options.loaded = 1;

                    // Percentages
                    options.position.x = options.restrict(
                        (event.pageX - options.position.img.offset.left) /
                            options.position.img.width
                    );

                    options.position.y = options.restrict(
                        (event.pageY - options.position.img.offset.top) /
                            options.position.img.height
                    );

                    // Offsets
                    options.position.zoom.offset = {
                        left:
                            (options.position.zoom.width - options.position.img.width) *
                            options.position.x,
                        top:
                            (options.position.zoom.height - options.position.img.height) *
                            options.position.y
                    };

                    options.getZoomInstance().css({
                        transform:
                            "translate(-" +
                            options.position.zoom.offset.left +
                            "px, -" +
                            options.position.zoom.offset.top +
                            "px)"
                        });
                };

                // Show zoom
                options.show = function(event) {
                // Return early if image is loading
                if (!options.enabled || (options.status === 1 && options.loaded === 0)) {
                    return;
                }

                // Set zoom status
                options.status = 1;

                // Append image
                if (options.img.parent().find("." + options.class.img).length == 0) {
                    options.img.after(options.getImgSrc());

                    // Image loaded callback
                    options
                    .getZoomInstance()
                    .on("load", function() {
                        options.setPosition(event);
                    })
                    .each(function() {
                        if (this.complete) options.setPosition(event);
                    });
                } else {
                    options.setPosition(event);
                }

                // onZoomIn
                options.execute("onZoomIn");
                };

                // Hide zoom
                options.hide = function() {
                    options.status = 0;
                    options.loaded = 0;
                    options.imgTag = null;
                    options.img.parent().removeClass(options.class.loaded);
                    setTimeout(function() {
                    options.getZoomInstance().remove();
                    }, 500);
                    options.getZoomInstance().remove();

                    // onZoomOut
                    options.execute("onZoomOut");
                };

                // Move zoom
                options.move = function(event) {
                if (options.status) {
                    options.show(event);
                }
                };

                // Enable
                options.enable = function() {
                    options.enabled = 1;
                };

                // Disable
                options.disable = function() {
                options.enabled = 0;
                };

                // Initialize
                options.init = function() {
                options.img
                    .wrap(options.getContainer())
                    .after('<div class="' + options.class.ghost + '"></div>');

                // Ghost
                options.ghost = options.img.parent().find("." + options.class.ghost);

                // Mouse events
                options.ghost
                    .on("mouseenter touchstart", function(event) {
                        options.show(event);
                    })
                    .on("mouseleave touchend", function() {
                        options.hide();
                    })
                    .on("mousemove touchmove", function(event) {
                        event.stopPropagation();
                        event.preventDefault();
                        options.move(event);
                    })
                    .on("click", function() {
                        options.execute("onClick");
                    });

                // onInit
                options.execute("onInit");
                };

                // Bind zoom data
                options.img.data("zoom", options);
                options.init();
            };
        });

        //apply
        $("#pic").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic2").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic2_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic2_2").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic3").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic4").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic5").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic5_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic6").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic7").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic7_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic8").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic8_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic9").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic9_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic10").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic10_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic11").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic11_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic12").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic12_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic13").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic14").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic15").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic16").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
        $("#pic16_1").zoomIt({
            onClick:function(){
                alert("good luck");
            }
        });
    // FIM JS ZOOM IMAGE - 2  

</script>

@endsection