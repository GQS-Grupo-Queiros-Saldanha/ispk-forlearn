@extends('layouts.backoffice_new')
@section('styles')
    @parent
    @yield("styles-new")
@endsection
@section('title')
    @parent
@endsection
@section('content')

    <div class="content-panel" style="padding: 0">
        @yield('navbar')
        <div class="content-header">
            <div class="container-fluid">
                <br>
                <div class="row">
                    <div class="col-md-6">
                        @isset($painelTitleDiv)
                            @yield('page-title')
                        @else
                            <h1 class="fs-3">@yield('page-title')</h1>
                        @endisset
                    </div>
                    <div class="col-md-6">
                        <ol class="breadcrumb float-right mr-3 @isset($breadcrumbCls) {{$breadcrumbCls}} @endisset " style="margin-top: 20px;">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                @yield("form-start")
                <div class="row">
                    <div class="col-md-6" style=" margin-top: -10px;">
                        <div class="d-grid gap-2 d-md-block ml-3">
                           @yield("buttons")
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-left: 63%; width: 35%; margin-right: 1%; margin-bottom: 10px; margin-top: -30px;">
                           @yield("selects")
                        </div>
                    </div>
                </div>
                <div class="row" style=" margin-top: -20px;">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                @yield("body")
                            </div>
                        </div>
                    </div>
                </div>
                @yield("form-end")
            </div>
        </div>
    </div>

    <div class="d-none" style="display: none" hidden>
        @yield('hiddens')
    </div>
    
    @yield('models')
@endsection
@section('scripts')
    @parent
    @yield('scripts-new')
@endsection
