<?php
use App\Modules\Cms\Controllers\mainController;
?>

<style> 
    .tabela_pauta tbody tr td { font-weight: normal !important; } 
    .tabela_pauta tbody tr .text-bold { font-weight: 600 !important; } 
    .bg0 { background-color: #2f5496 !important; color: white; } 
    .bg1 { background-color: #8eaadb !important; } 
    .bg2 { background-color: #d9e2f3 !important; } 
    .bg3 { background-color: #fbe4d5 !important; } 
    .bg4 { background-color: #f4b083 !important; } 
    .bgmac { background-color: #a5c4ff !important; } 
    .cf1 { background-color: #4888ffdb !important; } 
    .rec { background-color: #a5c4ff !important; } 
    .fn { background-color: #1296ff !important; } 
    .bo1 { border: 1px solid white!important; } 
    table tr .small, table tr .small { font-size: 11px !important; } 
    .for-green { background-color: #00ff89 !important; } 
    .for-blue { background-color: #cce5ff !important; z-index: 1000; } 
    .for-red { background-color: #f5342ec2 !important; } 
    .for-yellow { background-color: #f39c12 !important; } 
    .boletim_text { font-weight: normal !important; } 
    .barra { color: #f39c12 !important; font-weight: bold; } 
    .semestreA, .semestre2{ } 
</style>

@if(2+2 !=4 )
    <div class="alert alert-warning text-dark font-bold">
        Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar os seus pagamentos!
    </div>
@elseif (auth()->check() && auth()->user()->id != 529)
    @include('Cms::initial.components.manutencao')
@else
    @if(isset($disciplinas))
        <h1>ola</h1>
    @endif

@endif