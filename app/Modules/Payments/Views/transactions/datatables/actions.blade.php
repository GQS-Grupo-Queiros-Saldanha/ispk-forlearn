@if($item->type !== 'debit')
    <a onclick="generateReceiptForTransaction({{$item->id}})" class="btn btn-info btn-sm">
        <i class="fas fa-receipt"></i>
    </a>

    <a onclick="sendReceiptByEmail({{$item->id}})" class="btn btn-warning btn-sm email_transaction_{{$item->id}}">
        <i class="fas fa-envelope email_transaction_icon_{{$item->id}}"></i>
    </a>

    <script>
        function generateReceiptForTransaction(id) {
            var myNewTab = window.open('about:blank', '_blank');
            let route = '{{ route('transactions.receipt', 0) }}'.slice(0, -1) + id
            $.ajax({
                method: "GET",
                url: route
            }).done(function (url) {
                myNewTab.location.href = url;
            });
        }

        function sendReceiptByEmail(id) {
            var button = $('.email_transaction_' + id)
            var icon = $('.email_transaction_icon_' + id);

            button.addClass('disabled')

            icon.removeClass('fa-envelope')
                .addClass('fa-sync fa-spin');

            let route = '{{ route('transactions.email.receipt', 0) }}'.slice(0, -1) + id
            $.ajax({
                method: "GET",
                url: route
            }).done(function (data) {
                if (data.sent) {
                    icon.removeClass('fa-sync fa-spin')
                        .addClass('fa-check');
                } else {
                    button.removeClass('disabled')

                    icon.removeClass('fa-sync fa-spin')
                        .addClass('fa-envelope');
                }
            });
        }
    </script>
@endif
