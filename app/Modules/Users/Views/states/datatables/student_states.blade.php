@php
    $data=[
        (object)[
        'code_dev'=>'anul_matric',
        'cor'=>'btn-danger',
        ''=>'Suspensão da matrícula'
        ],
        (object)[
        'code_dev'=>'confirm',
        'cor'=>'btn-success',
        'states'=>'Frequentar'
        ], (object)[
        'code_dev'=>'mudanca_curso',
        'cor'=>'btn-warning',
        'states'=>'Mudança de curso'
        ],
        (object)[
        'code_dev'=>'trabalho_fim_curso',
        'cor'=>'btn-success',
        'states'=>'Concluído'
        ],(object)[
        'code_dev'=>'exame',
        'cor'=>'btn-warning'
        ]
        ,(object)[
        'code_dev'=>'pedido_t_entrada',
        'cor'=>'btn-success',
        'states'=>'Em aprovação de transferência entrada'
        ]
        ,(object)[
        'code_dev'=>'pedido_t_saida',
        'cor'=>'btn-success',
        'states'=>'Em aprovação de transferência saida'
        ]
    ];
@endphp
@php $encotrou=false; @endphp
@foreach ($data as $item)
    @if ($item->code_dev==$student_state->code_dev)
        @php $encotrou=true; @endphp
        <P class="{{$item->cor}}" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
            {{ $student_state->studant_state }}
        </P>
    @endif
@endforeach

@if ($encotrou==false)
            {{--  A frequentar --}}

            @if ($student_state->studant_state == 'Frequentar')
            <P class="btn-success" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Finalista --}}

            @if ($student_state->studant_state == 'Finalista')
            <P class="btn-success" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
                    {{ $student_state->studant_state }}
                </P>
            @endif


            {{-- Falecido --}}

            @if ($student_state->studant_state == 'Falecido')
                <P style="width: max-content; padding: 5px;border-radius: 5px;color: #fff;
                background-color: #000000;
                border-color: #000000;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Concluido --}}

            @if ($student_state->studant_state == 'Concluído')
            <P class="btn-success" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Aguardar pagamento --}}

            @if ($student_state->studant_state == 'Aguardar pagamento')
            <P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Aguardar matrícula --}}

            @if ($student_state->studant_state == 'Aguardar matrícula')
                <P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Interrompido --}}

            @if ($student_state->studant_state == 'Interrompido' || $student_state->studant_state == 'Interropido')
            <P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Mudança de curso --}}

            @if ($student_state->studant_state == 'Pedido de Transferência' || $student_state->studant_state == 'Pedido de transferência')
            <P class="btn-danger" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif
            {{-- Prescrito --}}

            @if ($student_state->studant_state == 'Prescrito' || $student_state->studant_state == 'Prescrito')
            <P class="btn-danger" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Não inscrito--}}

            @if ($student_state->studant_state == 'Não inscrito' || $student_state->studant_state == 'Não inscrito')
            <P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Mudança de curso --}}

            @if ($student_state->studant_state == 'Mudança de curso' || $student_state->studant_state == 'Mudança de curso')
            <P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Suspensão de matrícula --}}

            @if ($student_state->studant_state == 'Suspensão da matrícula' || $student_state->studant_state == 'Suspensão da matrícula')
            <P class="btn-danger" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Inactivo --}}

            @if ($student_state->studant_state == 'Inactivo' || $student_state->studant_state == 'Inactivo')
                <P style="width: max-content; padding: 5px;border-radius: 5px;color: #fff;
                background-color: #383838;
                border-color: #383838;">
                    {{ $student_state->studant_state }}
                </P>
            @endif

            {{-- Em aprovação de transferência --}}

            @if ($student_state->studant_state == 'Em aprovação de transferência' || $student_state->studant_state == 'Em aprovação de transferência')
            <P class="btn-success" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
                    {{ $student_state->studant_state }}
                </P>
            @endif
@endif


















































{{-- @if ($student_state->studant_state == 'Falecido')
<P style="width: max-content; padding: 5px;border-radius: 5px;color: #fff;
background-color: #000000;
border-color: #000000;">
    {{ $student_state->studant_state }}
</P> --}}

{{-- @elseif ($student_state->studant_state == 'Finalista')
<P class="btn-success" style="width: max-content; padding: 5px;border-radius: 5px;color: white;">
    {{ $student_state->studant_state }}
</P> --}}


{{-- Aguardar pagamento --}}
{{-- @elseif ($student_state->studant_state == 'Aguardar pagamento')
<P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
    {{ $student_state->studant_state }}
</P> --}}


{{-- Aguardar matrícula --}}
{{-- @elseif ($student_state->studant_state == 'Aguardar matrícula')
<P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
    {{ $student_state->studant_state }}
</P> --}}


{{-- Interrompido --}}

{{-- @elseif ($student_state->studant_state == 'Interrompido' || $student_state->studant_state == 'Interropido')
<P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
    {{ $student_state->studant_state }}
</P> --}}


{{-- Prescrito --}}

{{-- @elseif ($student_state->studant_state == 'Prescrito' || $student_state->studant_state == 'Prescrito')
<P class="btn-danger" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
    {{ $student_state->studant_state }}
</P> --}}


{{-- Inactivo --}}

{{-- @elseif ($student_state->studant_state == 'Inactivo' || $student_state->studant_state == 'Inactivo')
<P style="width: max-content; padding: 5px;border-radius: 5px;color: #fff;
background-color: #383838;
border-color: #383838;">
    {{ $student_state->studant_state }}
</P> --}}
{{-- @elseif ($student_state->studant_state == null)
<P class="btn-warning" style="width: max-content; padding: 5px;border-radius: 5px;color: black;">
Aguardar Matrícula
</P>
@endif --}}