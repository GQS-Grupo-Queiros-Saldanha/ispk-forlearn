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
    .fotoUserFunc {
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
    .close-form-salario:hover{
        cursor: pointer;
    }
    .modal-body span {
        font-size: 13px;
        color: black;
    }
    .form-group span {
        font-weight: 0;
    }
</style>

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>
 <!-- Modal para adicionar função  -->
<div style="z-index: 999999" class="modal fade table-responsive col-md-12" id="add-funcao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"aria-hidden="true">
     <div style="max-width: 100%;" class="modal-dialog m-0">
         <div  class="modal-content rounded mt-1">
             <div style="background:#20c7f9;width: 100%;border-top-left-radius: 2px;border-top-right-radius: 2px;height: 6px;" class="m-0" ></div>
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLongTitle">Add Função!</h5>
                 <button type="button" class="close btn-choseModal" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body row">
                 <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col">
                     <div class="col-12 mb-4">
                         <form method="POST" action="{{ route('recurso.add-funcao-funcionario') }}" class="pb-4 border-bottom">
                             @csrf
                             <input type="hidden" class="form-control idFuncionario" value="" name="idFuncionario">
                             <div class="form-group col-md-12 pr-0 pl-0 mr-0 ml-0" >
                                <label class="m-0" for="inputEmail4">Selecione função</label>
                                <div class="input-group mt-1">
                                  <div  class="input-group-prepend m-0 p-0">
                                    <div style="background: #7eaf3e" class="input-group-text p-0 m-0"><button style="overflow-inline: none; outline: hidden"  data-toggle="modal" data-type="editar" data-target="#criar_funcao" class="btn m-0 p-0 text-white"><i class="m-0 p-0 fas fa-plus" ></i></button>  
                                    </div>
                                  </div>
                                  <select name="funcao[]"  multiple="" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                    @foreach ($getfuncoes as $item)
                                        <option value="{{$item->id}}">{{$item->display_name}}</option>
                                    @endforeach
                                  </select>
                                </div>
                             </div>
                             <div class="form-row">
                                 <div class="form-group col-md-6">
                                     <label for="inputEmail4">Data de inicio do contrato de trabalho (Exer. Função)</label>
                                     <input required  type="date" class="form-control" name="dataIncial">
                                 </div>
                                 <div class="form-group col-md-6">
                                     <label for="inputEmail4">Data de termo do contrato de trabalho (Exer. Função)</label>
                                     <input required  type="date" class="form-control" name="dataFinal" >
                                 </div>
                             </div>
                             <div class="form-group">
                                <label for="exampleFormControlTextarea1">Nota</label>
                                <textarea required class="form-control" name="nota" id="exampleFormControlTextarea1" rows="3"></textarea>
                             </div>
                             <button style="background: #20c7f9" type="submit" class="btn text-white">Gravar</button>
                         </form>
                     </div>
                 </div>
                 <div class="col ml-0  pl-0">
                    <div style="background: #eff3f5" class="container jumbotron  pb-3  rounded">
                        <h1 class="ml-0 pl-0 funcao-name-funcionario"></h1>
                        <hr class="my-4">
                        <p>De acordo ao/aos contrato/os criado ao/a  <b>Sr.</b> enncontram-se as seguintes funções atribuidas: </p>
                        {{-- informação sobre as funções a ser exercidas --}}
                        <div class="m-0 p-0" id="div-funcao">
                            
                        </div>
                    </div>
                </div>
             </div>
         </div>
     </div>
</div>

{{-- Modal para criar função --}}
<div style="z-index: 99999999; background: #0000009e" class="modal fade" id="criar_funcao" tabindex="-2" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Criar função</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">
                    <form method="POST" action="{{ route('recurso.create-FuncaoRH_contrato') }}" class="pb-4">
                        @csrf
                        <div class="form-group">
                            <label for="inputAddress">nome</label>
                            <input  type="text" class="form-control" name="display_name" id="name_funcao" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Descrição</label>
                            <input  type="text" class="form-control" name="descricao" id="descricao" placeholder="">
                        </div>
                        <button type="submint" class="btn btn-primary btn-submintEditar">Gravar</button>
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
                    <h1>{{$action}}</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')

            <div style="background-color:#f6fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                        
                        <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase"><i class="fas fa-user-tie"> </i> Criar contrato</h5>
                        {{--formularios--}}
                        <div class="col-12 mb-4 ">
                            <form method="POST" action="{{ route('recurso.contrato-funcionario') }}" enctype="multipart/form-data" accept-charset="UTF-8" class="mb-3 pb-1 border-bottom">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Funcionário/os</label>
                                            <select data-live-search="true"  required class="selectpicker form-control" required="" id="funcionario-contrato" data-actions-box="false" data-selected-text-format="values" name="funcionario" tabindex="-98">
                                                <option  selected></option>
                                                @foreach ($users as $element)
                                                    <option value="{{$element->id}}">{{$element->full_name}} - {{$element->email}}</option> 
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <label class="m-0 p-0" for="inputPassword4">Tipo de contrato</label>
                                            <select data-live-search="true"  required id="tipo-contrato" class="selectpicker form-control" required="" id="presenca" data-actions-box="false" data-selected-text-format="values" name="presenca" tabindex="-98">
                                                <option selected></option>
                                                <option value="1">Contrato por tempo determinado</option>
                                                <option value="2">Contrato por tempo indeterminado</option>
                                                <option value="3">Contrato de trabalho eventual</option>
                                                <option value="4">Contrato de estágio</option>
                                                <option value="5">Contrato de experiência</option>
                                                <option value="6">Contrato de teletrabalho;</option>
                                                <option value="7">Contrato intermitente</option>
                                                <option value="8">Contrato de trabalho à tarefa</option>
                                            </select>
                                        </div>   
                                        <div class="form-group col custom-file-upload">
                                            <div class="file-upload-wrapper" id="file-upload-wrapper">
                                                <input type="file" class="attachment custom-file-upload-hidden" id="arquivo" name="arquivo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword4">Cargos</label>
                                            <select data-live-search="true" required  required class="selectpicker form-control" required="" id="roles" data-actions-box="false" data-selected-text-format="values" name="roles" tabindex="-98">
                                            
                                            </select>
                                        </div>                                    
                                        <div class="form-group col-md">
                                            <label for="inputPassword4">Data de inicio do contrato de trabalho</label>
                                            <input required type="date" class="form-control" name="dataIncial" id="dataIncial" >
                                        </div>
                                        <div class="form-group col-md" id="data-termino-contrato">
                                            <label for="inputPassword4">Data de Término do contrato de trabalho</label>
                                            <input required type="date" class="form-control" name="dataFinal"  id="dataFinal" >
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTipoIRT">IRT</label>
                                            <select data-live-search="true"  required class="selectpicker form-control" required="" id="inputTipoIRT" data-actions-box="false" data-selected-text-format="values" name="tipo_irt" tabindex="-98">
                                                <option  selected></option>
                                                <option value="IRT_OUTREM">IRT conta de outrem</option>
                                                <option value="IRT_PROPRIA">IRT conta própria</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group  col-md-2 pl-0 pb-0 mb-0">
                                        <button type="submit" type="button"   style="background: #2b9fc2 "  class="btn text-white">Gravar</button>
                                    </div> --}}
                                {{-- </form> --}}

                                <div  class="form-row ">
                                    <div hidden class="form-group col-md-4 infor-user">
                                        <div  class="card card-widget widget-user">
                                            <div class="widget-user-image text-center">
                                                <center>
                                                    <div class="fotoUserFunc mt-3 mb-2" ></div>
                                                </center> 
                                            </div>
                                            <div  class="widget-user-header text-center">
                                                <div class="">
                                                    <h4 class="widget-user-username"><strong class="name-user"></strong> </h4>
                                                    <h6 class="widget-user-desc ">Estado do contrato - <b style="color: #7eaf3e" class="user-contrato"></b></h5>
                                                </div> 
                                            </div>
                                            <div style="background: white; color:black" class="card-footer p-0 align-self-end col">
                                                <div class="row">
                                                    <div class="col-5 border-right m-0 p-0 pl-2 pt-2 pb-2 ">
                                                        <div class="description-block text-center">
                                                            <p class="description-header  user-idade"> </p>
                                                            <span class="description-text"><b>IDADE</b></span>
                                                        </div>
                                                    </div>
                                                    <div class="col m-0 p-0 pl-2 pt-2 pb-2">
                                                        <div class="description-block  text-center m-0 p-0">
                                                            <p class="description-header  user-contactos"></p>
                                                            <span class="description-text"><b>CONTACTOS</b></span>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col m-0 p-0">
                                                        <div class="description-block  text-center m-0 p-0">
                                                            <h6 class="description-header text-center "> <br> </h6>
                                                            <button style="background: white;" type="button"  class="btn btn-sm m-0 open-form-salario"><i class="fas fa-money-bill-wave"></i></button>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                                <div hidden class="form-group ml-0 btn-add-funcao mb-0">
                                                    <button type="button" data-toggle="modal" data-type="addFuncao" data-target="#add-funcao"  style="background: #20c7f9"  class="btn text-white">Atribuir função</button>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>


                                    <div hidden class="form-group col-md-8 form-salario">
                                        <div class="p-0 bg-white">
                                            <div style="background: #2b9fc2; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-2 "></div>
                                            <div class="text-right text-muted mr-3 mt-4">
                                                {{-- <h6  class="float-right mr-3 col-1 text-uppercase close-form-salario"><i class="fas fa-x"> </i></h6> --}}
                                                <button style="background: #15b6e7; color:white" type="button"  class="btn btn-sm m-0 open-form-salario-tempo pr-2 pl-2" style="padding-top = -15"><i  class="fas fa-person-chalkboard"></i></button>
                                                <button style="background: #00c4ffba;color:white" type="button"  class="btn btn-sm m-0 open-form-salario-hora" style="padding-top = -15"><i class="fa fa-user-clock"></i></button>
                                            </div>
                                            
                                            
                                                <input type="hidden" class="form-control" name="id_funSalario" id="id_funSalario">
                                                {{-- <div hidden class="form-group col">
                                                    <label for="inputPassword4">Cargos</label>
                                                    <select data-live-search="true" required  required class="form-control"  id="roleSalario" data-actions-box="false" data-selected-text-format="values" name="roleSalario" tabindex="-98">
                                                    
                                                    </select>
                                                </div> --}}
                                                <div class="ml-3 mb-3 form-check form-group col">
                                                    <input class="form-check-input" type="checkbox" value="" id="renovarSalario">
                                                    <label class="form-check-label pt-1" for="renovarSalario">Renovação  do salário base ?</label>
                                                </div>
                                                <div class="row col-md-12 mr-0 pr-3  ml-0 ">
                                                    <div style="background:#f8f9fa" class="form-group col-md-5">
                                                        <label class="border-bottom mb-0 pb-0" for="inputEmail4">Salário atual</label>
                                                        <h6 style="color: #6c6c6c;font-weight: bold" class="mt-0 pt-0"  id="salarioAntigo" ></h6>
                                                    </div>
                                                    
                                                    
                                                    <div class="form-group col-md-7 ml-0 pl-1 mb-3 form-salario-hora mr-0 pr-0">
                                                        <label class="m-0" for="inputEmail4">Nova remuneração</label> 
                                                        <div class="input-group mt-0">
                                                            <div class="input-group-prepend">
                                                            <div class="input-group-text">Kz</div>
                                                            </div>
                                                            <input type="text" maxlength="15"  class="form-control" name="valorSalario" id="valorSalario" >
                                                        </div>
                                                        <small style="font-weight: bold" id="salario" class="form-text text-muted"></small>
                                                    </div>
                                                    
                                                </div>
                                                <div class="row col-md-12 mr-0 pr-3  ml-0 ">
                                                    <div class="form-group col-md-6 ml-0 pl-0 horaLaboral">
                                                        <label for="inputEmail4">Hora laboral</label>
                                                        <select data-live-search="true"   class="selectpicker form-control" id="horaLaboral" data-actions-box="false" data-selected-text-format="values" name="horaLaboral" tabindex="-98">
                                                            <option  selected></option>
                                                            @foreach ($horas_laboral as $item)
                                                                <option value="{{$item->id}}">{{$item->dias_trabalho}}Dias | {{$item->total_horas_dia}}Horas / Entrada {{$item->entrada_1}} - Saida {{$item->saida_2}}</option>
                                                            @endforeach
                                                            
                                                        </select>
                                                    </div>
                                                    <div  class="form-group col-md-6 mr-0 pr-0 dataSalario">
                                                        <label for="inputEmail4">Data</label>
                                                        <input required type="month" class="form-control" name="dataSalario" id="dataSalario" >
                                                    </div>
                                                </div>
                                                <div  class="form-group col-12 ">
                                                   &nbsp; <h6 class="col-md-12 text-right m-0 p-0"><button  type="submit"  style="background: #1f7b97 "  class="btn text-white float-right col-2">Gravar</button></h6>
                                                </div>
                                            
                                        </div>
                                    </div>
                                </div>

                            </form>
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
     const fileWrapper= $('.file-upload-wrapper');
     const tipoIRT = $('#inputTipoIRT');

     fileWrapper.each((index,obj) => {
        let ob = $(obj);
        let children = ob.children('.file-upload-wrapper');
        if(children.length > 0){
            children.each((i, item)=>{
                if(!item.classList.contains("d-flex","grap-1")){
                    item.classList.add("d-flex","grap-1","mt-4");
                }
            })
        }
     });

    // variaveis
      var getUsers= @json($users);
      var getUsers_Role=JSON.parse(JSON.stringify(getUsers))
      var roles=$("#roles")
      var getcontratos=@json($getcontratos);
      var getcontratos_user=JSON.parse(JSON.stringify(getcontratos));

      var getSalariofuncionario=@json($getSalariofuncionario);
      var get_Salariofun=JSON.parse(JSON.stringify(getSalariofuncionario));

      var div_funcao=$("#div-funcao");
      var roleSalario=$("#roleSalario");
      var getfuncaoUsers=@json($getfuncaoUsers);
      var getfunUser=JSON.parse(JSON.stringify(getfuncaoUsers));
      var valorSalario=$("#valorSalario")
      var valorSalarioHoraDia=$("#valorSalarioHoraDia")
      var getIdUser=null;
      var vetorContrato=[];
      var cargoWithContrato=false;


       $(".forlearn-btn").empty(); 
       $(".forlearn-btn").css({
        paddingLeft: '3px',
        paddingRight: '3px',
        marginLeft: '1px'
       }); 
       $(".forlearn-btn").html('<i class="fas fa-file-upload" aria-hidden="true"></i>'); 

       $("#tipo-contrato").change(function (e) { 
            var tipo_contrato=$(this).val();
            tipo_contrato==2 ? $("#data-termino-contrato").attr('hidden',true) : $("#data-termino-contrato").attr('hidden',false);
            tipo_contrato==2 ? $("#dataFinal").prop('required',false) : $("#dataFinal").prop('required',true);
       });

      $("#valorSalario").keyup(function (e) {
          var valor = valorSalario.val();

          var er = /[^0-9.]/;
          er.lastIndex = 0;
          if (valor == '') {
              $("#salario").text("")
          } else {
              if (er.test(valorSalario.val())) {
                  valorSalario.val("");
              } else {
                  valor = Number.parseFloat(valorSalario.val())
                  $("#salario").html(valor.toLocaleString('pt-br', {
                      minimumFractionDigits: 2
                  }) + " <span>Kz</span>")
              }

          }
      });

      $("#renovarSalario").change(function (e) { 
        if($(this).is(":checked") && cargoWithContrato==true) {
            $("#dataIncial").attr('required',false)
            $("#dataFinal").attr('required',false)
            $("#presenca").attr('required',false)
        }else{
            $("#dataIncial").attr('required',true)
            $("#dataFinal").attr('required',true)
            $("#presenca").attr('required',true)
        }
        
      });
   
        $(".close-form-salario-tempo").click(function (e) { 
            $(".form-salario-tempo").slideUp(900);
        });
        $(".open-form-salario-tempo").click(function (e) { 
           
            $(".horaLaboral").attr('hidden',true)                  
            $(".dataSalario").attr('class','form-group col-md-12  ml-0 pl-0 mr-0 pr-0 dataSalario')
                             
        });

        $(".close-form-salario-hora").click(function (e) { 
            $(".form-salario-hora").slideUp(900);
        });
        $(".open-form-salario-hora").click(function (e) { 
            $(".horaLaboral").attr('hidden',false)                 
            $(".dataSalario").attr('class','form-group col-md-6  mr-0 pr-0 dataSalario')
        });
    //   FIM
     
     function selectChangeValueSelectpicker(name, value){
         if(value == "") return;
        let select = $("#"+name);
        let options = select.children('option');
        select.empty();
        options.each( (i, item) =>{
            select.append(`<option value="${item.value}" ${item.value == value ? 'selected' : ''}>${item.innerHTML.trim()}</option>`);
        })
        select.selectpicker('refresh');
     }
     
     function popularFuncionario(find, funcionario=null){
        selectChangeValueSelectpicker("roles", find ? funcionario.role_id : funcionario);
        selectChangeValueSelectpicker("inputTipoIRT", find ? funcionario.tipo_irt : funcionario);
        selectChangeValueSelectpicker("tipo-contrato", find ? funcionario.tipo_presenca : funcionario);
        $("#dataIncial").val(find ? funcionario.data_inicio_conrato : funcionario);
        $("#dataFinal").val(find ? funcionario.data_fim_contrato : funcionario);
     }
     
     function popularRecurso(find, recurso=null){
        selectChangeValueSelectpicker("horaLaboral",find ? recurso.id_horalaboral : recurso);
        $("#salarioAntigo").html(find ? recurso.salarioBase : recurso);
        $("#dataSalario").val(find ? recurso.dataSalario : recurso);
     }
     
     function getInfoContrato(id){
         if(id == "") return;
         
         let link = "/RH/contrato-info/"+id;
         $.ajax({
            url: link,
            type: 'GET',
            success: function(data) {
                
                if(data.funcionario){
                    popularFuncionario(true, data.funcionario);
                }else{
                    popularFuncionario(false);
                }
                
                if(data.recurso){
                    popularRecurso(true, data.recurso.dataSalario);
                }else{
                    popularRecurso(false);
                }
                
            }
        });
        
     }

      $("#funcionario-contrato").change(function (e) { 
        
         $(".form-salario").attr('hidden',false);
         $(".form-salario").slideDown(900);
         getIdUser= $("#funcionario-contrato").val()
         $("#salarioAntigo").text("")  
          var contrato=false;
          $(".idFuncionario").val()   
          $(".idFuncionario").val(getIdUser) 
          if(getIdUser=="") {
            $(".btn-add-funcao").attr('hidden',true);
            $(".infor-user").slideUp(800);
          } else {
            getFuncaoUser(getIdUser)
            // getInfoSalario(getIdUser)
            $(".btn-add-funcao").attr('hidden',false);
            $(".funcao-name-funcionario").text();
            $(".name-user").text();
            $(".user-contactos").text()
            $(".user-idade").text()
            $(".user-contrato").text()
            $(".fotoUserFunc").attr('style','')
            $(".infor-user").attr('hidden',false)
            $(".infor-user").slideUp(0);
            $(".infor-user").slideDown(800);

            $.each(getcontratos_user, function (key, item) { 
                 if (item.id_user==getIdUser) {
                    contrato=true
                 }
            });
            if (contrato==true) {
                $(".user-contrato").text()  
                $(".user-contrato").text("ACTIVO")
            } else {
                $(".user-contrato").text()  
                $(".user-contrato").text("N/A")
            }
            
            $.each(getUsers_Role, function (index, item) { 
                if (item.id==getIdUser) {
                    $(".user-contactos").text(item.telefone+" / "+item.whatApp)
                    $(".user-idade").text(item.idade)
                    $(".funcao-name-funcionario").text("Fun. : "+item.name);
                    $(".name-user").text(item.name);

                    $(".fotoUserFunc").attr('style',"background-image: url('//{{$_SERVER['HTTP_HOST']}}/users/avatar/"+item.fotografia+"')")
                    let roleSelect = document.querySelector("#roles");
                    //console.log(roleSelect)
                    roles.empty();
                    //roleSelect.innerHTML = '<option selected>Seleciona o cargo</option>';
                    roles.append('<option></option>')
                    $.each(item.roles, function (key, element) { 
                        $.each(getcontratos_user, function (chave, value) { 
                            if (value.id_cargo==element.id && getIdUser==value.id_user) {
                                vetorContrato.push(element.id)
                                roles.append('<option style="font-weight: bold; font-size: 1pc;color:blue" value="'+element.id+'">' +element['current_translation'].display_name+ '&nbsp;&nbsp;&nbsp; [<strong>Contrato activo</strong>]'+'</option>')
                                //roleSelect.innerHTML +='<option style="font-weight: bold; font-size: 1pc;color:blue"  value="'+element.id+'">' +element['current_translation'].display_name+ '&nbsp;&nbsp;&nbsp; [<strong>Contrato activo</strong>]'+'</option>'                               
                            }
                        });
                        var found= vetorContrato.find(id=> id==element.id)
                        if (found==undefined) {
                            roles.append('<option value="' +element.id+ '">' +element['current_translation'].display_name+ '</option>')
                            //roleSelect.innerHTML += '<option value="' +element.id+ '" >' +element['current_translation'].display_name+ '</option>';
                        }
                        
                    });
                    roles.selectpicker('refresh');
                }
            });
          }
          
         getInfoContrato(e.target.value);
            
      });
      
      $("#roles").change(function (e) { 
        //console.log(get_Salariofun)
        var role=$("#roles").val()
        $("#salarioAntigo").text("")  
        $("#id_funSalario").val(getIdUser)
        var pesquiLastSalario=false;
        var found= vetorContrato.find(id=> id==role)
        if (found!=undefined) {
            cargoWithContrato=true
        } else {
            cargoWithContrato=false
            
        }
        $.each(get_Salariofun, function (index, item) { 
             if (item.id_user==getIdUser && pesquiLastSalario==false && role==item.id_cargo && cargoWithContrato==true) {
                 pesquiLastSalario=true;
                 $("#salarioAntigo").text(item.salarioBase.toLocaleString('pt-br', {minimumFractionDigits: 2}))
             }
        });
          
      });

      function getFuncaoUser(getIdUser) { 
          var div="";
          //console.log(getfunUser)
          div_funcao.empty()
          var i=0;
          $.each(getfunUser, function (index, item) { 
              i++;
               if (item.id_user==getIdUser) {
                div+="<div class='accordion' id='accordionExample'>"+
                    "<div class='card rounded'>"+
                        "<div class='card-header bg-light border-top' id='heading"+i+"'>"+
                            "<h2 class='mb-0'>"+
                                "<button class='btn btn-link btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse"+i+"' aria-expanded='true' aria-controls='collapse"+i+"'>"+
                                    item.display_name+
                                 "</button>"+
                            "</h2>"+
                            "</div>"+
                            "<div  id='collapse"+i+"' class='collapse' aria-labelledby='heading"+i+"' data-parent='#accordionExample'>"+
                                "<div class='card-body'>"+
                                   "<div class='d-flex w-100 justify-content-between'>"+
                                        "<h5 class='mb-1'> Estado da função:<b> "+
                                         item.status_contrato_at_funcao+
                                        "</b></h5>"+
                                        "<small class='text-muted'>"+
                                            item.data_inicio_contrato_at_funcao+ " &nbsp; - , - &nbsp; "+item.data_fim_contrato_at_funcao+
                                        "</small>"+
                                   "</div>"+
                                   "<p class='mb-1'>"+
                                        item.nota+
                                    "</p>"+
                                    "<small class='text-muted'>Criado aos: "+
                                        item.created_at+
                                    "</small>"+
                                    "<div>"+
                                        "<button value='"+item.id+"' onclick='delete_funcaoFuncionario("+item.id_fun_with_cont_funcao+")'  class='btn btn-success btn-elimina-funcion'>Eliminar</button>"+
                                    "</div>"+
                                "</div>"+
                            "</div>"
                  div+="</div></div>"
               }
          });
          div_funcao.append(div)
          
      }
      function getInfoSalario(getIdUser) {
        roleSalario.empty();
        roleSalario.append('<option selected></option>')
        
        $("#id_funSalario").val(getIdUser)
          $.each(getcontratos_user, function (index, item) { 
              if (item.id_user==getIdUser) {
                  $.each(getUsers_Role, function (set, value) { 
                      $.each(value['roles'], function (key, element) {
                          if (value.id==getIdUser && element.id == item.id_cargo){
                              roleSalario.append('<option value="' + element.id + '">' + element['current_translation'].display_name + '</option>')

                          }
                      });
                  });
              }
            
          });
          roleSalario.selectpicker('refresh');
      }
        function delete_funcaoFuncionario(getIdFuncao) { 
           // console.log(getIdFuncao)
            $.ajax({
                url: 'recurso_delete_funcaoFuncionario/'+getIdFuncao,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(response)  {
                // console.log(response)
                setTimeout(() => {
                    location.reload(true);
                }, 600);
            })
        }
    </script>
@endsection


