<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> Dashboard | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
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
                    @include('Dashboard.sections.models.new-folder')
                    @if(count($qrxCodes)> 0)
                        @include('Dashboard.sections.qrx.header')
                        @include('Dashboard.sections.qrx.qrs')
                        <div class="row text-center">
                            {{ $qrxCodes->links() }} 
                        </div>
                    @else
                        @include('Dashboard.sections.qrx.create')
                    @endif
                </div>
            </div>
            @include('Dashboard.main.footer')
        </div>
    </div>
    @include('Dashboard.main.customizer')
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
</body>
</html>




