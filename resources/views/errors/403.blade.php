@extends('errors::illustrated-layout')

@section('code', '403')
@section('title', __('errors.403_title'))

@section('image')
    <div style="background-image: url({{ asset('/svg/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@php
    $message = __('errors.403_message');
    try {
        if(config('permission.display_permission_in_exception')) {
            $message .= ' ' .  __('errors.required_permissions_are') . implode(', ', $exception->getRequiredPermissions());
        }
    } catch (\Exception|\Throwable $e) {
        //echo $e->getMessage();
    }
@endphp

@section('message', $message)
