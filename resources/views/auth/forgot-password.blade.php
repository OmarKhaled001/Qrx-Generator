@extends('auth.layout')

@section('title', 'Forgot password')
@section('intro', 'Enter your email and we will send you a link to choose a new password.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="email">
            @error('email') <p class="text-danger mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Email password reset link</button>
    </form>
    <p class="text-center mt-4 mb-0"><a href="{{ route('login') }}">Back to sign in</a></p>
@endsection
