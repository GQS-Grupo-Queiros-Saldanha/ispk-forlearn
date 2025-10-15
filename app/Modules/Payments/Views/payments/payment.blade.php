@section('title',__('Payments::payments.payment'))

@extends('layouts.backoffice')

@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @lang('Payments::payments.payment')
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('payments.show', $payment) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @if(auth()->user()->can('manage-manual-payments') && $payment->fulfilled_at === null)
                    {!! Form::open(['route' => ['account.manual_update', $payment->id]]) !!}
                @endif

                <div class="row">
                    <div class="col">

                        {{--                        <button type="submit" class="btn btn-sm btn-warning mb-5 w-auto">--}}
                        {{--                            <i class="fas fa-envelope"></i>--}}
                        {{--                            Re-enviar email--}}
                        {{--                        </button>--}}

                        {{--                        <a href="{{ route('articles.edit', 0) }}"--}}
                        {{--                           class="btn btn-sm btn-danger mb-5 w-auto">--}}
                        {{--                            <i class="fas fa-ban"></i>--}}
                        {{--                            Cancelar--}}
                        {{--                        </a>--}}

                        @if(auth()->user()->can('manage-manual-payments'))
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                <i class="fas fa-save"></i>
                                @lang('common.save')
                            </button>

                            <div class="card">
                                @if ($payment->fulfilled_at === null)
                                <div class="row">
                                    <div class="col-6">
                                        {{ Form::bsNumber('manual_value', null, ['placeholder' => 'AKZ', 'min' => 0, 'max' => $payment->total_value - $payment->total_paid], ['label' => __('Payments::payments.manual_payment_value')]) }}
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        {{ Form::bsTextArea('free_text', $payment->free_text, [], ['label' => __('Payments::payments.manual_free_text')]) }}
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <hr>
                        @endif

                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::payments.status.status')</label>
                                        <div>{!! $payment_status !!}</div>
                                    </div>
                                    <div class="col">
                                        <label>@lang('Payments::payments.missing_value')</label>
                                        <div>{{ number_format($payment->total_value - $payment->total_paid, 2, ",", ".") }}
                                            Kz
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        {{-- Payment --}}
                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col">
                                        <h3>@lang('Payments::payments.payment_info')</h3>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::payments.user')</label>
                                        <div id="show_article_code">{{ $payment->user->name }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::articles.article')</label>
                                        <div
                                            id="show_article_code">{{ $payment->article ? $payment->article->currentTranslation->display_name : 'N/A' }}</div>
                                    </div>
                                </div>
                                {{--<div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::payments.entity')</label>
                                        <div id="show_article_code">99915</div>
                                    </div>
                                </div>--}}
                                {{--<div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::payments.reference')</label>
                                        <div
                                            id="show_article_code">{{ substr($payment->transaction_uid, 0, 3) . ' ' . substr($payment->transaction_uid, 3, 3) . ' ' . substr($payment->transaction_uid, 6, 3) }}</div>
                                    </div>
                                </div>--}}
                                <div class="col-12">
                                    <div class="col">
                                        <label>@lang('Payments::payments.value')</label>
                                        <div
                                            id="show_article_code">{{ number_format($payment->total_value, 2, ",", ".") }}
                                            Kz
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        {{-- Article --}}
                        @if($payment->article)
                            <div class="card mb-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col">
                                            <h3>@lang('Payments::payments.article_info')</h3>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col">
                                            <label>@lang('common.code')</label>
                                            <div id="show_article_code">{{ $payment->article->code }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col">
                                            <label>@lang('Payments::articles.article')</label>
                                            <div
                                                id="show_article_name">{{ $payment->article->currentTranslation->display_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col">
                                            <label>@lang('translations.description')</label>
                                            <div
                                                id="show_article_description">{{ $payment->article->currentTranslation->display_name ?: 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col">
                                            <label>@lang('Payments::articles.base_value')</label>
                                            <div>
                                                <b id="show_article_value">{{ $payment->article->base_value }}</b>
                                                Kz
                                            </div>
                                        </div>
                                    </div>
                                    @if($article_extra_fees)
                                        <div id="show_article_fees_container" class="col-12">
                                            <div class="col">
                                                <label>@lang('Payments::articles.extra_fees.extra_fee')</label>
                                                <div id="show_article_fees">{!! $article_extra_fees !!}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
    </script>
@endsection
