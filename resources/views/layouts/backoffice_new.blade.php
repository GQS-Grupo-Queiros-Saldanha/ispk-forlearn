<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
    <meta charset="UTF-8"/>
    @include('layouts.backoffice.head')
    @include('layouts.backoffice.styles')
    @include('layouts.backoffice_new.head')
</head>
<body class="bg-light">
   
    @include('layouts.backoffice_new.navbar')
    
    <section class="wrapper-slc wrapper">
        <div class="wrapper content-wrapper-slc content-panel-slc">
            @section('content')
            @show
        </div>
    </section>
    
    <div class="modal" id="change-aluno-load" tabindex="-1" role="dialog" style="z-index: 9999999" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>
    
    @include('layouts.backoffice.scripts')
    @include('layouts.backoffice_new.body')
    
    <script>
         function showAndHideModal() {
              var modal = document.getElementById("change-aluno-load");
              modal.style.display = "block";
              setTimeout(function() {
                modal.style.display = "none";
              }, 2000);
        }
        showAndHideModal();
    </script>
    
    
</body>
</html>