@extends('auth.layout')

@section('title', 'Confirm your password')
@section('intro', 'This is a secure area. Please confirm your password to continue.')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
            @error('password') <p class="text-danger mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirm</button>
    </form>
@endsection
