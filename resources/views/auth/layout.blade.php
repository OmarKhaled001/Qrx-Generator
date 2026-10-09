<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/favicon.ico') }}">
    @include('Dashboard.main.css')
</head>

<body>
    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mb-0">
                        <div class="card-body p-4 p-sm-5">
                            <div class="text-center mb-4">
                                <h5 class="fs-3xl">@yield('title')</h5>
                                @hasSection('intro')
                                    <p class="text-muted">@yield('intro')</p>
                                @endif
                            </div>
                            @if (session('status'))
                                <div class="alert alert-success" role="status">{{ session('status') }}</div>
                            @endif
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('Dashboard.main.scripts')
</body>
</html>
