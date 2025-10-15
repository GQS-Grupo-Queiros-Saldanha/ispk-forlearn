@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button{
            border: none; background: none; outline-style: none;transition: all 0.5s;
        }
        .list-group li button:hover{cursor: pointer;font-size: 15px;transition: all 0.5s; font-weight: bold }
        .subLink{
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }
        .subLink:hover{
            cursor: pointer;font-size: 15px;transition: all 0.5s; border-bottom: #dfdfdf 1px solid;
        }
        .fotoUserFunc{
           margin: 0px;
            padding: 0px;
            shape-outside: circle();
            clip-path: circle();
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #fff;
        }
        .perfil-user{
            display: flex;
            overflow-x: scroll;
            margin: 0;
            padding: 0;
            box-shadow: inset #cad3dba6 4px -4px 11px 1px;
        }
        .container-perfil{
            flex: 1 0 90%;
            margin: 0px 8px 8px 8px;
            width: 100%;
            border-right: #cad3db 1px dashed;
            border-radius: 1px;
            padding-right: 3px;
            box-shadow: #cad3dbad 7px 0px 10px 0px;
        }
        .perfil-user::-webkit-scrollbar {       
            height: 7px;  
            border-radius: 30px;           
        }

        .perfil-user::-webkit-scrollbar-track {
            background: #e0e0e0;   
            border-radius: 30px; 
            height: 2px
           
        }

        .perfil-user::-webkit-scrollbar-thumb {
            background-color: #565656;   
            border-radius: 30px;       
            border: none; 
            height: 2px
        }


        .modal-body span {
            font-size: 13px;
            color: black;
        }
  

        .wrapper-page{
            text-align: center;
            font-size: 10px;
        }
        header h1{
            font-size: 4em;
        }
        header h3{
            font-size: 2.5em; 
        }
        header span{
            color: #666;
            display: inline-block;
            padding: .5em;
            -webkit-transition: .15s ease-in;
            -moz-transition: .15s ease-in;
            -ms-transition: .15s ease-in;
        }
        header span:hover{
            cursor: pointer;
            -webkit-transform: scale(1.50,1.50); 
            -moz-transform: scale(1.05,1.05);
            -ms-transform: scale(1.05,1.05);
        }
        .plan{
            background: #ccc;
            border: .09em solid #999;
            width: 18em;
            position: relative;
            box-shadow: 0 0 1em .05em #666;
            display: inline-block;
            margin: 2em;
            font-size: 1.2em;
            -webkit-transition: .15s ease-in;
            -moz-transition: .15s ease-in;
            -ms-transition: .15s ease-in;
        }
        .plan:hover{
            -webkit-transform: scale(1.05,1.05) 
                                translateY(-.5em); 
            -moz-transform: scale(1.05,1.05)
                            translateY(-.5em);
            -ms-transform: scale(1.05,1.05)
                            translateY(-.5em);
            cursor: pointer;
        }
        .plan h1{
            background: #333;
            display: block;
            border-top-left-radius: .3em;
            border-top-right-radius: .3em;
            -moz-border-top-left-radius: .3em;
            -moz-border-top-right-radius: .3em;
            top: -.3em;
            position: relative;
            color: #dedede;
            text-align: center;
            line-height: 1.8em;
            font-size: 1.3em;
            font-weight: normal;
        }
        .plan h2{
            background: #29A6CF;
            line-height: 2em;
            font-size: 1.2em;
            font-weight: normal;
            /* text-align: center; */
            position: relative;
            width: 15.6em;
            left: -.35em;
            margin-top: -.3em;
            box-shadow: 0 0 .3em 1px #333;
            color: #dedede;
        }
        .plan h2:before{
            content: "";
            width: 0; 
                height: 0; 
                border-top: .35em solid #2081A1; 
                border-left: .35em solid transparent;
            position: absolute;
            bottom: -.35em;
            left: 0;
        }
        .plan h2:after{
            content: "";
            width: 0; 
                height: 0; 
                border-top: .35em solid #2081A1; 
                border-right: .35em solid transparent;
            position: absolute;
            bottom: -.35em;
            right: 0;
        }
        .plan h3{
            font-weight: normal;
            text-align: center;
            line-height: 2.5em;
        }
        .plan h4{
            font-size: 1.5em;
            font-weight: normal;
            text-align: center;
            color: #666;
        }
        .plan ul{
            width: 60%;
            padding: 1em 0;
            margin: auto;
            list-style-position: outside;
            font-size: 1em;
            text-align: left;
        }
        .plan li{
            margin-bottom: .3em; 
        }
        .perUnit{
            font-size: .5em;
        }
    </style>
    {{-- modal load carregar site --}}
    <div style="z-index: 1900" class="modal fade modal_loader" id="formProcessamentoSalario-load"  data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>

    {{-- modal procesamento de salário geral --}}
    {{-- modal procesamento de salário geral --}}
    <div   style="z-index: 999999" class="modal fade table-responsive col-md-12" id="processoSalario-geral" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"aria-hidden="true">
        <div style="max-width: 100%;" class="modal-dialog m-0">
            <div  class="modal-content rounded mt-1">
                <div style="background:#20c7f9;width: 100%;border-top-left-radius: 2px;border-top-right-radius: 2px;height: 6px;" class="m-0" ></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Gerar relátorio processamento de salário</h5>
                    <button type="button" class="close btn-choseModal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col mt-4 mb-4">
                        <div class="col mb-4" style=" display: flex;justify-content: center;align-items: center;">
                            <form method="POST" action="{{ route('recurso_humano.get-processoSalario') }}" class="pb-4 border-bottom col-10" target="_blank">
                                @csrf
                                <input type="hidden" class="form-control idFuncionario" value="" name="idFuncionario">
                                <div class="form-group">
                                    <label for="inputEmail4">Funcionario/os</label>
                                    <select  name="idFuncionario[]" autocomplete="" multiple="" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                        <option ></option>
                                        @foreach ($users as $element)
                                            <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class=" col-md-5">
                                        <label for="inputEmail4">Cargo</label>
                                        <select required  name="cargoModal[]" id="cargoModal" autocomplete="" multiple="" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                            
                                            {{-- @php $setRoles=[] @endphp
                                            @foreach ($users as $item)
                                                @foreach ($item->roles as $key=> $element)
                                                    @if (!in_array($element->id,$setRoles))
                                                        @php $setRoles[]=$element->id @endphp
                                                        <option value="{{$element->id}}">{{$element->name}}</option> 
                                                    @endif
                                                @endforeach
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class=" col-md-3">
                                        <label for="inputEmail4">Vencimente referente a</label>
                                        <select required  name="vencimentoMonth" autocomplete=""  class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                            {{-- <option ></option> --}}
                                            @foreach ($getProcessoSalario as $Year_mothy => $item)
                                                <option value="{{$Year_mothy}}">{{$Year_mothy}}</option>   
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class=" border-left col-md-4">
                                        <label for="inputEmail4">Bancos</label>
                                        <select   name="bancos[]" id="banco" autocomplete=""  class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                            <option ></option>
                                            @foreach ($bancos as $key => $item)
                                                <option value="{{$item->id}}">{{$item->display_name}}</option>   
                                            @endforeach
                                        </select>
                                        <small style="font-size: 0.9pc;background: #eef1f5;padding: 4px; border-radius: 3px;" id="passwordHelpBlock" class="form-text ">Caro utilizador para gerar um relatório de processamento de salário por banco(s), por favor selecione o respentivo banco.</small>
                                    </div>
                                </div>
                                <button style="background: #20c7f9" type="submit" class="btn text-white">GERAR PDF</button>
                            </form>
                        </div>
                    </div>
                    <div hidden class="col ml-0  pl-0">
                       <div style="background: #eff3f5" class="container jumbotron  pb-3  rounded">
                           <h1 class="ml-0 pl-0 funcao-name-funcionario">Ola,  {{Auth::user()->name}}</h1>
                            <h4 class="ml-3 mb-0 pb-0 pl-0 funcao-name-funcionario">Estatisca informativa</h1>
                            <p class="ml-3">Até a presente data @php $data= date("Y-m-d")@endphp <b> {{$data}} </b>, enncontram-se as seguintes informação no sistema: </p>
                           <hr class="mb-4 mt-2">
                               
                            <div class="col wrapper-page">
                                <div class="plan">
                                    <hgroup>
                                        <h1>Processamento do Salário</h1>
                                        <h2>Data processada salário<span class="perUnit">/month</span></h2>
                                        <h3>Access to 5 Widgets</h3>
                                        <h4>You break even...</h4>
                                        <ul>
                                            <li>These widgets rock!</li>
                                            <li>Includes free access to widget shop!</li>
                                            <li>Your friends will be so jealous!</li>
                                        </ul>
                                    </hgroup>
                                </div>
                                <div class="plan">
                                    <hgroup>
                                        <h1>Processamento em espera</h1>
                                        <h2>$25<span class="perUnit">/month</span></h2>
                                        <h3>Access to 5 Widgets</h3>
                                        <h4>You break even...</h4>
                                        <ul>
                                            <li>These widgets rock!</li>
                                            <li>Includes free access to widget shop!</li>
                                            <li>Your friends will be so jealous!</li>
                                        </ul>
                                    </hgroup>
                                </div>
                            </div>
                            
                          
                           {{-- informação sobre as funções a ser exercidas --}}
                           <div class="m-0 p-0" id="div-funcao">
                               
                           </div>
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
                      <h1>{{$action}}</h1> 
                    </div>
                    <div class="col-sm-6">
                      
                    </div>
                </div>
            </div>
        </div>
            
        <p class="btn-menu col-md-0 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                @include('RH::index_menuSalario')
                <div style="background-color: #f8f9fa" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div  class="criarCodigo ">
                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                            <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-2 "></div>
                           
                           
                            <div class="col-md-12 align-items-end border-bottom mb-1 pb-2">
                                <div class="float-right  d-flex flex-row-reverse bd-highlight">
                                    <div class="p-0 mt-0 bd-highlight ">
                                        <button data-toggle="modal" data-type="processoSalario-geral" data-target="#processoSalario-geral" type="button" style="background: #2b9fc2;font-size: 1.1pc;border-radius: 6px;" class="btn m-0 btn-lg text-white">
                                            <i class="fa-solid fa-file-invoice"></i> Gerar relátorio(s) processamento(s) de salário
                                        </button>
                                    </div>
                                    <div style="text-align: left; display: flex; align-items: center;" class="pr-3 pl-3 pt-0 mt-0 bd-highlight ">
                                        <small class="text-muted">Caro utilizador para poder gerar relátorios, por favor click aqui <i class="fa-solid fa-right-long"></i></small>
                                    </div>
                                </div>
                            </div>

                            @if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))    
                                <div class="col-md-12 row mb-4 mt-2 pr-0">
                                    <div class="mr-0 pr-0 col-md-8 border-bottom">
                                        {{--formularios--}}
                                        <form  method="POST" action="{{ route('recurso_humano.create-processoSalario') }}" class="pb-4">
                                            @csrf
                                            <div style="background: #e9ecef85" class="bd-highlight mb-4"><h1 style="font-size: 1.3pc" class="text-muted text-uppercase">Processar salário</h1></div>
                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputEmail4">Funcionario/os</label>
                                                    <select required name="funcionario[]" data-live-search="true" autocomplete="" multiple=""   class="selectpicker form-control"  id="funcionario-contrato" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                                        <option  ></option>
                                                        @php $getUser=[]; @endphp
                                                        @foreach ($users as $element)
                                                            @foreach ($getcontratos as $item)
                                                                @if ($element->id==$item->id_user && !in_array($element->id,$getUser)) 
                                                                @php $getUser[]=$item->id_user @endphp
                                                                    <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option>    
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="m-0 p-0" for="inputPassword4">Contratado/a com o cargo</label>
                                                    <select required name="roles[]" data-live-search="true" autocomplete="" multiple=""   class="selectpicker form-control"  id="roles" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                                        
                                                    </select>
                                                </div>    
                                            </div>
                                            <div class="form-row">

                                                <div class="form-group col-6 mb-3 div-salarioBase">
                                                    <label class="m-0" for="inputEmail4">Salário base</label>
                                                    <div class="input-group mt-0">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
                                                    </div>
                                                    <input readonly="" style="color: #6c6c6c;font-weight: bold;background:#f8f9fa" type="numeric" class="form-control" name=salarioBase-fun" id="salarioBase-fun" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="form-group col-6 mb-3 ml-0 pl-0 div-valorHora-tempo">
                                                    <label class="m-0" for="inputEmail4">Valor do contrato(por tempo ou hora)</label>
                                                    <div class="input-group mt-0">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
                                                    </div>
                                                    <input readonly style="color: #6c6c6c;font-weight: bold;background:#f8f9fa" type="numeric" class="form-control" name="valorTempo-hora" id="valorTempo-hora" placeholder="">
                                                    </div>
                                                </div>


                                                <div class="form-group col-6 mb-3 div-salarioBase">
                                                    <label for="inputPassword4">Refrente a</label>
                                                    <div class="input-group mt-0">                                               
                                                        <input required type="month" name="refrencia" class="form-control" id="refrencia">
                                                    </div>
                                                </div>
                                                <div class="form-group col-6 mb-3 ml-0 pl-0 div-valorHora-tempo">
                                                    <label class="m-0" for="inputEmail4">Reembolso</label>
                                                    <div class="input-group mt-0">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
                                                    </div>
                                                    <input style="color: #6c6c6c;font-weight: bold;background:#f8f9fa" type="numeric" class="form-control" name="valorReembolso" id="valorReembolso" placeholder="">
                                                    </div>
                                                </div>

                                                {{--
                                                <div  class="form-group col-md-12">
                                                    <label for="inputPassword4">Refrente a</label>
                                                    <input required type="month" name="refrencia" class="form-control" id="refrencia">
                                                </div>
                                                --}}
                                            
                                                {{-- <div class="form-group col-md-12">
                                                    <label for="inputPassword4">Dias falta</label>
                                                    <input min="0"  type="number" class="form-control" name="dataFinal"  id="dataFinal" >
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="inputPassword4">Valor falta</label>
                                                    <input min="0"  type="number" class="form-control" name="Valorfalta"  id="Valorfalta" >
                                                </div> --}}

                                                <div class="form-group col-md-12">
                                                    <label for="exampleFormControlTextarea1">Nota</label>
                                                    <textarea  class="form-control" name="nota" id="exampleFormControlTextarea1" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <!-- Modal  alerta processamento de salário-->
                                            <div class="modal fade" id="alertaProcessamento" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-lg rounded mt-5" role="document">
                                                <div class="modal-content rounded" style="background-color: #002d3a;">
                                                    <div class="modal-header">
                                                    <h3 class="modal-title" id="exampleModalLongTitle" style="color:#ededed">Informação</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span style="color:#ededed" aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p style="color:#ededed" class="lead">&nbsp;Caro utilizador/a @php $user=Auth::user()->name @endphp 
                                                        <strong style="font-weight: bold">{{$user}}</strong>     
                                                        pretende formalizar o processamento do salário do(os) funcionário(os)?</p>
                                                        <hr style="background: #035268" class="my-4">
                                                        <p style="color:#adadad">&nbsp; Após a formalização deste processo podera ter acesso ao(aos) recibo de vencimento do funcionário(os) .</p>
                                                        
                                                        <button style="border-radius: 6px; background: #20c7f9" type="submit" class="btn btn-lg text-white mt-2 btn-submeter">Submeter</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>




                                            <div  class="form-row ml-0 mt-1 pl-0">
                                                <div class="form-group mr-3">
                                                    <button  hidden data-toggle="modal" data-target="#alertaProcessamento"  type="button"  style="background: #2b9fc2"  class="btn text-white btn-Processar"><i class="fas fa-receipt"></i> Processar salário</button>
                                                </div>                            
                                            </div>
                                        </form> 
                                    </div>
                                    <div hidden class="col-md-4 pr-3 infor-user">
                                        <div class="card m-0 p-0 col card-primary card-outline rounded " >
                                            <div style="background: white" class="card-body m-0 p-0  box-profile rounded perfil-user">
                                                
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endif
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

    // variaves
        var getUsers= @json($users);
        var getUsers_Role=JSON.parse(JSON.stringify(getUsers))
        
        var getfuncoesFuncionario=@json($getfuncoesFuncionario); 
        var getfuncoes_Funcionario=JSON.parse(JSON.stringify(getfuncoesFuncionario));
        
        var getcontratos=@json($getcontratos);
        var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));

        var getSalariofuncionario=@json($getSalariofuncionario);
        var getSalario_fun=JSON.parse(JSON.stringify(getSalariofuncionario));
        var getIdUser=null;
        var getVetorCargo=[];
        
        var salarioBaseFun = $("#salarioBase-fun");
        var roles=$("#roles")
        var valorTempoHora=$("#valorTempo-hora")
        var cargoModal=$("#cargoModal")
        var perfilUser=$(".perfil-user")
        var refrencia=$("#refrencia")
    // variaves

    getRolesSelect(getUsers_Role)
    // console.log(getcontratos_user)
    // console.log(getUsers_Role)
    // console.log(getSalario_fun)
    // console.log(getfuncoes_Funcionario)   
    
        $(".btn-submeter").click(function (e) {
            if(refrencia.val()!=null){ 
                $('#alertaProcessamento').modal('hide')
               $("#formProcessamentoSalario-load").modal('show')
                // setTimeout(() => {
                //     location.reload(true);
                // }, 1900); 
            }
            
        });
        
      $("#funcionario-contrato").change(function () { 
        getIdUser= $("#funcionario-contrato").val()
        var getbtn_submeter=false;
        getVetorCargo=[]
        salarioBaseFun.val(null)
        valorTempoHora.val(null)

        $(".name-user").text(); 
        var contrato=false; 
        if(getIdUser.length==0) {
            $(".infor-user").slideUp(900);
            $(".fotoUserFunc").attr('style','')
            $('.div-valorHora-tempo').attr('hidden',false)
            $('.div-salarioBase').attr('hidden',false)
            roles.empty();
            roles.append('<option ></option>')
            roles.selectpicker('refresh');
        } else{
            perfilUser.empty();
            if(getIdUser.length>1) {
                $('.div-valorHora-tempo').attr('hidden',true)
                $('.div-salarioBase').attr('hidden',true)
            }
            $(".infor-user").attr('hidden',false)
            $(".infor-user").slideUp(0);
            $(".infor-user").fadeIn(1380);
          
            $.each(getcontratos_user, function (key, item) { 
                    if (item.id_user==getIdUser) {
                    contrato=true
                    }
            });
            if (contrato==true) {
                $(".user-contrato").text()  
                $(".user-contrato").text("activo")
            } else {
                $(".user-contrato").text()  
                $(".user-contrato").text("n/a")
            }
            roles.empty();
            roles.append('<option ></option>')
            var div_container='';
            $.each(getIdUser, function (keyUser, setId) { 
                if (setId!="") {
                   
                    $.each(getUsers_Role, function (index, item) { 
                        if (item.id==setId) { 
                            let routePerfil_user=("{{ route('users.show','id_fun') }}").replace('id_fun', setId); 
                            div_container+='<div class="container-perfil">'+
                                    '<div class="text-center mt-3">'+
                                        '<center>'+
                                            ' <div style="background-image: url(//{{$_SERVER['HTTP_HOST']}}/users/avatar/'+item.fotografia+')" class="fotoUserFunc mt-1 mb-2" ></div>'+
                                        '</center>'+
                                    '</div>'+
                                    '<h3 class="profile-username text-center name-user">'+item.name+'</h3>'+
                                    '<p class="text-muted text-center">Estado do contrato: <strong class="user-contrato"></strong></p>'+
                                    '<ul class="m-0 p-0 mb-1 list-group-unbordered  rounded">'+
                                        '<li class="list-group-item"><b>Contactos</b> <a class="float-right">'+item.telefone+' / '+item.whatApp+'</a></li>'+
                                        '<li class="list-group-item"><b>B.I nº:</b> <a class="float-right">'+item.bi_num+'</a></li>'+
                                    '</ul>'+
                                    '<div class="accordion mb-4" id="accordionExample">'+
                                        '<div class="card rounded">'+
                                            '<div style="background: white" class="card-header " id=heading'+setId+'">'+
                                                '<h2 class="mb-0">'+
                                                    '<button class=" pl-0 btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse'+setId+'" aria-expanded="true" aria-controls="collapse'+setId+'">Funções do funcionário'+
                                                    '</button>'+
                                                '</h2>'+
                                            '</div>'+
                                            '<div style="background: #e1e1e1"  id="collapse'+setId+'" class="collapse " aria-labelledby="heading'+setId+'"  data-parent="#accordionExample">'
                                    div_container+='<div class="card-body">';

                                         $.each(getfuncoes_Funcionario, function (i, valueElement) {
                                             if (valueElement.id_user == setId) {
                                                i+=1;
                                                div_container+='<div class="d-flex w-100 justify-content-between">'+
                                                        '<h6 class="mb-2"><small class="p-2" style="color:white;background-color: #20c7f9ba;shape-outside: circle();clip-path: circle();">'+i+'</small> '+valueElement.display_name+'</h5>'+
                                                        '<small class="text-muted">'+valueElement.data_inicio_contrato_at_funcao +' - '+ valueElement.data_fim_contrato_at_funcao+'</small>'
                                                div_container+='</div>';
                                             }
                                         });
                                        
                                                    // '<p class="mb-1">Some placeholder content in a paragraph.</p>'+
                                                    // '<small class="text-muted">And some muted small print.</small>'+
                                                    // 'Some placeholder content for the first accordion panel. This panel is shown by default, thanks to the <code>.show</code> class.'+
                                    div_container+='</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<a target="_blank" style="background: #2b9fc2" href="'+routePerfil_user+'" class="btn  btn-block text-white rounded"><b><i class="fa fa-eye"></i></b></a>' 
                                    div_container+= '</div>';


                                    
                           
                            $.each(getcontratos_user, function (chave, value) { 
                                if (value.id_user == setId) {
                                    $.each(item.roles, function (key, element) {
                                        var found= getVetorCargo.find(getRoles=> getRoles == element.id)
                                        if (found==undefined) {
                                            if (element.id == value.id_cargo) {
                                                getVetorCargo.push(value.id_cargo)
                                                getbtn_submeter=true;
                                                roles.append('<option value="'+element.id+'">' +element['current_translation'].display_name+ '</option>')                                    
                                            }
                                        }
                                    }); 
                                }
                            }); 
                            
                        }
                    });
                   
                }  
            });
            perfilUser.append(div_container)
            roles.selectpicker('refresh');
            getbtn_submeter==true?$(".btn-Processar").attr('hidden',false):$(".btn-Processar").attr('hidden',true)
        }
      });

     $("#roles").change(function (e) { 
        var getId_role=$("#roles").val()
        var pesquiLastSalario=false;
        var pesquiSalarioDocente=false;
        var valorSalarioHora;
        var valorSalarioTempo;
        salarioBaseFun.val(null)
        valorTempoHora.val(null)
        if(getIdUser.length>1 || getId_role.length>1) {
            $('.div-valorHora-tempo').attr('hidden',true)
            $('.div-salarioBase').attr('hidden',true)
        }else{
            $('.div-valorHora-tempo').attr('hidden',false)
            $('.div-salarioBase').attr('hidden',false)
            console.log(getSalario_fun);
            $.each(getSalario_fun, function (index, item) { 
                if (item.id_user==getIdUser && pesquiLastSalario==false && getId_role==item.id_cargo) {
                   if (item.id_horalaboral==null) {
                    // calcular o vencimento de todos aqueles que são docentes.
                    pesquiSalarioDocente=true;
                    pesquiLastSalario=true
                   } else {
                        pesquiLastSalario=true
                        valorSalarioHora=item.salarioBase/(item.dias_trabalho * item.total_horas_dia)
                        salarioBaseFun.val(item.salarioBase.toLocaleString('pt-br', {minimumFractionDigits: 2}))
                        $("#id_salarioBase").val(item.id)
                        valorTempoHora.val(valorSalarioHora.toLocaleString('pt-br', {minimumFractionDigits: 2}))
                   }
                    
                }
            });
            // pesquisar o tempo de aulas que o docente, estágiario tem
            if (pesquiSalarioDocente==true) {
                $.ajax({
                    url: 'recuso-humano-ajaxDocentePlanoAula/'+getIdUser,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data)  {
                    var response=data['data'];
                    if (data!=null) {
                        if (response.disciplinaDocente==false) {
                            salarioBaseFun.val("Docente sem disciplina")
                        } else {
                            salarioBaseFun.val(response.valorSalarioBase.toLocaleString('pt-br', {minimumFractionDigits: 2}))
                            $("#id_salarioBase").val(response.id)
                            valorTempoHora.val(response.valorTempo.toLocaleString('pt-br', {minimumFractionDigits: 2}))
                        }
                       
                    }
                    
                })
                
            }    
        }
      });  

      function getRolesSelect(getUsers_Role) {
        var setRoles=[];
        cargoModal.empty()
        cargoModal.append('<option ></option>')  
        $.each(getUsers_Role, function (index, item) { 
            $.each(item.roles, function (key, element) { 
                var found=setRoles.find(value => value == element.id)
                if (found == undefined) {
                    setRoles.push(element.id)
                    cargoModal.append('<option value="' +element.id+ '">' +element['current_translation'].display_name+ '</option>')  
                }
            }); 
           
           }); 
           cargoModal.selectpicker('refresh');
      }
</script>
@endsection