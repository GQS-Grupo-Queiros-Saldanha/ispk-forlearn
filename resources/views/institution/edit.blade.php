@extends('layouts.generic_index_new')
<title>Editar Instituição de ensino | forLEARN® by GQS</title>
@section('page-title', 'Instituição de Ensino')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('institution.show') }}">Instituição</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Edição</li>
@endsection
@section('body')
    <div class="" style="padding: 0;">
        <form method="POST" action="{{ route('institution.update') }}" accept-charset="UTF-8" enctype="multipart/form-data"
            style="border: none !important;">
            @csrf
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button"
                        role="tab" aria-controls="home" aria-selected="true">
                        <span>DADOS GERAIS</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                        role="tab" aria-controls="profile" aria-selected="false">
                        <span>CONTACTOS GERAIS</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact" type="button"
                        role="tab" aria-controls="contact" aria-selected="false">
                        <span>DIREÇÃO ACADÉMICA</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="direcao-tab" data-toggle="tab" data-target="#direcao" type="button"
                        role="tab" aria-controls="direcao" aria-selected="false">
                        <span>DIRECÇÃO EXECUTIVA</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="propriet-tab" data-toggle="tab" data-target="#propriet" type="button"
                        role="tab" aria-controls="propriet" aria-selected="false">
                        <span>PROPRETÁRIO DA IE</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="submit" class="btn btn-success">
                        Editar dados
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" name="nome" value="{{ $institution->nome }}"
                                    id="nome">
                            </div>
                            <div class="form-group col">
                                <label for="abrev">Abreviatura</label>
                                <input type="text" class="form-control" name="abrev" value="{{ $institution->abrev ?? '' }}"
                                    id="abrev">
                            </div>                            
                            <div class="form-group col">
                                <label for="morada">Morada</label>
                                <input type="text" class="form-control" placeholder="Morada da Instituição"
                                    autocomplete="morada" name="morada" value="{{ $institution->morada }}" id="morada">
                            </div>
                            <div class="form-group col">
                                <label for="provincia">Província</label>
                                <!--<select autocomplete="provincia" class="form-control" name="provincia" type="text"-->
                                <!--    id="provincia" class="form-control form-control-sm">-->
                                <!--    <option value="{{ $institution->provincia }}">{{ $institution->provincia }}</option>-->
                                <!--    <option value="Bengo">Bengo</option>-->
                                <!--    <option value="Benguela">Benguela</option>-->
                                <!--    <option value="Cabinda">Cabinda</option>-->
                                <!--    <option value="Cunene">Cunene</option>-->
                                <!--    <option value="Cuando Cubango">Cuando Cubango</option>-->
                                <!--    <option value="Cuanza Norte">Cuanza Norte</option>-->
                                <!--    <option value="Cuanza Sul">Cuanza Sul</option>-->
                                <!--    <option value="Huambo">Huambo</option>-->
                                <!--    <option value="Huila">Huila</option>-->
                                <!--    <option value="Luanda">Luanda</option>-->
                                <!--    <option value="Lunda Norte">Lunda Norte</option>-->
                                <!--    <option value="Lunda Sul">Lunda Sul</option>-->
                                <!--    <option value="Malange">Malange</option>-->
                                <!--    <option value="Moxico">Moxico</option>-->
                                <!--    <option value="Namibe">Namibe</option>-->
                                <!--    <option value="Uige">Uige</option>-->
                                <!--    <option value="Zaire">Zaire</option>-->
                                <!--    <option value="Bié">Bié</option>-->
                                <!--</select>-->
                                 <select autocomplete="provincia" class="form-control" name="provincia" type="text" id="provincia">
                                <option value="">Nenhum selecionado</option>
                                @foreach ($provinces as $province)
                                    <option data-parameter="{{$province->parameters_id}}" value="{{$province->display_name}}"  @if($institution->provincia == $province->display_name) selected @endif>
                                        {{ $province->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group col">
                                <label for="municipio">Município</label>
                                <!--<select autocomplete="municipio" class="form-control" name="municipio" type="text"-->
                                <!--    id="municipio" class="form-control form-control-sm">-->
                                <!--    <option value="{{ $institution->municipio }}">{{ $institution->municipio }}</option>-->
                                <!--    <option value="Benguela">Benguela</option>-->
                                <!--    <option value="Catumbela">Catumbela</option>-->
                                <!--    <option value="Lobito">Lobito</option>-->
                                <!--    <option value="Luanda">Luanda</option>-->
                                <!--    <option value="Soyo">Soyo</option>-->
                                <!--</select>-->
                                <select autocomplete="municipio" class="form-control" name="municipio" type="text" id="municipio">
                                <option value="" disabled>Nenhum selecionado</option>
                                @foreach ($municipios as $municipio)
                                    <option  @if($institution->municipio == $municipio->display_name) selected @endif>
                                        {{ $municipio->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group col">
                                <label for="contribuinte">Contribuinte</label>
                                <input type="text" class="form-control"
                                    placeholder="Nº de Contribuinte da Instituição" autocomplete="contribuinte"
                                    value="{{ $institution->contribuinte }}" name="contribuinte" id="contribuinte">
                            </div>
                            <div class="form-group col">
                                <label for="capital_social">Capital social</label>
                                <input type="number" class="form-control" placeholder="Capital social da Instituição"
                                    autocomplete="capital_social" name="capital_social"
                                    value="{{ $institution->capital_social }}" id="capital_social">
                            </div>
                            <div class="form-group col">
                                <label for="registro_comercial_n">Registro comercial nº</label>
                                <input type="text" class="form-control" placeholder="Número do registro comercial"
                                    autocomplete="registro_comercial_n" name="registro_comercial_n"
                                    value="{{ $institution->registro_comercial_n }}" id="registro_comercial_n">
                            </div>

                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="logotipo">Logo</label>
                                <div class="custom-file-upload">
                                    <div class="file-upload-wrapper">
                                        <img class="user-profile-image" style="width:100px;"
                                            src="@php echo "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo; @endphp">
                                        <input type="file" class="attachment custom-file-upload-hidden" id="logotipo"
                                            name="logotipo" value="{{ $institution->logotipo }}" tabindex="-1"
                                            style="position: absolute; left: -9999px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="decreto_instituicao">Decreto da Instituição</label>
                                <input type="text" class="form-control" placeholder="Decreto da instituição"
                                    required="" autocomplete="decreto_instituicao" name="decreto_instituicao"
                                    value="{{ $institution->decreto_instituicao }}" id="decreto_instituicao">
                                <div class="custom-file-upload">
                                    <div class="file-upload-wrapper">
                                        <!--<a href="//forlearn.ispm.ao/storage/{{ $institution->instituicao_arquivo }}" target="black">{{ $institution->instituicao_arquivo }}</a>-->
                                        <input type="file" class="attachment custom-file-upload-hidden"
                                            id="instituicao_arquivo" name="instituicao_arquivo"
                                            value="{{ $institution->instituicao_arquivo }}" tabindex="-1"
                                            style="position: absolute; left: -9999px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="decreto_cursos">Decreto Cursos</label>
                                <input type="text" class="form-control" placeholder="Decreto do Curso" required=""
                                    autocomplete="decreto_cursos" name="decreto_cursos"
                                    value="{{ $institution->decreto_cursos }}" id="decreto_cursos">
                                <div class="custom-file-upload">
                                    <div class="file-upload-wrapper">
                                        <!--<a href="//forlearn.ispm.ao/storage/{{ $institution->cursos_arquivo }}" target="black">{{ $institution->cursos_arquivo }}</a>-->
                                        <input type="file" class="attachment custom-file-upload-hidden"
                                            id="cursos_arquivo" name="cursos_arquivo"
                                            value="{{ $institution->cursos_arquivo }}" tabindex="-1"
                                            style="position: absolute; left: -9999px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="dominio_internet">Domínio de internet</label>
                                <input type="text" class="form-control" placeholder="Domínio de Internet"
                                    autocomplete="dominio_internet" name="dominio_internet"
                                    value="{{ $institution->dominio_internet }}" id="dominio_internet">
                            </div>
                            <div class="form-group col">
                                <label for="registro_comercial_de">Conservatória do registro comercial</label>
                                <select autocomplete="registro_comercial_de" name="registro_comercial_de" type="text"
                                    id="registro_comercial_de" class="form-control">
                                    <option value="{{ $institution->registro_comercial_de }}">
                                        {{ $institution->registro_comercial_de }}</option>
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
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="telefone_geral">Telefone geral</label>
                                <input type="text" class="form-control" placeholder="Telefone geral"
                                    autocomplete="telefone_geral" name="telefone_geral"
                                    value="{{ $institution->telefone_geral }}" id="telefone_geral">
                            </div>
                            <div class="form-group col">
                                <label for="telemovel_geral">Telemóvel geral</label>
                                <input type="text" class="form-control" placeholder="Telemóvel geral"
                                    autocomplete="telemovel_geral" name="telemovel_geral"
                                    value="{{ $institution->telemovel_geral }}" id="telemovel_geral">
                            </div>
                            <div class="form-group col">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" placeholder="E-mail da Instituição"
                                    autocomplete="email" name="email" value="{{ $institution->email }}"
                                    id="email">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <div class="form-group">
                                    <label for="whatsapp">Whatssap</label>
                                    <input type="text" class="form-control" placeholder="Whatssap"
                                        autocomplete="whatsapp" name="whatsapp" value="{{ $institution->whatsapp }}"
                                        id="whatsapp">
                                </div>
                                <div class="form-group">
                                    <label for="facebook">Facebook</label>
                                    <input type="text" class="form-control" placeholder="Facebook"
                                        autocomplete="facebook" name="facebook" value="{{ $institution->facebook }}"
                                        id="facebook">
                                </div>
                                <div class="form-group">
                                    <label for="instagram">Instagram</label>
                                    <input type="text" class="form-control" placeholder="Instagram"
                                        autocomplete="instagram" name="instagram" value="{{ $institution->instagram }}"
                                        id="instagram">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="director_geral">Presidente</label>
                                <select data-live-search="true" class="selectpicker form-control form-control-sm" 
                                    id="director_geral" data-actions-box="false" data-selected-text-format="values"
                                    name="director_geral">
                                    @foreach ($institution_cargos[0] as $dir_geral)
                                        @if ($institution->director_geral == $dir_geral->users_id)
                                            <option selected value="{{ $dir_geral->users_id }}">{{ $dir_geral->value }} -
                                                {{ $dir_geral->email }}</option>
                                        @else
                                            <option value="{{ $dir_geral->users_id }}">{{ $dir_geral->value }} -
                                                {{ $dir_geral->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="vice_director_academica">Vice-director(a) área académica</label>
                                <select data-live-search="true" class="selectpicker form-control form-control-sm" 
                                    id="vice_director_academica" data-actions-box="false"
                                    data-selected-text-format="values" name="vice_director_academica">
                                    @foreach ($institution_cargos[1] as $vd_acad)
                                        @if ($institution->vice_director_academica == $vd_acad->users_id)
                                            <option selected value="{{ $vd_acad->users_id }}">{{ $vd_acad->value }} -
                                                {{ $vd_acad->email }}</option>
                                        @else
                                            <option value="{{ $vd_acad->users_id }}">{{ $vd_acad->value }} -
                                                {{ $vd_acad->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="vice_director_cientifica">Vice-director(a) área científica</label>
                                <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                    id="vice_director_cientifica" data-actions-box="false"
                                    data-selected-text-format="values" name="vice_director_cientifica">
                                    @foreach ($institution_cargos[2] as $vd_cient)
                                        @if ($institution->vice_director_cientifica == $vd_cient->users_id)
                                            <option selected value="{{ $vd_cient->users_id }}">{{ $vd_cient->value }} -
                                                {{ $vd_cient->email }}</option>
                                        @else
                                            <option value="{{ $vd_cient->users_id }}">{{ $vd_cient->value }} -
                                                {{ $vd_cient->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <div class="form-group col">
                                    <label for="daac">DAAC</label>
                                    <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                        id="daac" data-actions-box="false"
                                        data-selected-text-format="values" name="daac">
                                        @foreach ($institution_cargos[3] as $daac)
                                            @if ($institution->daac == $daac->users_id)
                                                <option selected value="{{ $daac->users_id }}">{{ $daac->value }} -
                                                    {{ $daac->email }}</option>
                                            @else
                                                <option value="{{ $daac->users_id }}">{{ $daac->value }} -
                                                    {{ $daac->email }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col">
                                    <label for="gabinete_termos">Gabinete de termos</label>
                                    <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                        id="gabinete_termos" data-actions-box="false"
                                        data-selected-text-format="values" name="gabinete_termos">
                                        @foreach ($institution_cargos[4] as $gab_ter)
                                            @if ($institution->gabinete_termos == $gab_ter->users_id)
                                                <option selected value="{{ $gab_ter->users_id }}">{{ $gab_ter->value }} -
                                                    {{ $gab_ter->email }}</option>
                                            @else
                                                <option value="{{ $gab_ter->users_id }}">{{ $gab_ter->value }} -
                                                    {{ $gab_ter->email }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col">
                                    <label for="secretaria_academica">Secretaria académica</label>
                                    <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                        id="secretaria_academica" data-actions-box="false"
                                        data-selected-text-format="values" name="secretaria_academica">
                                        @foreach ($institution_cargos[5] as $sec_acad)
                                            @if ($institution->secretaria_academica == $sec_acad->users_id)
                                                <option selected value="{{ $sec_acad->users_id }}">{{ $sec_acad->value }}
                                                    - {{ $sec_acad->email }}</option>
                                            @else
                                                <option value="{{ $sec_acad->users_id }}">{{ $sec_acad->value }} -
                                                    {{ $sec_acad->email }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="direcao" role="tabpanel" aria-labelledby="direcao-tab">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="director_executivo">Director(a) executivo</label>
                                <select data-live-search="true" class="selectpicker form-control form-control-sm"
                                    id="director_executivo" data-actions-box="false" data-selected-text-format="values"
                                    name="director_executivo">
                                    @foreach ($institution_cargos[6] as $director_executivo)
                                        @if ($institution->director_executivo == $director_executivo->users_id)
                                            <option selected value="{{ $director_executivo->users_id }}">
                                                {{ $director_executivo->value }} - {{ $director_executivo->email }}
                                            </option>
                                        @else
                                            <option value="{{ $director_executivo->users_id }}">
                                                {{ $director_executivo->value }} - {{ $director_executivo->email }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="recursos_humanos">Recursos Humanos</label>
                                <select data-live-search="true" class="selectpicker form-control form-control-sm" 
                                    id="recursos_humanos" data-actions-box="false" data-selected-text-format="values"
                                    name="recursos_humanos">
                                    @foreach ($institution_cargos[7] as $rh)
                                        @if ($institution->recursos_humanos == $rh->users_id)
                                            <option selected value="{{ $rh->users_id }}">{{ $rh->value }} -
                                                {{ $rh->email }}</option>
                                        @else
                                            <option value="{{ $rh->users_id }}">{{ $rh->value }} -
                                                {{ $rh->email }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="propriet" role="tabpanel" aria-labelledby="propriet-tab">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="nome_dono">Nome do propriétário</label>
                                <input type="text" class="form-control" placeholder="Nome do Propriétário"
                                    autocomplete="nome_dono" name="nome_dono" value="{{ $institution->nome_dono }}"
                                    id="nome_dono">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label for="nif">NIF do propriétário</label>
                                <input type="text" class="form-control" placeholder="NIF do Propriétário"
                                    autocomplete="nif" name="nif" value="{{ $institution->nif }}" id="nif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script src="{{ asset('js/new_tabpane_form.js')}} "></script>
    <script>
        (() => {

            // const btnSave = document.querySelector("#btn-salvar");
            // const btnEditar = document.querySelector("#btn-editar");
            // const inputLogo = document.querySelector("#logotipo-panel");
            const selectProvince = document.querySelector("#provincia");
            const selectMunicipio = document.querySelector("#municipio");
            // const formControl = document.querySelectorAll('.form-control');

            // function configuration() {
            //     if (!btnSave.classList.contains('d-none')) btnSave.classList.add('d-none');
            //     if (!inputLogo.classList.contains('d-none')) inputLogo.classList.add('d-none');
                
            //     formControl.forEach((item) => {
            //         if (!item.hasAttribute('disabled')) item.setAttribute('disabled', true);
            //     });
            // }
            
            function buildOptionOfMunicipal(){
                const value = selectProvince.value;
                const option = selectProvince.querySelector(`option[value="${value}"]`);
                const parameter = option.dataset.parameter;
                
                if(!parameter) return;
                
                let municipal = "";
                const inputMunicipal = document.querySelector('#municipio-def');
                
                if(inputMunicipal) municipal = inputMunicipal.value;
                
                $.ajax({
                    url: "{{route('institution.mun')}}?parameter="+parameter,
                    success: function(data) {
                        let html = "";
                        data.forEach(item => {
                            html += `<option value='${item.display_name}' ${item.display_name == municipal ? 'selected' : ''}>
                                        ${item.display_name}
                                    </option>`
                        });
                        if(html != "") selectMunicipio.innerHTML = html;
                    }
                })                
            }
            
            // configuration();
            
            buildOptionOfMunicipal();
            
            selectProvince.addEventListener('change', (e) => {
                buildOptionOfMunicipal();
            })

            // btnEditar.addEventListener('click', (e) => {

            //     if (!btnEditar.classList.contains('d-none')) btnEditar.classList.add('d-none');

            //     if (btnSave.classList.contains('d-none')) btnSave.classList.remove('d-none');
            //     if (inputLogo.classList.contains('d-none')) inputLogo.classList.remove('d-none');

            //     formControl.forEach((item) => {
            //         if (item.hasAttribute('disabled')) item.removeAttribute('disabled');
            //         if (item.hasAttribute('readonly')) item.removeAttribute('readonly');
            //     });
            // });

        })();
    </script>
@endsection
