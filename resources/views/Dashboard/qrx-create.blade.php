
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">
    
<head>
    <meta charset="utf-8" />
    <title> Dashboard | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/favicon.ico') }}">
    <link href="{{ asset('assets-dashboard/libs/multi.js/multi.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-dashboard/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}" rel="stylesheet">
    @include('Dashboard.main.css')
    @livewireStyles
</head>
<body>
    <div id="layout-wrapper">
        @include('Dashboard.main.topbar')
        @include('Dashboard.main.sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @livewire('qrx-create')
                </div>
            </div>
            @include('Dashboard.main.footer')
        </div>
    </div>
    @include('Dashboard.main.customizer')
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/js/pages/password-addon.init.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/multi.js/multi.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{asset('assets-dashboard/js/pages/flag-input.init.js')}}"></script>
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
    @livewireScripts
</body>
</html>








