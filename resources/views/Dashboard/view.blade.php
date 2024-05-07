<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> View | Qrx-Generator </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets-dashboard/images/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets-dashboard/libs/multi.js/multi.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-dashboard/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}" rel="stylesheet">
    @include('Dashboard.main.css')
</head>

<body>
    <div id="layout-wrapper">
        <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-11">
                        <div class=" mb-0">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-6 mx-auto">
                                    <div class="p-2 mt-3 text-center">
                                        @include('Dashboard.sections.view.text')
                                        @include('Dashboard.sections.view.url')
                                        @include('Dashboard.sections.view.message')
                                        @include('Dashboard.sections.view.email')
                                        @include('Dashboard.sections.view.vcard')
                                        @include('Dashboard.sections.view.phone')
                                        @include('Dashboard.sections.view.wifi')
                                    </div>
                                    <div class="p-5 mt-5 text-center">
                                        <h1>
                                            <i class="fa-solid fa-qrcode fa-bounce fa-2xl text-success"></i>
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('Dashboard.main.scripts')
    <script src="{{ asset('assets-dashboard/js/app.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/pages/password-addon.init.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/multi.js/multi.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>
    <script src="{{ asset('assets-dashboard/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{asset('assets-dashboard/js/pages/flag-input.init.js')}}"></script>
    <script>
    $("#tel").intlTelInput({utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js"});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script src="https://cdn.rawgit.com/zenorocha/clipboard.js/v2.0.11/dist/clipboard.min.js"></script>
    <script src="https://unpkg.com/clipboard@2/dist/clipboard.min.js"></script>
    <script src="https://rawcdn.githack.com/zenorocha/clipboard.js/v2.0.11/dist/clipboard.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script src="dist/clipboard.min.js"></script>
    <script>new ClipboardJS('.btn');</script>
</body>
</html>







