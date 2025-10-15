@section('title', __('GA::study-plans.study_plans'))


@section('styles')
    @parent
@endsection

@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('GA::study-plans.study_plans')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('study-plans') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            

    
        </div>


    @endsection

    @section('scripts')
        @parent
        <script></script>
    @endsection
