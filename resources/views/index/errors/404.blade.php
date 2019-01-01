@extends('index.layouts')

@section('main')
    <article class="message is-danger">
        <div class="message-header">
            <p>{{ $code }}</p>
            <a class="icon" href="{{ route('home') }}">
                <i class="fas fa-home"></i>
            </a>
        </div>
        <div class="message-body">{{ $message }}</div>
    </article>
@endsection
