<table>
    <thead>
        <tr>
            <th>Matricula</th>
            <th>{{$funcionario}}</th>
            <th>Emolumento</th>
            <th>Curso</th>
            <th>Ano</th>
            <th>Banco</th>
            <th>Referencia</th>
            <th>Data</th>
            <th>Utilizador</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @php $subtotal = 0; $ano=date('Y'); @endphp
        @php $total = 0; @endphp
          
        {{-- @foreach ($emoluments as $emolument)
         @php $subtotal = 0;  @endphp
        <tr>
            <td>{{ $emolument->matriculation_number }}</td>
            <td>{{ $emolument->user_name }}</td>
            <td>{{ $emolument->article_name }}</td>
            <td>{{ $emolument->course_name }}</td>
            <td>{{ $emolument->course_year }}</td>
            <td>{{ $emolument->bank_name }}</td>
            <td>{{ $emolument->reference }}</td>
            <td>{{ date('d-m-Y', strtotime($emolument->fulfilled_at)) }}</td>
            <td>{{ $emolument->created_by }}</td>
            <td style="text-align: right" class="td-parameter-column"> {{ number_format($emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia, 2, ".", ",") }} @php $subtotal += $emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia  ; $total += $emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia;  @endphp </td>
        </tr>
        @endforeach --}}
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ number_format($total, 2, ".", ",") }}</td>
        </tr>
    </tbody>
</table>