@php
    use Carbon\Carbon;
    
    // Configurações
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    $currentDate = Carbon::now()->format('d/m/Y');
    
    // Processar nome da instituição
    $institutionName = isset($institution->nome) ? mb_strtoupper($institution->nome, 'UTF-8') : 'Nome da instituição não encontrado';
    $institutionNameParts = explode(' ', $institutionName);
@endphp

<div class="document-header" style="background-image: url('{{ $logotipo }}')">
    <table class="table m-0 p-1 bg-primary" style="border: none; margin-top: -15px; margin-right: 0;">
        <tr>
            <td class="logo-container" rowspan="12" style="background-image: url('{{ $logotipo }}')"></td>
            <td style="width: 200px; height: 78px; border: none;" rowspan="3"></td>
        </tr>
        
        <tr>
            <td class="bg-primary text-center" style="border: none; padding-top: 20px;">
                <h1 class="document-title pp1-highlight">
                    <b>PP1</b>
                </h1>
            </td>
        </tr>
        
        <tr>
            <td class="bg-primary text-right pr-2" style="border: none;">
                <h2 class="document-title">
                    <b>{{ $doc_name ?? 'Documento' }}</b>
                </h2>
            </td>
        </tr>
        
        <tr>
            <td class="text-right pr-2" style="border: none; color: white;">
                <span class="document-date">
                    Documento gerado a <b>{{ $currentDate }}</b>
                </span>
            </td>
        </tr>
    </table>

    <div class="institution-name">
        <h4><b>
            @foreach($institutionNameParts as $key => $part)
                @if($key == 1)
                    {{ $part }}<br>
                @else
                    {{ $part }} 
                @endif
            @endforeach
        </b></h4>
    </div>
</div>

<!-- Exemplo de uso das classes CSS para conteúdo adicional -->
<div class="table-main p-1">
    <table class="te-table">
        <thead>
            <tr class="bg-primary">
                <th>Coluna 1</th>
                <th>Coluna 2</th>
                <th class="bg-accent">Coluna 3 (Destacada)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dado 1</td>
                <td>Dado 2</td>
                <td class="bg-accent-light text-highlight">Dado 3 destacado</td>
            </tr>
        </tbody>
    </table>
</div>