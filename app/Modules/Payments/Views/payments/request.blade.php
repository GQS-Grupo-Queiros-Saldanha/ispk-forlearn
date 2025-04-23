@switch($action)
    @case('create') @section('title',__('Payments::payments.create_request')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @lang('Payments::payments.create_request')
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('payments.create') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                {!! Form::open(['route' => ['account.store']]) !!}

                <div class="row">
                    <div class="col">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-sm btn-success mb-3">
                            <i class="fas fa-plus-circle"></i>
                            @lang('Payments::payments.submit')
                        </button>

                        @if(auth()->user()->can('create-requests-others'))
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>@lang('Payments::payments.student')</label>
                                            {{ Form::bsLiveSelect('user', $users, null, ['required', 'placeholder' => '']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div id="article-select-container"
                             class="card" {{ auth()->user()->can('create-requests-others') ? 'hidden' : '' }}>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Payments::articles.article')</label>
                                        {{ Form::bsLiveSelectEmpty('article', [], null, ['required', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div id="select-year-container" class="col-3" hidden>
                                    <div class="form-group col">
                                        <label>@lang('Payments::payments.year')</label>
                                        {{ Form::bsLiveSelect('year', $years, \Carbon\Carbon::now()->year, ['required', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div id="select-month-container" class="col-3" hidden>
                                    <div class="form-group col">
                                        <label>@lang('Payments::payments.month')</label>
                                        {{ Form::bsLiveSelect('month', $months, \Carbon\Carbon::now()->month, ['required', 'placeholder' => '']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="article-info" class="card" hidden>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('common.code')</label>
                                        <div id="show_article_code">...</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::articles.article')</label>
                                        <div
                                            id="show_article_name">...
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('translations.description')</label>
                                        <div
                                            id="show_article_description">...
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::articles.base_value')</label>
                                        <div>
                                            <b id="show_article_value">...</b>
                                            Kz
                                        </div>
                                    </div>
                                </div>
                                <div id="show_article_fees_container" class="col-12" style="display: none;">
                                    <div class="col">
                                        <label>@lang('Payments::articles.extra_fees.extra_fee')</label>
                                        <div id="show_article_fees">...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        var articles = null;
        var articleContainer = $('#article-select-container');
        var articleInfoContainer = $('#article-info');
        var yearContainer = $('#select-year-container');
        var monthContainer = $('#select-month-container');
        var selectUser = $('#user');
        var selectArticle = $('#article');

        function resetArticles() {
            articleContainer.attr('hidden', true);
            resetArticleInfo()
        }

        function resetArticleInfo() {
            articleInfoContainer.prop('hidden', true);
            yearContainer.attr('hidden', true);
            monthContainer.attr('hidden', true);
        }

        function switchArticles(userId) {
            if (userId) {
                $.ajax({
                    url: '{{ route('account.ajax_articles', 0) }}'.slice(0, -1) + userId
                }).done(function (response) {
                    selectArticle.empty();

                    if (response.length) {
                        selectArticle.append('<option selected="" value=""></option>');
                        response.forEach(function (article) {
                            selectArticle
                                .append('<option value="' + article.id + '">' + article.current_translation.display_name + '</option>');
                        });

                        articles = response;

                        articleContainer.attr('hidden', false);
                        selectArticle.selectpicker('refresh');
                    } else {
                        resetArticles();
                    }
                });
            } else {
                resetArticles();
            }
        }

        function showCurrentSelectedArticle(element) {
            var selectedArticleIdx = $.inArray(
                parseInt(element.value),
                $.map(articles, function (a) {
                    return a.id;
                })
            );

            if (selectedArticleIdx !== -1) {
                var selectArticle = articles[selectedArticleIdx];

                var yearSelect = yearContainer.find('select');
                var monthSelect = monthContainer.find('select');
                if (selectArticle.monthly_charges.length) {
                    yearContainer.removeAttr('hidden', false);
                    yearSelect.attr('name', 'month');
                    monthContainer.removeAttr('hidden', false);
                    monthSelect.attr('name', 'month');
                } else {
                    yearContainer.attr('hidden', true);
                    yearSelect.removeAttr('name');
                    monthContainer.attr('hidden', true);
                    monthSelect.removeAttr('name');
                }

                $('#show_article_code').text(selectArticle.code);
                $('#show_article_name').text(selectArticle.current_translation.display_name);
                $('#show_article_description').text(selectArticle.current_translation.description ? selectArticle.current_translation.description : 'N/A');
                $('#show_article_value').text(selectArticle.base_value);

                $('#show_article_fees').html(selectArticle.extraFeesAsText);
                selectArticle.extraFeesAsText !== '' ? $('#show_article_fees_container').show() : $('#show_article_fees_container').hide();

                articleInfoContainer.prop('hidden', false);
            } else {
                resetArticleInfo()
            }
        }

        $(document).ready(function () {
            @if(auth()->user()->can('create-requests-others'))
            switchArticles(selectUser[0].value);
            selectUser.change(function () {
                switchArticles(this.value);
            });
            @else
            switchArticles({{ auth()->user()->hasRole('student') ? auth()->user()->id : null }});
            @endif

            selectArticle.change(function () {
                console.log(this.value);
                showCurrentSelectedArticle(this);
            });
        });
    </script>
@endsection
