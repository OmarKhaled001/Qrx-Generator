@extends('auth.layout')

@section('title', 'Verify your email')
@section('intro', 'We sent a verification link to your email address. Click it to continue.')

@section('content')
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success" role="status">A new verification link has been sent.</div>
    @endif
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary w-100">Resend verification email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="text-center mt-3">
        @csrf
        <button type="submit" class="btn btn-link">Sign out</button>
    </form>
@endsection
