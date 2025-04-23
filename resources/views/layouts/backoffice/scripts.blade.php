@section('scripts')

    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ asset('js/backoffice/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/backoffice/dataTables.conditionalPaging.js') }}"></script>
    <script src="{{ asset('lang/bootstrap-select/' . App::getLocale() . '.js') }}"></script>

    {!! Toastr::message() !!}

@show
