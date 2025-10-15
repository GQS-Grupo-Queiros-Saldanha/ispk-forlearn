@section('scripts')

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
           var table =  $('#example').DataTable({
               "bInfo" : false,
               "searching": false,
                order: [[ 2, 'asc' ], [ 0, 'asc' ]],
                paging: false
            });
            table.on('order.dt search.dt', function () {
            table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
             table.cell(cell).invalidate('dom');
            });
        }).draw();
        });
    </script>
    <script type="text/javascript">
        //window.onload = function() { window.print(); }
    </script>

    <script>
        var delayInMilliseconds = 3000; //1 second

        setTimeout(function() {
            window.print();
        }, delayInMilliseconds);

    </script>

    

    {{--<script>@php include_once public_path().'/js/manifest.js'; @endphp</script>
    <script>@php include_once public_path().'/js/vendor.js'; @endphp</script>
    <script>@php include_once public_path().'/js/app.js'; @endphp</script>--}}

    {{--<script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>--}}

{{--    <script src="{{ public_path('/js/manifest.js') }}"></script>--}}
{{--    <script src="{{ public_path('/js/vendor.js') }}"></script>--}}
{{--    <script src="{{ public_path('/js/app.js') }}"></script>--}}
@show
