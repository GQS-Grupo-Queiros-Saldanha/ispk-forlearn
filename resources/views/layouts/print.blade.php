<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    @include('layouts.backoffice.head')
    @include('layouts.print.styles')
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<body class="bg-white">

    @section('content')
    @show

@include('layouts.print.scripts')
</body>
</html>

