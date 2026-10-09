@extends('auth.layout')

@section('title', 'Choose a new password')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control" required autocomplete="username">
            @error('email') <p class="text-danger mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
            @error('password') <p class="text-danger mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Reset password</button>
    </form>
@endsection
