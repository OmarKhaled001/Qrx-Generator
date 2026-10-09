<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> Profile | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/favicon.ico') }}">
    @include('Dashboard.main.css')
</head>

<body>
    <div id="layout-wrapper">
        @include('Dashboard.main.topbar')
        @include('Dashboard.main.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header"><h5 class="card-title mb-0">Profile information</h5></div>
                                <div class="card-body">
                                    @if (session('status') === 'profile-updated')
                                        <div class="alert alert-success" role="status">Profile saved.</div>
                                    @endif
                                    <form method="POST" action="{{ route('profile.update') }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                                            @error('name') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                            @error('email') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><h5 class="card-title mb-0">Update password</h5></div>
                                <div class="card-body">
                                    @if (session('status') === 'password-updated')
                                        <div class="alert alert-success" role="status">Password updated.</div>
                                    @endif
                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current password</label>
                                            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                                            @error('current_password', 'updatePassword') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New password</label>
                                            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
                                            @error('password', 'updatePassword') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm new password</label>
                                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                                        </div>
                                        <button type="submit" class="btn btn-success">Update password</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card border-danger">
                                <div class="card-header"><h5 class="card-title mb-0 text-danger">Delete account</h5></div>
                                <div class="card-body">
                                    <p class="text-muted">Deleting your account permanently removes it. Enter your password to confirm.</p>
                                    <form method="POST" action="{{ route('profile.destroy') }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="mb-3">
                                            <label for="delete_password" class="form-label">Password</label>
                                            <input id="delete_password" name="password" type="password" class="form-control" autocomplete="current-password">
                                            @error('password', 'userDeletion') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <button type="submit" class="btn btn-danger">Delete account</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('Dashboard.main.footer')
        </div>
    </div>
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
</body>
</html>
