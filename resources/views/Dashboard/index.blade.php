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
                <h1>{{Auth()->user()->subscribed()}}</h1>
                <div class="container-fluid">
                    {{-- @include('Dashboard.sections.home.header')
                    @if (!empty($plan))
                    @include('Dashboard.sections.home.plan-detail') --}}
                        @if($qrxs != null)
                            @include('Dashboard.sections.home.chart')
                            @include('Dashboard.sections.home.top-qr')
                        @endif
                    {{-- @else
                        @include('Dashboard.sections.qrx.subscripe')
                    @endif --}}
                </div>
            </div>
            @include('Dashboard.main.footer')
        </div>
    </div>
    @include('Dashboard.main.customizer')
    @if($qrxs != null)
        <script src="{{ $QrType->cdn() }}"></script>
        <script src="{{ $QrStatus->cdn() }}"></script>
        {{ $QrType->script() }}
        {{ $QrStatus->script() }}
    @endif
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/pages/apexcharts-bar.init.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
</body>
</html>




