@section('title', $user->name)
@extends('layouts.print')
@section('content')

    <style>
        html, body {
            font-size: {{ $options['font-size'] }};
        }

        body {
            font-family: Montserrat, sans-serif;
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;
        }

        .form-group, .card, label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

        .h1-title {
            padding: 0;
            margin-bottom: 0;
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
        }

        .table-parameter-group {
            page-break-inside: avoid;
        }

        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }

        .tbody-parameter-group {
            border-top: 0;
            border-left: 1px solid #BCBCBC;
            border-right: 1px solid #BCBCBC;
            border-bottom: 1px solid #BCBCBC;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }

        .div-top {
            text-transform: uppercase;
            position: relative;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
        }

        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }

        input, textarea, select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

    </style>
    <main>
        <div class="div-top">
            <table class="table m-0 p-0">
                <tr>
                    <td class="td-fotografia" rowspan="2"
                        @foreach($user->parameters as $parameter)
                            @if($parameter->code === 'fotografia')
                                  style="background-image: url('{{ asset('storage/attachment/' . $parameter->pivot->value) }}');"
                            @endif
                        @endforeach
                    >
                    </td>
                    <td class="pl-1">
                        <h1 class="h1-title">
                            Recibo nº 20-526820
                        </h1>
                    </td>
                    <td class="td-institution-name" rowspan="2">
                        Instituto Superior<br>Politécnico Maravilha
                    </td>
                    <td class="td-institution-logo" rowspan="2">
                        <img class="img-institution-logo" src="{{ asset('img/logo.jpg') }}" alt="">
                    </td>
                </tr>
                <tr>
                    <td class="pl-1">
                        Documento gerado a
                        <b>{{ $date_generated }}</b>
                        
                    </td>
                </tr>
            </table>
        </div>
        @include('Users::users.partials.pdf_parameters')

        {{-- Attachments --}}
        @if($include_attachments)
            @foreach($parameter_groups as $parameter_group)
                @if($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))
                    @php
                        $parameters = $user->parameters->filter(function($item) {
                        return in_array($item->type, ['file_pdf', 'file_doc'], true);
                    });
                    @endphp

                    @if(!$parameters->isEmpty())
                        @foreach($parameters as $parameter)
                            @php
                                $user_parameter = $user->parameters->filter(function ($item) use ($parameter, $parameter_group) {
                                    return $item->pivot->parameters_id === $parameter->id && $item->pivot->parameter_group_id === $parameter_group->id;
                                })->first();
                            @endphp

                            @if($user_parameter)
                                <embed src="{{ URL::to('/') . '/storage/attachment/' . $user_parameter->pivot->value }}" width="100%" height="100%" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">
                            @endif
                        @endforeach
                    @endif

                @endif
            @endforeach
        @endif

    </main>

@endsection
