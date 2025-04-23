<table class="table table-parameter-group">

    <thead class="thead-parameter-group">
    <th class="th-parameter-group bg1">DADOS PESSOAIS</th>
    </thead>

</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
    <thead>
        <tr class="bg2">
    <th style="font-size: 8pt; border: 0 !important;">
        Nome
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Nº Matrícula
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Bilhete de Identidade
    </th></tr>
    </thead>
    <tbody class="">
    <tr>
        <td width="25%" style="font-size: 8pt;">
            {{ $personal['name'] }}
        </td>
        <td width="25%" style="font-size: 8pt;">
           {{ $personal['n_mecanografico']}}
        </td>
        <td width="50%" style="font-size: 8pt;">
            {{ $personal['bi'] }}
        </td>
    </tr>
    </tbody>
</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
    <thead>
        <tr class="bg2">

       
    <th style="font-size: 8pt; border: 0 !important;">
        Telémovel
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Telémovel Alternativo
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Telefone
    </th>
    <th style="font-size: 8pt; border: 0 !important;"></th>
    </tr>
    </thead>
    <tbody class="">
    <tr>
        <td width="25%" style="font-size: 8pt;">
            {{ $personal['mobile_phone'] }}
        </td>
        <td width="25%" style="font-size: 8pt;">
            {{ $personal['mobile_phone_alt'] }}
        </td>
        <td width="25%" style="font-size: 8pt;">
            {{ $personal['phone'] }}
        </td>
        <td width="25%" style="font-size: 8pt;"></td>
    </tr>
    </tbody>
</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
    <thead>
        <tr class="bg2">
    <th style="font-size: 8pt; border: 0 !important;">
        Email
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Email Pessoal
    </th></tr>
    <tbody class="">
    <tr>
        <td width="50%" style="font-size: 8pt;">
            {{ $personal['email'] }}
        </td>
        <td width="50%" style="font-size: 8pt;">
            {{ $personal['email_2'] }}
        </td>
    </tr>
    </tbody>
</table>

<br>

<table class="table table-parameter-group">

    <thead class="thead-parameter-group">
    <th class="th-parameter-group bg1">DADOS CURRICULARES</th>
    </thead>

</table>

<table class="table table-parameter-group" width="100%" style="border: 0 !important;">
    <thead>
        <tr class="bg2">
    <th style="font-size: 8pt; border: 0 !important;">
        Curso
    </th>
    @isset($matriculation_lective_year->id)
    <th style="font-size: 8pt; border: 0 !important;">
        Ano lectivo
    </th>
    @endisset    
    <th style="font-size: 8pt; border: 0 !important;">
        Ano Curricular
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Código da Matrícula
    </th>
    <th style="font-size: 8pt; border: 0 !important;">
        Turno
    </th></tr>
    <tbody class="">
    <tr>
        <td width="25%" style="font-size: 8pt;">
            {{ $curricular['course'] }}
        </td>
        @isset($matriculation_lective_year->id)
        <th width="25%" style="font-size: 8pt;">
            {{$matriculation_lective_year->currentTranslation->display_name}}
        </th>
        @endisset
        <td width="25%" style="font-size: 8pt;">
            {{ $curricular['year'] }}
        </td>
          <td width="25%" style="font-size: 8pt;">
             {{ $matriculation_numb }}
        </td>
        @foreach($disciplines as $d)
         <td width="25%" style="font-size: 8pt;">
            @if($loop->first)
                @if( (substr($d['class'], -2, 1) == "M" ) )
                    Manhã
                @elseif((substr($d['class'], -2, 1) == "T" ))
                    Tarde
                @elseif((substr($d['class'], -2, 1) == "N" ))
                    Noite
            @endif
            @endif
         </td>
        @endforeach
    </tr>
    </tbody>
</table>

<br>

<table class="table table-parameter-group">

    <thead class="thead-parameter-group">
    <th class="th-parameter-group bg1">EXAME FINAL A DISCIPLINAS EM ATRASO</th>
    </thead>

</table>


<table class="table table-parameter-group" width="100%">
    <thead>
        <tr class="bg2">
        <th style="font-size: 8pt;">CÓDIGO</th>
    <th style="font-size: 8pt;">DISCIPLINA</th>
    <th style="font-size: 8pt;">REGIME</th>
    <th style="font-size: 8pt;">ANO</th>
    <th style="font-size: 8pt;">TURMA</th></tr>
    </thead>
    <tbody class="tbody-parameter-group">
    @foreach($disciplines_exam as $d)
        <tr>
        <td width="10%" style="font-size: 8pt;">{{ $d['code'] }}</td>
            <td width="50%" style="font-size: 8pt;">{{ $d['name'] }}</td>
            <td width="20%" style="font-size: 8pt;">{{ $d['regime'] }}</td>
            <td width="10%" style="font-size: 8pt;">{{ $d['year'] }}</td>
            <td width="20%" style="font-size: 8pt;">{{ $d['class'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<br>

<table class="table table-parameter-group">

    <thead class="thead-parameter-group">
    <th class="th-parameter-group bg1">FREQUÊNCIA A DISCIPLINAS EM ATRASO</th>
    </thead>

</table>


<table class="table table-parameter-group" width="100%">
    <thead>
        <tr class="bg2">
        <th style="font-size: 8pt;">CÓDIGO</th>
    <th style="font-size: 8pt;">DISCIPLINA</th>
    <th style="font-size: 8pt;">REGIME</th>
    <th style="font-size: 8pt;">ANO</th>
    <th style="font-size: 8pt;">TURMA</th></tr>
    </thead>
    <tbody class="tbody-parameter-group">
    @foreach($disciplines_late as $d)
        <tr>
        <td width="10%" style="font-size: 8pt;">{{ $d['code'] }}</td>
            <td width="50%" style="font-size: 8pt;">{{ $d['name'] }}</td>
            <td width="20%" style="font-size: 8pt;">{{ $d['regime'] }}</td>
            <td width="10%" style="font-size: 8pt;">{{ $d['year'] }}</td>
            <td width="20%" style="font-size: 8pt;">{{ $d['class'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<br>

<table class="table table-parameter-group">

    <thead class="thead-parameter-group">
    <th class="th-parameter-group bg1">FREQUÊNCIA A DISCIPLINAS DO ANO CURRICULAR</th>
    </thead>

</table>


<table class="table table-parameter-group" width="100%">
    <thead><tr class="bg2">
    <th style="font-size: 8pt;">CÓDIGO</th>
    <th style="font-size: 8pt;">DISCIPLINA</th>
    <th style="font-size: 8pt;">REGIME</th>
    <th style="font-size: 8pt;">ANO</th>
    <th style="font-size: 8pt;">TURMA</th></tr>
    </thead>
    <tbody class="tbody-parameter-group">
        @foreach($disciplines as $d)
            <tr>
            <td width="10%" style="font-size: 8pt;">{{ $d['code'] }}</td>
                <td width="50%" style="font-size: 8pt;">{{ $d['name'] }}</td>
                <td width="20%" style="font-size: 8pt;">{{ $d['regime'] }}</td>
                <td width="10%" style="font-size: 8pt;">{{ $d['year'] }}</td>
                <td width="20%" style="font-size: 8pt;">{{ $d['class'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>
<br>
<br>
<br>
<table class="table-borderless" style="margin-left:4px;">
    <thead style="text-align:left;">
   
    </thead>
    <tbody>
       
  
        <tr>
   
            <td style="font-size: "><b>O estudante</b><br><br>

                __________________________________________________________________<br>
                {{ $personal['name'] }}

                

            </td>

            <td style="color: white;">_____</td>
            

            <td style="font-size: "><b>Staff da IE</b><br><br>

                __________________________________________________________________<br>
                {{ $created_by }}

                

            </td>

        </tr>

    </tbody>
</table>
<div>

    
</div>
<br>
<br>
<br>
<br>
@include('Reports::pdf_model.signature')

<br><br>
