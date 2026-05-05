@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">Dashboard</h1>
            <p class="mb-0 text-body-secondary">{{ __('Current locale') }}: <strong>{{ app()->getLocale() }}</strong></p>
        </div>
    </div>
@endsection
