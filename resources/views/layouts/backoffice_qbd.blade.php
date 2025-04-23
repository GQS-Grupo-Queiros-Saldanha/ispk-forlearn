<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    @include('layouts.backoffice.head')
    @include('layouts.backoffice.styles')
   
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<body class="hold-transition sidebar-mini">
@auth
    @include('layouts.backoffice.background')

    <div class="wrapper">
        @include('layouts.backoffice.header')

        <div class="content-wrapper">

            @section('content')
            @show
        </div>

        @include('layouts.backoffice.footer')
    </div>
@endauth

@guest
    <div class="wrapper">
        @include('layouts.backoffice.header')
        @section('guest-content')
        @show
    </div>
@endguest

@include('layouts.backoffice.scripts_qbd')
@include('layouts.backoffice.footer')

</body>
</html>
