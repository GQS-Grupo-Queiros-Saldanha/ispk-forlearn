@section('title',__('GA::enrollments.enrollments'))
@extends('layouts.backoffice')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/jquery.steps.css') }}">
@endsection

@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('GA::enrollments.create_enrollment') @break
                                @case('show') @lang('GA::enrollments.enrollment') @break
                                @case('edit') @lang('GA::enrollments.edit_enrollment') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('enrollments.create') }} @break
                            @case('show') {{ Breadcrumbs::render('enrollments.show', $enrollment) }} @break
                            @case('edit') {{ Breadcrumbs::render('enrollments.edit', $enrollment) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['enrollments.store'], 'id' => 'form-enrollment']) !!}
                    @break
                    @case('show')
                    {!! Form::model($enrollment) !!}
                    @break
                    @case('edit')
                    {!! Form::model($enrollment, ['route' => ['enrollments.update', $enrollment->id], 'method' => 'put']) !!}
                    @break
                @endswitch

                {{--Steps--}}
                <div id="stepper">
                    @include('GA::enrollments.steps.step1', [])
                    @include('GA::enrollments.steps.step2', [])
                    @include('GA::enrollments.steps.step3', [$study_plan_editions])
                    @include('GA::enrollments.steps.step4', [])
                    @include('GA::enrollments.steps.step5', [])
                    @include('GA::enrollments.steps.step6', [])
                    @include('GA::enrollments.steps.step7', [])
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection


@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
    <script>
        $(function () {

            var $stepper = $("#stepper");

            $stepper.steps({

                /* Appearance */
                headerTag: "h3",
                bodyTag: "section",
                stepsOrientation: "vertical",

                /* Behaviour*/
                autoFocus: true,

                /* Transition Effects */
                transitionEffect: "slideLeft",

                /* Events */
                onFinishing: function (event, currentIndex) {

                    // Remover select das study plan editions
                    $('input[name="study_plan_editions[]"]').remove();

                    return true;
                },
                onFinished: function (event, currentIndex) {
                    $('#form-enrollment').submit();
                },
                onStepChanging: function (event, currentIndex, newIndex) {

                    // After selection user
                    if (currentIndex === 0 && newIndex !== 0) {
                        loadUserParameters($('select[name^="user"]').val());
                    }

                    // After filling up user data
                    if (currentIndex === 1 && newIndex !== 1) {
                        var isValid = true;
                        $('#stepper-p-1').find('input,select,textarea').filter('[required]:visible').each(function() {
                            var $self = $(this);
                            if ($self.val() === '') {
                                $self.removeClass('is-valid').addClass('is-invalid');
                                isValid = false;
                            } else {
                                $self.removeClass('is-invalid').addClass('is-valid');
                            }
                        });

                        return isValid;
                    }

                    // After selecting study_plan_edition
                    if (currentIndex === 2 && newIndex !== 2) {
                        loadStudyPlanEditionDisciplines($('select[name^="study_plan_edition"]').val());
                    }

                    return true;
                },

                /* Labels */
                labels: {
                    cancel: "@lang('common.cancel')",
                    current: "current step:",
                    pagination: "Pagination",
                    finish: "@lang('common.finish')",
                    next: "@lang('common.next')",
                    previous: "@lang('common.previous')",
                    loading: "@lang('common.loading') ..."
                }
            });

            function loadUserParameters(user_id) {
                var url = '{{ route('enrollments.user.ajax', ':id') }}';
                url = url.replace(':id', user_id);
                $.ajax({
                    url: url,
                    beforeSend: function() {
                        $('#stepper-p-1').addClass('bg-loading');
                    }
                }).done(function (html) {
                    $('#stepper-p-1').removeClass('bg-loading').html(html);
                }).fail(function() {
                    $('#stepper-p-1').removeClass('bg-loading');
                });
            }

            function loadStudyPlanEditionDisciplines(study_plan_edition_id) {
                // Load its disciplines
                var url = '{{ route('enrollments.disciplines.ajax', ':id') }}';
                url = url.replace(':id', study_plan_edition_id);
                $.ajax({
                    url: url,
                    beforeSend: function () {
                        console.log('Loading disciplines for study plan edition with ID ' + study_plan_edition_id);
                        $('#stepper-p-3').addClass('bg-loading');
                    }
                }).done(function (html) {
                    $('#stepper-p-3').removeClass('bg-loading').html(html);
                }).fail(function() {
                    $('#stepper-p-3').removeClass('bg-loading');
                });

                // Load its optional disciplines
                var url = '{{ route('enrollments.optional-disciplines.ajax', ':id') }}';
                url = url.replace(':id', study_plan_edition_id);
                $.ajax({
                    url: url,
                    beforeSend: function () {
                        console.log('Loading optional disciplines for study plan edition with ID ' + study_plan_edition_id);
                        $('#stepper-p-4').addClass('bg-loading')
                    }
                }).done(function (html) {
                    $('#stepper-p-4').removeClass('bg-loading').html(html);
                }).fail(function () {
                    $('#stepper-p-4').removeClass('bg-loading');
                });
            }

        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
