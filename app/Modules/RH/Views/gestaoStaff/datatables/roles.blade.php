

<style>
    .see-more{
        transition: all 0.5s;
    }
    .see-more:hover{
        cursor: pointer;
        background: rgb(255, 255, 255);
        font-size: 0.9pc;
        transition: all 0.5s;
        padding: 1.5px;
    }
</style>

@php $user_id = [];
    $cargos=[];
    $fun_sem_contrado=0;
    $fun_contrado=0;
    $pesquisaLastSalario=false;
@endphp


    @foreach ($getcontratos as $cargo) 
        @if ($cargo->user_id === $item->id_user)
                @if ($cargo->status_contrato== "ativo")
                    
                
                    @if(!in_array($cargo->cargo_id, $cargos))
                        <a class=".see-more"  href="recurso_rescisaoBaixando_arquivos/{{$cargo->contratoPDF}}" title="Contrato: {{$cargo->nome_cargo}}&#013;&#010;Estado: Activo&#013;&#010;Início: {{$cargo->inicio_contrato}}&#013;&#010;Fim: {{$cargo->fim_contrato}}&#013;&#010;Salário Base: {{number_format($cargo->salarioBase, 2, ',', '.') }}Kz&#013;&#010;Entrada em vigor: {{$cargo->dataSalario}}&#013;&#010;">                            
                            {{$cargo->nome_cargo}}
                        </a>
                        @php $cargos []= $cargo->cargo_id;@endphp
                    @endif
                        
                    @php $pesquisaLastSalario=true @endphp
                    @if (!in_array($item->id_user,$user_id))
                        @php $user_id []= $item->id_user;@endphp
                    @endif
                    
                
                @endif       
        @endif
    @endforeach

    @if(!in_array($item->id_user,$user_id)) 
        <h6 style="color: red">Sem contrato</h6>
    @endif
<script>
    
</script>