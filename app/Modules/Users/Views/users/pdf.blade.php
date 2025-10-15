@section('title', $user->name)
@extends('layouts.print')
@section('content')
   
<main>
    <style>
        
        .bg0,.div-top{
                background-color: #2f5496!important;
                color: white!important;
            }
            .bg1{
                background-color: #8eaadb!important;
                padding: 10px 25px!important;
                height: 20px;
                font-size: 40px;
                color: white!important;
                margin-bottom: 30px;
                border: none!important;
                
            }
            .bg2{
                background-color: #d9e2f3!important;
            }
            .bg3{
                background-color:#fbe4d5!important;
            }
            .bg4{
                background-color:#f4b083!important;
            } 
            .h1-title{
                color: white;
            }

            .div-top table tr:last-child{
                color: white;
            }

        
    </style>
    @include('Reports::pdf_model.pdf_header')
    <!-- aqui termina o cabeçalho do pdf -->
        
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
