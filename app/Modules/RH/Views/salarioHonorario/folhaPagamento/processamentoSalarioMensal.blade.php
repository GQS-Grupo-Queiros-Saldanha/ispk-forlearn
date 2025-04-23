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

                            <div class="col-md-12 align-items-end ">
                                <div class="float-right  d-flex flex-row-reverse bd-highlight">
                                    <div class="p-2 bd-highlight"><h5 class="text-muted text-uppercase"> Folha de salários</h5></div>
                                    {{-- <div class="pr-1 pl-1 pt-0 mt-0 bd-highlight"><button data-toggle="modal" data-type="processoSalario-geral" data-target="#processoSalario-geral" type="button" type="button" style="background: #2b9fc2 " class="p-2 pr-3 pl-3 btn btn-sm text-white"><i class="fa-solid fa-file-invoice"></i> <i class="fa-solid fa-g"></i></button></div> --}}
                                </div>
                            </div>
                           

                            @if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))    
                                <div class="col-md-12 row mb-4 mt-2 pr-0">
                                    <div class="mr-0 pr-0 col-md-8 border-bottom">

                                        <form method="POST" action="{{ route('recurso_humano.get-processoSalarioMes') }}" class="pb-4 border-bottom col-10" target="_blank">
                                            @csrf
                                            {{-- 
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
                                            --}}
                                            <div class="form-group">
                                                <div class="form-row">
                                                    <label for="inputEmail4">Cargo</label>
                                                    <select required  name="cargoModal[]" id="cargoModal" autocomplete="" multiple="" class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                                        

                                                    </select>
                                                </div>                                                
                                            </div>

                                            <div class="form-group">
                                                <div class="form-row">
                                                    <label for="inputEmail4">Vencimente referente a</label>
                                                    <select required  name="vencimentoMonth" autocomplete=""  class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98">
                                                        {{-- <option ></option> --}}
                                                        @foreach ($getProcessoSalario as $Year_mothy => $item)
                                                            <option value="{{$Year_mothy}}">{{$Year_mothy}}</option>   
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                            </div>

                                            <button style="background: #20c7f9" type="submit" class="btn text-white">GERAR PDF</button>
                                        </form>
                                        
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
    
    
    var cargoModal=$("#cargoModal")
    var getUsers= @json($users);
    var getUsers_Role=JSON.parse(JSON.stringify(getUsers))

    getRolesSelect(getUsers_Role)

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