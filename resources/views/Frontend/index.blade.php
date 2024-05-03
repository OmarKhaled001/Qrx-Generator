<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="DSAThemes">	
    <meta name="description" content="Martex - Software, App, SaaS & Startup Landing Pages Pack">
    <meta name="keywords" content="Responsive, HTML5, DSAThemes, Landing, Software, Mobile App, SaaS, Startup, Creative, Digital Product">	
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Qrx-Genrator</title>
	@include('Frontend.main.css')
    @livewireStyles
</head>
<body>
    <div id="loading" class="loading--theme">
        <div id="loading-center"><span class="loader"></span></div>
    </div>
    <div id="page" class="page font--jakarta">
        @include('Frontend.main.nav')				
        @include('Frontend.sections.slider')				
        @include('Frontend.sections.about-us')				
        @include('Frontend.sections.qr-create')				
        @include('Frontend.sections.features')
        @if( count(Auth()->user()->subscriptions) ==  0)				
        @include('Frontend.sections.pricing')	
        @endif			
        @include('Frontend.sections.contact-us')				
        @include('Frontend.main.footer')
    </div>		
    @livewireScripts
    @include('Frontend.main.scripts')				
</body>
</html>