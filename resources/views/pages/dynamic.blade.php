@extends('layouts.app')

@section('title', $page->title)

@section('content')
    <article class="mx-auto" style="max-width: 720px;">
        <h1 class="h2 mb-3">{{ $page->title }}</h1>
        <div class="text-body-secondary lh-lg">{!! nl2br(e($page->body)) !!}</div>
    </article>
@endsection
