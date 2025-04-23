<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    @include('layouts.backoffice.head')
    @include('layouts.print.style_pauta')
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<body class="bg-white">

    @section('content')
    @show

@include('layouts.print.script_pauta')
</body>
</html>
