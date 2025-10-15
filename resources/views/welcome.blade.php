<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500|Roboto+Slab:400,700">
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <title> {{ config('app.name') }}</title>
</head>
<body>
<div id="landing-page" class="container-fluid h-100">
    <div class="row h-100">

        <!-- Students -->
        <div class="col" id="col-students">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="col col-title">
                        <div class="col-title-top">
                            FOR
                        </div>
                        STUDENTS
                    </div>
                </div>
                <div class="row row-hidden">
                    <div class="col">
                        <div class="description">
                            Consulta as tuas inscrições, acede ao teu horário, paga as tuas propinas e consulta as tuas aulas.
                        </div>
                        <button class="btn btn-forlearn" data-parent="#col-students" data-toggle="modal" data-target="#modal-login">
                            @lang('auth.login')
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="col" id="col-teachers">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="col col-title">
                        <div class="col-title-top">
                            FOR
                        </div>
                        TEACHERS
                    </div>
                </div>
                <div class="row row-hidden">
                    <div class="col">
                        <div class="description">
                            Consulte as suas turmas, horários e disponibilize material para os seus alunos consultarem.
                        </div>
                        <button class="btn btn-forlearn" data-parent="#col-teachers" data-toggle="modal" data-target="#modal-login">
                            @lang('auth.login')
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff -->
        <div class="col" id="col-staff">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="col col-title">
                        <div class="col-title-top">
                            FOR
                        </div>
                        STAFF
                    </div>
                </div>
                <div class="row row-hidden">
                    <div class="col">
                        <div class="description">
                            Operações de administração e funcionalidades.
                        </div>
                        <button class="btn btn-forlearn" data-parent="#col-staff" data-toggle="modal" data-target="#modal-login">
                            @lang('auth.login')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.backoffice.modal_login')

</div>

<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ asset('js/vendor.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    $(function () {
        @if($errors->any())
        $('#modal-login').modal('show');
        @endif

        // Move the selected column to the left
        let columns = $('#col-students, #col-teachers, #col-staff');
        $('[data-target="#modal-login"]').on('click', function () {
            let parentSelector = $(this).attr('data-parent');
            columns.removeClass('order-first');
            $(parentSelector).addClass('order-first');
        });
    });
</script>

</body>
</html>
