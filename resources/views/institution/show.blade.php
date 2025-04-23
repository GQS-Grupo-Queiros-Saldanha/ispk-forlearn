@extends('layouts.generic_index_new')
<title>Instituição de ensino | forLEARN® by GQS</title>
@section('page-title', 'Instituição de Ensino')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active">
        <a href="{{ route('institution.show') }}">Instituição</a>
    </li>
@endsection
@section('body')
    <section class="">
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
                @php
                    Session::forget('success');
                @endphp
            </div>
        @endif
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
            @if (auth()->user()->hasAnyPermission(['editar_instituição']))
                <li class="nav-item" role="presentation">
                    <a href="{{ route('institution.edit') }}" class="btn btn-warning">
                        <i class="fas fa-plus-square"></i>
                        Editar formulário
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('institution.index') }}" class="btn btn-primary" target="black">
                        <i class="fas fa-plus-square"></i>
                        Gerar documento .pdf
                    </a>
                </li>
            @endif
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" name="nome" value="{{ $institution->nome }}"
                                readonly id="nome">
                        </div>
                        <div class="form-group col">
                            <label for="abrev">Abreviatura</label>
                            <input type="text" class="form-control" name="abrev" value="{{ $institution->abrev ?? '' }}"
                                id="abrev">
                        </div>                        
                        <div class="form-group col">
                            <label for="morada">Morada</label>
                            <input type="text" class="form-control" placeholder="Morada da Instituição"
                                autocomplete="morada" name="morada" value="{{ $institution->morada }}" readonly
                                id="morada">
                        </div>
                        <div class="form-group col">
                            <label for="provincia">Província</label>
                            <select autocomplete="provincia" class="form-control" name="provincia" type="text"
                                id="provincia" class="form-control form-control-sm" disabled>
                                <option value="{{ $institution->provincia }}" selected disabled>
                                    {{ $institution->provincia }}
                                </option>
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
                            <select autocomplete="municipio" class="form-control" name="municipio" type="text"
                                id="municipio" class="form-control form-control-sm" disabled>
                                <option value="{{ $institution->municipio }}" selected disabled>
                                    {{ $institution->municipio }}
                                </option>
                                <option value="Belas">Belas</option>
                                <option value="Cacuaco">Cacuaco</option>
                                <option value="Cazenga">Cazenga</option>
                                <option value="Icolo e Bengo">Icolo e Bengo</option>
                                <option value="Luanda">Luanda</option>
                                <option value="Quissama">Quissama</option>
                                <option value="Viana">Viana</option>
                            </select>
                        </div>
                        <div class="form-group col">
                            <label for="contribuinte">Contribuinte</label>
                            <input type="text" class="form-control" placeholder="Nº de Contribuinte da Instituição"
                                required="" autocomplete="contribuinte" value="{{ $institution->contribuinte }}"
                                readonly name="contribuinte" id="contribuinte">
                        </div>
                        <div class="form-group col">
                            <label for="capital_social">Capital social</label>
                            <input type="number" class="form-control" placeholder="Capital social da Instituição"
                                autocomplete="capital_social" name="capital_social"
                                value="{{ $institution->capital_social }}" readonly id="capital_social">
                        </div>
                        <div class="form-group col">
                            <label for="registro_comercial_n">Registro comercial nº</label>
                            <input type="text" class="form-control" placeholder="Número do registro comercial"
                                required="" autocomplete="registro_comercial_n" name="registro_comercial_n"
                                value="{{ $institution->registro_comercial_n }}" readonly id="registro_comercial_n">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="logotipo">Logo</label>
                            <div class="custom-file-upload">
                                <div class="file-upload-wrapper">
                                    <img class="user-profile-image" style="width:100px;"
                                        src="@php echo "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo; @endphp">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="decreto_instituicao">Decreto da Instituição</label>
                            <input type="text" class="form-control" placeholder="Decreto da instituição"
                                required="" autocomplete="decreto_instituicao" name="decreto_instituicao"
                                value="{{ $institution->decreto_instituicao }}" readonly id="decreto_instituicao">
                            <div class="custom-file-upload">
                                <div class="file-upload-wrapper">
                                    <!--<a href="//forlearn.ispm.ao/storage/{{ $institution->instituicao_arquivo }}" target="black">{{ $institution->instituicao_arquivo }}</a>-->
                                    <!--
                                                                                <input type="file"  required="" class="attachment custom-file-upload-hidden" id="instituicao_arquivo" name="instituicao_arquivo" value="{{ $institution->instituicao_arquivo }}" tabindex="-1" style="position: absolute; left: -9999px;">
                                                                                -->
                                    <a href="//forlearn.ispm.ao/instituicao-arquivo/{{ $institution->instituicao_arquivo }}"
                                        target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="decreto_instituicao">Decreto do Curso</label>
                            <input type="text" class="form-control" placeholder="Decreto do Curso" required=""
                                autocomplete="decreto_cursos" name="decreto_cursos"
                                value="{{ $institution->decreto_cursos }}" readonly id="decreto_cursos">
                            <div class="custom-file-upload">
                                <div class="file-upload-wrapper">
                                    <!--<a href="//forlearn.ispm.ao/storage/{{ $institution->cursos_arquivo }}" target="black">{{ $institution->cursos_arquivo }}</a>-->
                                    <!--
                                                                                <input type="file"  required="" class="attachment custom-file-upload-hidden" id="cursos_arquivo" name="cursos_arquivo" value="{{ $institution->cursos_arquivo }}" tabindex="-1" style="position: absolute; left: -9999px;">
                                                                                -->
                                    <a href="//forlearn.ispm.ao/instituicao-arquivo/{{ $institution->cursos_arquivo }}"
                                        target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="registro_comercial_de">Conservatória do registro comercial</label>
                            <select autocomplete="registro_comercial_de" class="form-control" name="registro_comercial_de" type="text"
                                id="registro_comercial_de" class="form-control form-control-sm">
                                <option value="{{ $institution->registro_comercial_de }}" selected disabled>
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
                        <div class="form-group col">
                            <label for="dominio_internet">Domínio de internet</label>
                            <input type="text" class="form-control" placeholder="Domínio de Internet" required=""
                                autocomplete="dominio_internet" name="dominio_internet"
                                value="{{ $institution->dominio_internet }}" readonly id="dominio_internet">
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
                                value="{{ $institution->telefone_geral }}" readonly id="telefone_geral">
                        </div>
                        <div class="form-group col">
                            <label for="telemovel_geral">Telemóvel geral</label>
                            <input type="text" class="form-control" placeholder="Telemóvel geral"
                                autocomplete="telemovel_geral" name="telemovel_geral"
                                value="{{ $institution->telemovel_geral }}" readonly id="telemovel_geral">
                        </div>
                        <div class="form-group col">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" placeholder="E-mail da Instituição"
                                autocomplete="email" name="email" value="{{ $institution->email }}" readonly
                                id="email">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <div class="form-group col">
                                <label for="whatsapp">Whatssap</label>
                                <input type="text" class="form-control" placeholder="Whatssap" required=""
                                    autocomplete="whatsapp" name="whatsapp" value="{{ $institution->whatsapp }}"
                                    readonly id="whatsapp">
                            </div>
                            <div class="form-group col">
                                <label for="facebook">Facebook</label>
                                <input type="text" class="form-control" placeholder="Facebook" required=""
                                    autocomplete="facebook" name="facebook" value="{{ $institution->facebook }}"
                                    readonly id="facebook">
                            </div>
                            <div class="form-group col">
                                <label for="instagram">Instagram</label>
                                <input type="text" class="form-control" placeholder="Instagram" required=""
                                    autocomplete="instagram" name="instagram" value="{{ $institution->instagram }}"
                                    readonly id="instagram">
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
                            @foreach ($institution_cargos[0] as $dir_geral)
                                @if ($institution->director_geral == $dir_geral->users_id)
                                    <input type="text" class="form-control" placeholder="director" required=""
                                        autocomplete="director" name="director"
                                        value="{{ $dir_geral->value }} - {{ $dir_geral->email }}" readonly
                                        id="director">
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group col">
                            <label for="vice_director_academica">Vice-director(a) área académica</label>
                            @foreach ($institution_cargos[1] as $vd_acad)
                                @if ($institution->vice_director_academica == $vd_acad->users_id)
                                    <input type="text" class="form-control" placeholder="director_academica"
                                        required="" autocomplete="director_academica" name="director"
                                        value="{{ $vd_acad->value }} - {{ $vd_acad->email }}" readonly
                                        id="director_academica">
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group col">
                            <label for="vice_director_cientifica">Vice-director(a) área científica</label>
                            @foreach ($institution_cargos[2] as $vd_cient)
                                @if ($institution->vice_director_cientifica == $vd_cient->users_id)
                                    <input type="text" class="form-control" placeholder="director_cientifica"
                                        required="" autocomplete="director_cientifica" name="director_cientifica"
                                        value="{{ $vd_cient->value }} - {{ $vd_cient->email }}" readonly
                                        id="director_cientifica">
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="daac">DAAC</label>
                            @foreach ($institution_cargos[3] as $daac)
                                @if ($institution->daac == $daac->users_id)
                                    <input type="text" class="form-control" placeholder="daac" required=""
                                        autocomplete="daac" name="daac"
                                        value="{{ $daac->value }} - {{ $daac->email }}" readonly id="daac">
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group col">
                            <label for="gabinete_termos">Gabinete de termos</label>
                            @foreach ($institution_cargos[4] as $gab_ter)
                                @if ($institution->gabinete_termos == $gab_ter->users_id)
                                    <input type="text" class="form-control" placeholder="gabinete" required=""
                                        autocomplete="gabinete" name="gabinete"
                                        value="{{ $gab_ter->value }} - {{ $gab_ter->email }}" readonly id="gabinete">
                                @endif
                            @endforeach
                        </div>
                        <div class="form-group col">
                            <label for="secretaria_academica">Secretaria académica</label>
                            @foreach ($institution_cargos[5] as $sec_acad)
                                @if ($institution->secretaria_academica == $sec_acad->users_id)
                                    <input type="text" class="form-control" placeholder="secretaria" required=""
                                        autocomplete="secretaria" name="secretaria"
                                        value="{{ $sec_acad->value }} - {{ $sec_acad->email }}" readonly
                                        id="secretaria">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="direcao" role="tabpanel" aria-labelledby="direcao-tab">
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="director_executivo">Director(a) executivo</label>
                            @foreach ($institution_cargos[6] as $director_executivo)
                                @if ($institution->director_executivo == $director_executivo->users_id)
                                    <input type="text" class="form-control" placeholder="director_executivo"
                                        required="" autocomplete="director_executivo" name="director_executivo"
                                        value="{{ $director_executivo->value }} - {{ $director_executivo->email }}"
                                        readonly id="director_executivo">
                                @endif
                            @endforeach

                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="recursos_humanos">Recursos Humanos</label>
                            @foreach ($institution_cargos[7] as $rh)
                                @if ($institution->recursos_humanos == $rh->users_id)
                                    <input type="text" class="form-control" placeholder="rh" required=""
                                        autocomplete="rh" name="rh"
                                        value="{{ $rh->value }} - {{ $rh->email }}" readonly id="rh">
                                @endif
                            @endforeach

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
                                autocomplete="nome_dono" name="nome_dono" value="{{ $institution->nome_dono }}" readonly
                                id="nome_dono">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col">
                            <label for="nif">NIF do propriétário</label>
                            <input type="text" class="form-control" placeholder="NIF do Propriétário"
                                autocomplete="nif" name="nif" value="{{ $institution->nif }}" readonly
                                id="nif">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
