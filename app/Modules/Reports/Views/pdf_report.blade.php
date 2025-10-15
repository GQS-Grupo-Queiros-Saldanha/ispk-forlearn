<!DOCTYPE html>
<html>
<head>
    <title>PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous"> 
</head>
<body>
<table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº de Ordem</th> 
                    <th>Nº Mecanografico </th>
                    <th>Nº Candidato </th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cargo</th>
                    <th>Data de Nascimento</th>
                    <th>Estado Civil</th>
                    <th>Sexo</th>
                    <th>Curso</th>
                    <th>Turma</th>
                    <th>Altura</th>
                    <th>Tipo de Sangue</th>
                    <th>Nº Bilhete Identidade</th>
                    <th>E-mail Pessoal</th>
                    <th>Nacionalidade</th>
                    <th>Bacharelato</th>
                    <th>Licenciatura</th>
                    <th>Mestrado</th>
                    <th>Doutoramento</th>
                    <th>Peso (kg)</th>
                    <th>Data de Validade do Bilhete de Identidade</th>
                    <th>Nº de Passaporte</th>
                    <th>Data de Validade do Passaporte</th>
                    <th>NIF</th>
                    <th>Nº de Segurança Social</th>
                    <th>Nº Atestado Médico</th>
                    <th>Nº do Registro Criminal</th>
                    <th>Nº do Ressenciamento Militar</th>
                    <th>Nº da Carta de Condução</th>
                    <th>Estudante Trabalhador</th>
                    <th>Necessidades Especiais</th>
                    <th>Data do Termo de Trabalho</th>
                    <th>Nº IBAN</th>
                    <th>Telefone Fixo</th>
                    <th>Telefone Principal</th>
                    <th>Telefone Alternativo</th>
                    <th>Email Alternativo</th>
                    <th>Whatsapp</th>
                    <th>Skype</th>
                    <th>Facebook</th>
                    <th>Provincia de Origem</th>
                    <th>Provinctia Actual</th>
                    <th>Atualizado Por </th>
                    <th>Criado Por</th>
                    {{--<th>Código da Disciplina </th>--}}
                    <th>Nº de Matrícula</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $item)
                        <tr>
                        <td></td>
                        <td>{{ $item->value_mecanografico }} </td>
                        <td> {{ $item->code}} </td>
                        <td>{{ $item->value_nome }}</td>
                        <td>{{ $item->user_email }}</td>
                        <td>{{ $item->role_name }}</td>
                        <td>{{ $item->value_nascimento }}</td>
                        <td>{{ $item->getcode_civil }}</td>
                        <td>{{ $item->getcode_sexo}} </td>
                        <td> {{ $item->course_name}}</td>
                        <td> {{ $item->turma_display_name}}</td>
                        <td>{{ $item->value_altura }}</td>
                        <td>{{ $item->getcode_sangue }}</td>
                        <td>{{ $item->value_bilhete }}</td>
                        <td>{{ $item->value_email }}</td>
                        <td>{{ $item->getcode_nacionalidade }}</td>
                        <td>{{ (substr($item->getcode_bacharelato, -3) == "sim") ? 'Sim':'Não' }}</td>
                        <td>{{ (substr($item->getcode_licenciatura, -3) == "sim") ? 'Sim':'Não' }}</td>
                        <td>{{ (substr($item->getcode_mestrado, -3) == "sim") ? 'Sim':'Não' }}</td>
                        <td>{{ (substr($item->getcode_doutoramento, -3) == "sim") ? 'Sim':'Não' }}</td>
                        <td>{{ $item->value_peso }}</td>
                        <td>{{ $item->value_validade_bilhete }}</td>
                        <td>{{ $item->value_passaporte }}</td>
                        <td>{{ $item->value_validade_passaporte }}</td>
                        <td>{{ $item->value_nif }}</td>
                        <td>{{ $item->value_segsocial }}</td>
                        <td>{{ $item->value_atestmedico }}</td>
                        <td>{{ $item->value_regcriminal }}</td>
                        <td>{{ $item->value_ressmilitar }}</td>
                        <td>{{ $item->value_cartaconducao }}</td>
                        <td>{{ $item->getcode_estudantetrabalhador }}</td>
                        <td>{{ $item->getcode_necespeciais }}</td>
                        <td>{{ $item->value_data_termo_trabalho }}</td>
                        <td>{{ $item->value_iban }}</td>
                        <td>{{ $item->value_teleffixo }}</td>
                        <td>{{ $item->value_telefprincipal }}</td>
                        <td>{{ $item->value_telefalternativo }}</td>
                        <td>{{ $item->value_emailalternativo }}</td>
                        <td>{{ $item->value_whatsapp }}</td>
                        <td>{{ $item->value_skype }}</td>
                        <td>{{ $item->value_facebook }}</td>
                        <td>{{ (substr($item->getcode_value_provincia_origem, -1) == "x") ? substr($item->getcode_value_provincia_origem, 0, -1) : $item->getcode_value_provincia_origem }}</td>
                        <td>{{ (substr($item->getcode_value_provincia_actual, -1) == "x") ? substr($item->getcode_value_provincia_actual, 0, -1) : $item->getcode_value_provincia_actual }}</td>
                        <td>{{ $item->updated_by}}</td>
                        <td>{{ $item->created_by}}</td>
                        {{--<td>{{ $item->d_c}}</td>--}}
                        <td>{{ $item->matricula_numb}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
</body>
</html>
