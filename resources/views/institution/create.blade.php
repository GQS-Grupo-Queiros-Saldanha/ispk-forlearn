@extends('layouts.backoffice')

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                           Instituição de Ensino
                        </h1>
                    </div>
                </div>
                                
                <form method="POST" action="{{ route('institution.store') }}" accept-charset="UTF-8"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-5">DADOS GERAIS</h3>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="nome">Nome</label>
                                        <input type="text" class="form-control" placeholder="Nome da Instituição" required="" autocomplete="nome" name="nome" id="nome">
                                    </div>                                
                                    <div class="form-group col">
                                        <label for="morada">Morada</label>
                                        <input type="text" class="form-control" placeholder="Morada da Instituição" required="" autocomplete="morada" name="morada" id="morada">
                                    </div>
									<div class="form-group col">
										<label for="provincia">Província</label>
										<select autocomplete="provincia" name="provincia" type="text" id="provincia" class="form-control form-control-sm">
											<option value="" selected disabled></option>
											<option value="Bengo">Bengo</option>
											<option value="Benguela">Benguela</option>
											<option value="Cabinda">Cabinda</option>
											<option value="Cunene">Cunene</option>
											<option value="Cuando Cubango">Cuando Cubango</option>
											<option value="Cuanza Norte">Cuanza Norte</option>
											<option value="Cuanza Sul">Cuanza Sul</option>
											<option value="Huambo">Huambo</option>
											<option value="Huila">Huila</option>
											<option value="Luanda">Luanda</option>
											<option value="Lunda Norte">Lunda Norte</option>
											<option value="Lunda Sul">Lunda Sul</option>
											<option value="Malange">Malange</option>
											<option value="Moxico">Moxico</option>
											<option value="Namibe">Namibe</option>
											<option value="Uige">Uige</option>
											<option value="Zaire">Zaire</option>
											<option value="Bié">Bié</option>
										</select>
									</div> 
									<div class="form-group col">
										<label for="municipio">Município</label>
										<select autocomplete="municipio" name="municipio" type="text" id="municipio" class="form-control form-control-sm">
											<option value="" selected disabled></option>
											<option value="Benguela">Benguela</option>
											<option value="Lobito">Lobito</option>
											<option value="Catubela">Catubela</option>
										</select>
									</div>
                                    <div class="form-group col">
                                        <label for="contribuinte">Contribuinte</label>
                                        <input type="text" class="form-control" placeholder="Nº de Contribuinte da Instituição" required="" autocomplete="contribuinte" name="contribuinte" id="contribuinte">
                                    </div>
                                    <div class="form-group col">
                                        <label for="capital_social">Capital social</label>
                                        <input type="number" class="form-control" placeholder="Capital social da Instituição" required="" autocomplete="capital_social" name="capital_social" id="capital_social">
                                    </div>
                                    <div class="form-group col">
                                        <label for="registro_comercial_n">Registro comercial nº</label>
                                        <input type="text" class="form-control" placeholder="Número do registro comercial" required="" autocomplete="registro_comercial_n" name="registro_comercial_n" id="registro_comercial_n">
                                    </div>
                                    <div class="form-group col">
                                        <label for="registro_comercial_de">Conservatória do registro comercial</label>
                                        <select autocomplete="registro_comercial_de" name="registro_comercial_de" type="text" id="registro_comercial_de" class="form-control form-control-sm">
                                            <option value="" selected disabled></option>
                                            <option value="Bengo">Bengo</option>
                                            <option value="Benguela">Benguela</option>
                                            <option value="Cabinda">Cabinda</option>
                                            <option value="Cunene">Cunene</option>
                                            <option value="Cuando Cubango">Cuando Cubango</option>
                                            <option value="Cuanza Norte">Cuanza Norte</option>
                                            <option value="Cuanza Sul">Cuanza Sul</option>
                                            <option value="Huambo">Huambo</option>
                                            <option value="Huila">Huila</option>
                                            <option value="Luanda">Luanda</option>
                                            <option value="Lunda Norte">Lunda Norte</option>
                                            <option value="Lunda Sul">Lunda Sul</option>
                                            <option value="Malange">Malange</option>
                                            <option value="Moxico">Moxico</option>
                                            <option value="Namibe">Namibe</option>
                                            <option value="Uige">Uige</option>
                                            <option value="Zaire">Zaire</option>
                                            <option value="Bié">Bié</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col" style="margin-bottom: 24px;">
                                        <div class="form-group">
                                            <label for="logotipo">Logo</label>
                                            <div class="custom-file-upload">
                                                <div class="file-upload-wrapper">
                                                    <input type="file"  required="" class="attachment custom-file-upload-hidden" id="logotipo" name="logotipo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="decreto_instituicao">Decreto da Instituição</label> 
                                            <textarea id="decreto_instituicao" name="decreto_instituicao" rows="4" cols="50"> </textarea>                                                                                       
                                            <div class="custom-file-upload">
                                                <div class="file-upload-wrapper">
                                                    <input type="file"  required="" class="attachment custom-file-upload-hidden" id="instituicao_arquivo" name="instituicao_arquivo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                                </div>
                                            </div>
                                        </div>                                            
                                        <div class="form-group">
                                            <label for="decreto_cursos">Decreto Cursos</label>
                                            <textarea id="decreto_cursos" name="decreto_cursos" rows="4" cols="50"> </textarea>
                                            <div class="custom-file-upload">
                                                <div class="file-upload-wrapper">
                                                    <input type="file"  required="" class="attachment custom-file-upload-hidden" id="cursos_arquivo" name="cursos_arquivo" value="" tabindex="-1" style="position: absolute; left: -9999px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="dominio_internet">Domínio de internet</label>
                                            <input type="text" class="form-control" placeholder="Domínio de Internet" required="" autocomplete="dominio_internet" name="dominio_internet" id="dominio_internet">
                                        </div>										
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-5">CONTACTOS GERAIS</h3> 
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="telefone_geral">Telefone geral</label>
                                        <input type="text" class="form-control" placeholder="Telefone geral" required="" autocomplete="telefone_geral" name="telefone_geral" id="telefone_geral">
                                    </div>
                                    <div class="form-group col">
                                        <label for="telemovel_geral">Telemóvel geral</label>
                                        <input type="text" class="form-control" placeholder="Telemóvel geral" required="" autocomplete="telemovel_geral" name="telemovel_geral" id="telemovel_geral">
                                    </div>
									<div class="form-group col">
										<label for="email">E-mail</label>
										<input type="email" class="form-control" placeholder="E-mail da Instituição" required="" autocomplete="email" name="email" id="email">
									</div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group " style="margin-bottom: 24px;">
                                        <div class="form-group">
                                            <label for="whatsapp">Whatssap</label>
                                            <input type="text" class="form-control" placeholder="Whatssap" required="" autocomplete="whatsapp" name="whatsapp" id="whatsapp">
                                        </div>
                                        <div class="form-group">
                                            <label for="facebook">Facebook</label>
                                            <input type="text" class="form-control" placeholder="Facebook" required="" autocomplete="facebook" name="facebook" id="facebook">
                                        </div>
                                        <div class="form-group">
                                            <label for="instagram">Instagram</label>
                                            <input type="text" class="form-control" placeholder="Instagram" required="" autocomplete="instagram" name="instagram" id="instagram">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-5">DIREÇÃO ACADÉMICA</h3>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="director_geral">Director(a) geral</label>
                                        <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="director_geral" data-actions-box="false" data-selected-text-format="values" name="director_geral">
                                            <option selected value=""></option>
                                            <option value={{$institution_cargos[0]->users_id}}>{{$institution_cargos[0]->email}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="vice_director_academica">Vice-director(a) área académica</label>
                                        <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="vice_director_academica" data-actions-box="false" data-selected-text-format="values" name="vice_director_academica">
                                            <option selected value=""></option>
                                            <option value={{$institution_cargos[1]->users_id}}>{{$institution_cargos[1]->email}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="vice_director_cientifica">Vice-director(a) área científica</label>
                                        <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="vice_director_cientifica" data-actions-box="false" data-selected-text-format="values" name="vice_director_cientifica">
                                            <option selected value=""></option>
                                            <option value={{$institution_cargos[2]->users_id}}>{{$institution_cargos[2]->email}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col" style="margin-bottom: 24px;">
                                        <div class="form-group col">
                                            <label for="daac">DAAC</label>
                                            <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="daac" data-actions-box="false" data-selected-text-format="values" name="daac">
                                                <option selected value=""></option>
                                                <option value={{$institution_cargos[3]->users_id}}>{{$institution_cargos[3]->email}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <label for="gabinete_termos">Gabinete de termos</label>
                                            <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="gabinete_termos" data-actions-box="false" data-selected-text-format="values" name="gabinete_termos">
                                                <option selected value=""></option>
                                                <option value={{$institution_cargos[4]->users_id}}>{{$institution_cargos[4]->email}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col">
                                            <label for="secretaria_academica">Secretaria académica</label>
                                            <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="secretaria_academica" data-actions-box="false" data-selected-text-format="values" name="secretaria_academica">
                                                <option selected value=""></option>
                                                <option value={{$institution_cargos[5]->users_id}}>{{$institution_cargos[5]->email}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-5">DIRECÇÃO EXECUTIVA</h3> 
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="director_executivo">Director(a) executivo</label>
                                        <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="director_executivo" data-actions-box="false" data-selected-text-format="values" name="director_executivo">
                                            <option selected value=""></option>
                                            <option value=75>{{$institution_cargos[6]->email}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col" style="margin-bottom: 24px;">
                                        <label for="recursos_humanos">Recursos Humanos</label>
                                        <select data-live-search="true" class="selectpicker form-control form-control-sm" required id="recursos_humanos" data-actions-box="false" data-selected-text-format="values" name="recursos_humanos">
                                            <option selected value=""></option>
                                            <option value={{$institution_cargos[7]->users_id}}>{{$institution_cargos[7]->email}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-5">PROPRETÁRIO DA IE</h3>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="nome_dono">Nome do propriétário</label>
                                        <input type="text" class="form-control" placeholder="Nome do Propriétário" required="" autocomplete="nome_dono" name="nome_dono" id="nome_dono">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col" style="margin-bottom: 24px;">                                        
                                        <div class="form-group col">
                                            <label for="nif">NIF do propriétário</label>
                                            <input type="text" class="form-control" placeholder="NIF do Propriétário" required="" autocomplete="nif" name="nif" id="nif">
                                        </div>    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">      
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="float-right" style="margin-bottom: 50px;">                                        
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Salvar dados
                                        </button>    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                          
                </form>  

            </div>
        </div>
    </div>

@endsection