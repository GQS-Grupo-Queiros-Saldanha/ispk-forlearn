@section('title',__('Configurar documentação'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include('Avaliations::requerimento.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Configurações</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <h1>configurar documentação </h1>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="float-right mr-4" style="width:200px; !important">
                        </div>

                        <div class="card">
                            <div class="card-body">
                               <form action="{{route('document.generate-configuration')}}" method="POST" >
                                   @csrf
                                   @method('POST')
                      

                                   <div class="row">
                                  

                                       <div class="col-6">
                                       <div class="form-group">
                                           <label for="">Tipo de documento</label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="type_document" data-actions-box="false" data-selected-text-format="values" name="type_document" tabindex="-98">
                                               
                                              
                                                {{-- <option value="1">
                                                   Declaração sem notas
                                                </option>
                                                <option value="2">
                                                   Declaração com  notas
                                                </option>
                                                <option value="3">
                                                    Certificado de mérito
                                                 </option>
                                                <option value="4">
                                                    Certificado
                                                 </option> --}}
                                                 
                                            </select>
                                        </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Cabeçaho</label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="cabecalho" data-actions-box="false" data-selected-text-format="values" name="cabecalho" tabindex="-98">  

                                               <option value="">
                                                 selecione uma opção
                                                </option>

                                               <option value="1">
                                                  Com cabeçalho
                                                </option>
                                                
                                                <option value="2" >
                                                  Sem cabeçaho
                                                </option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>

                                

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Marca de água</label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="marca" data-actions-box="false" data-selected-text-format="values" name="marca" tabindex="-98">  
                                            <option value="">
                                                Selecione uma opção
                                             </option>

                                               <option value="1">
                                                  Com marca de água
                                                </option>
                                                
                                                <option value="2" >
                                                  Sem marca de água
                                                </option>
                                                
                                            </select>
                                        </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Rodapé</label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="rodape" data-actions-box="false" data-selected-text-format="values" name="rodape" tabindex="-98">  

                                               <option value="">
                                                    Selecione uma opção
                                                </option>
                                               <option value="1">
                                                  Com rodapé
                                                </option>
                                                
                                                <option value="2">
                                                  Sem Rodapé
                                                </option>
                                                
                                            </select>
                                        </div>
                                        </div>


                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Posição do título  </label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm"  id="position_title" data-actions-box="false" data-selected-text-format="values" name="titulo_position" tabindex="-98">  

                                               <option value="">
                                                    Selecione uma opção
                                                </option>
                                              
                                                
                                                <option value="1">
                                                  esquerda
                                                </option>
                                                <option value="2">
                                                    centro
                                                </option>
                                                <option value="3">
                                                  direita
                                                </option>
                                                
                                               
                                                
                                            </select>
                                        </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Tamanho da letra</label>
                                           <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="fonte" data-actions-box="false" data-selected-text-format="values" name="font_letra" tabindex="-98">  

                                               <option value="">
                                                 Selecione uma opção
                                                </option>
                                               <option value="10">
                                                  10 pt
                                                </option>
                                                
                                                <option value="11">
                                                 11 pt
                                                </option>
                                                
                                                <option value="12">
                                                 12 pt
                                                </option>
                                                
                                                <option value="13">
                                                 13 pt
                                                </option>
                                                <option value="14">
                                                 14 pt
                                                </option>  
                                                <option value="15">
                                                 15 pt
                                                </option>
                                                 <option value="16">
                                                 16 pt
                                                </option>
                                                 <option value="17">
                                                 17 pt
                                                </option>
                                                  <option value="18">
                                                 18 pt
                                                </option>
                                                 <option value="19">
                                                 19 pt
                                                </option>
                                                 <option value="20">
                                                 20 pt
                                                </option>
                                                 <option value="21">
                                                 21 pt
                                                </option>
                                                <option value="22">
                                                 22 pt
                                                </option>
                                                 <option value="23">
                                                 23 pt
                                                </option>
                                                 <option value="24">
                                                 24 pt
                                                </option>
                                                 <option value="25">
                                                 25 pt
                                                </option>
                                                
                                                
                                            </select>
                                        </div>
                                        </div>
                                        {{-- <div class="col-12">
                                            <div class="form-group">
                                                <label for="">Corpo do documento</label>
                                                <textarea name="descricao" id="" cols="30" rows="10" class="form-control">

                                                </textarea>
                                        </div>
                                        </div> --}}
         
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-success float-right">
                                                Guardar 
                                            </button>
                                        </div>
                                  
                        
                            
                                </form>
                              
                                  <div class="col-12">
                                        <br>  <br>  
                                <table class="table table-borderless">
                                        <thead style="background-color:black;color:white; padding:15px;">
                                            <tr>
                                                <td>Documento</td>
                                                <td>Cabeçalho</td>
                                                <td>Marca de água</td>
                                                <td>Rodapé</td>
                                                <td>Posição do título</td>
                                                <td>Tamanho da letra</td>
                                            </tr>
                                        </thead> 
                                         <tbody>
                                             
                                             @foreach($configDoc as $dock)
                                            <tr>
                                                @if($dock->document_type==1)
                                                <td>Declaração sem notas</td>
                                                 @elseif($dock->document_type==2)
                                                <td>Declaração com notas</td>
                                                  @elseif($dock->document_type==3)
                                                <td>Certificado de mérito</td>
                                                @elseif($dock->document_type==4)
                                                <td>Certificado</td>
                                               
                                                @else
                                                 <td>Outro</td>
                                                @endif
                                                
                                                @if($dock->cabecalho==1)
                                                <td>Activo</td>
                                                @else
                                                 <td>Não Activo</td>
                                                @endif
                                                
                                                
                                                   
                                                @if($dock->marca_agua==1)
                                                <td>Activo</td>
                                                @else
                                                 <td>Não Activo</td>
                                                @endif
                                                
                                                @if($dock->rodape==1)
                                                <td>Activo</td>
                                                @else
                                                 <td>Não Activo</td>
                                                @endif
                                                
                                                  @if($dock->titulo_position==1)
                                                <td>Esquerda</td>
                                                  @elseif($dock->titulo_position==2)
                                                <td>Centro</td>
                                                @else
                                                <td>Direita</td>
                                        
                                                @endif
                                                
                                                <td>{{$dock->tamanho_fonte}} Pt</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                              </table>
                                      </div>
                                
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>
        $(function(){
            var selectSType = $("#type_document");
                $.ajax({
                    url: "/gestao-academica/document_type/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function (data){
                    //if (dataResult.length) {
                        selectSType.prop('disabled', true);
                        selectSType.empty();

                        selectSType.append('<option value="">Selecione uma opção</option>');
                        $.each(data, function (index, row) {
                            selectSType.append('<option value="' + row.id + '">' + row.name +  '</option>');
                        });
                        selectSType.prop('disabled', false);
                        selectSType.selectpicker('refresh');
                    //}
                });



         
        })
    </script>


@endsection
