<title>{{ 'Boletim_de_notas_'.$student_info->matricula .
    '_' .
    $student_info->lective_year }}
</title>
@extends('layouts.print')
@section('content')
@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
    $documentoCode_documento = 50;
    $doc_name = 'Boletim de notas';
    $discipline_code = '';
@endphp
<main>
    @include('Reports::pdf_model.forLEARN_header')
    <!-- aqui termina o cabeçalho do pdf -->
    <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te">

                        <tr class="bg1">
                            <th class="text-center">Estudante</th>
                            <th class="text-center">Matrícula</th>
                            <th class="text-center">E-mail</th>
                            <th class="text-center">Turma</th>
                            <th class="text-center">Curso</th>
                            <th class="text-center">Ano Lectivo</th>
                        </tr>
                        
                        <tr class="bg2">
                            <td class="text-center bg2">{{ $student_info->full_name }}</td>
                            <td class="text-center bg2">{{ $student_info->matricula }}</td>
                            <td class="text-center bg2">{{ $student_info->email }}</td>
                            <td class="text-center bg2">{{ $student_info->classe }}</td>
                            <td class="text-center bg2">{{ $student_info->course }}</td>
                            <td class="text-center bg2">{{ $student_info->lective_year }}</td>
                        </tr>

                    </table>
                </div>
            </div>
            <!-- personalName -->
            <style>
                .tabela_pauta tbody td {
                    padding: 7px !important;
                    padding: 7px !important;
                    font-size: 14px!important;
                    min-width:20px!important;
                }

                .tabela_pauta thead th {
                    padding: 7px !important;
                    padding: 7px !important;
                    font-size: 12px!important;
                    min-width: 20px!important;
                } 
                .boletim_text{
                    font-size: 14px!important;
                }
                table tr .small,
                table tr .small {
                    font-size: 14px!important;
                }

                .for-red {
                    background-color: rgba(245, 52, 46, 0.761)!important; 
                   
                }
                .cf1{ 
                    background-color: rgba(72, 136, 255, 0.859)!important;
                   
                }
                .p-top{
                    padding-top: 5px!important;font-size: 13px!important;
                }
                
          
                .text-f{
                    font-weight:normal!important;font-size: 11px!important;
                }

            </style>
            
            
            <div class="row">
                <div class="col-12">
                    <div class="">
                       
                        @include('Cms::initial.components.boletim')

                        <br>
                        <br>

                        @include('Reports::pdf_model.signature')

                    </div>

                </div>
            </div>
            <style>
                 .bgmac{ 
                    background-color: rgba(72, 136, 255, 0.859) !important;
                }   
            </style>
        </div>
    </div>
    </div>
    </div>
</main>
@endsection

<script></script>
