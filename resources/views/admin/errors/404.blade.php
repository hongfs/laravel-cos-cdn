@extends('admin.layouts')

@section('main')
    <article class="message is-danger is-medium">
        <div class="message-header">
            <p>{{ $code }}</p>
            <a class="icon" href="{{ route('console') }}">
                <i class="fas fa-home"></i>
            </a>
        </div>
        <div class="message-body">{{ $message }}</div>
    </article>
@endsection
