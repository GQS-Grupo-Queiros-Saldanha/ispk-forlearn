@php $i=0; @endphp
@php $array=[]; @endphp
@foreach ($modelo as $artcle)
    @foreach ($artcle as $item)
        @php $i++; @endphp
        <tr>
            <th scope="row">{{ $i }}</th>
            <td>
                {{ $item->article_name }}
                @if ($item->discipline_id != null)
                    - ( {{ $item->discipline_name }} - [ {{ $item->codigo_disciplina }} ] )
                @endif
                @if ($item->article_month == 1)
                    (Janeiro {{ $item->article_year }})
                @elseif($item->article_month == 2)
                    ( Fevereiro {{ $item->article_year }} )
                @elseif ($item->article_month == 3)
                    ( Março {{ $item->article_year }} )
                @elseif ($item->article_month == 4)
                    ( Abril {{ $item->article_year }} )
                @elseif ($item->article_month == 5)
                    ( Maio {{ $item->article_year }} )
                @elseif ($item->article_month == 6)
                    ( Junho {{ $item->article_year }} )
                @elseif ($item->article_month == 7)
                    ( Julho {{ $item->article_year }} )
                @elseif ($item->article_month == 8)
                    ( Agosto {{ $item->article_year }} )
                @elseif ($item->article_month == 9)
                    ( Setembro {{ $item->article_year }} )
                @elseif ($item->article_month == 10)
                    ( Outubro {{ $item->article_year }} )
                @elseif ($item->article_month == 11)
                    ( Novembro {{ $item->article_year }} )
                @elseif ($item->article_month == 12)
                    ( Dezembro {{ $item->article_year }} )
                @endif
            </td>
            <td>{{ $item->nome_creador }}</td>
            <td>{{ $item->updated_at }}</td>
            @if (empty($array))
                @php $array []= $item->code_recibo @endphp
                <td>{{ $item->code_recibo }}</td>
                <td><a class="btn btn-info btn-sm"
                        href="https://ispk.forlearn.ao/pt/payments/view-file/receipts/{{ $item->transaction_id }}"
                        target="_blank"> <i class="fas fa-receipt">
                            <p
                                style="font-size: 1.1pc; color:#ffa500 ; position: relative; z-index: 999;margin-top: -17px">
                                X</p>
                        </i></a></td>
            @elseif(in_array($item->code_recibo, $array))
            @else
                @php $array []= $item->code_recibo @endphp
                <td>{{ $item->code_recibo }}</td>
                <td><a class="btn btn-info btn-sm"
                        href="https://ispk.forlearn.ao/pt/payments/view-file/receipts/{{ $item->transaction_id }}"
                        target="_blank"> <i class="fas fa-receipt">
                            <p
                                style="font-size: 1.1pc; color:#ffa500 ; position: relative; z-index: 999;margin-top: -17px">
                                X</p>
                        </i></a></td>
            @endif
        </tr>
    @endforeach
@endforeach
<script>
    function generateReceiptForTransaction(id) {
        console.log(id);
        var myNewTab = window.open('about:blank', '_blank');
        let route = '{{ route('transactions.receipt', 0) }}'.slice(0, -1) + id
        $.ajax({
            method: "GET",
            url: route
        }).done(function(url) {
            console.log(url);
            myNewTab.location.href = url;
        });
    }
</script>
