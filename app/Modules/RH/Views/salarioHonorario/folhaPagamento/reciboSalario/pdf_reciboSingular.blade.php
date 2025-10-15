@extends('layouts.print')
@section('content')
<style>
    .div-container{
        height: 100%;
    }
    .reciboSingular{
        border-right: 1.7px dashed black;
        /* width: 50%; */
        height: 60.2pc;
        padding-right: 7px;
        font-size: 0.9pc;
        /* background: rgb(252, 252, 252) */
    }
    .reciboSingular-copia{
        /* width: 50%; */
        height: 71.2pc;
        padding-left: 7px;
        font-size: 0.9pc;
        /* background: rgb(252, 252, 252) */
    }
</style>
    <div class="m-0 p-0 row div-container">
        <div class="reciboSingular">
            {{-- @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')
            <p class="text-doc-original">Original</p> --}}
            @include('RH::salarioHonorario.folhaPagamento.reciboSalario.reciboSalario_singular') 
            {{-- <div class="recibo-rodape">
                @include('Reports::pdf_model.pdf_footer')
            </div> --}}
        </div>
        <div class="reciboSingular-copia"> 
            {{-- @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')
            <p class="text-doc-original">Duplicado</p> --}}
            @include('RH::salarioHonorario.folhaPagamento.reciboSalario.reciboSalario_singular')
            {{-- <div class="recibo-rodape">
                @include('Reports::pdf_model.pdf_footer')
            </div> --}}
        </div>
    </div>
@endsection

    