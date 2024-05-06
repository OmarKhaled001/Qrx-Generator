<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> Register | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/favicon.ico') }}">
    @include('Dashboard.main.css')
</head>

<body>
    <div id="layout-wrapper">
        <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-11">
                        @include('Dashboard.sections.auth.register-form')
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/pages/password-addon.init.js') }}"></script>
</body>
</html>



