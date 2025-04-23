<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    @include('layouts.backoffice.head')
    {{-- @include('layouts.print.styleForSchedule') --}}
     @include('layouts.backoffice.styles')
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<body class="bg-white">

    @section('content')
    @show

    @include('layouts.backoffice.scripts')
</body>
</html>

