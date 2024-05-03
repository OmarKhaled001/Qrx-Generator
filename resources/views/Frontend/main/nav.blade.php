

<header id="header" class="tra-menu navbar-light white-scroll">
    <div class="header-wrapper">
        <div class="wsmobileheader clearfix">	  	
            <span class="smllogo "><img src="{{asset('assets-frontend')}}/images/logo-pink.png" alt="mobile-logo"></span>
            <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>	
        </div>
        <div class="wsmainfull menu clearfix">
            <div class="wsmainwp clearfix">
                <div class="desktoplogo">
                    <a href="#hero-1" class="logo-black">
                        <img class="light-theme-img" src="{{asset('assets-frontend')}}/images/logo-pink.png" alt="logo">
                        <img class="dark-theme-img" src="{{asset('assets-frontend')}}/images/logo-white.png" alt="logo">
                    </a>
                </div>
                <div class="desktoplogo">
                    <a href="#hero-1" class="logo-white"><img src="{{asset('assets-frontend')}}/images/logo-white.png" alt="logo"></a>
                </div>
                    <nav class="wsmenu clearfix">
                    <ul class="wsmenu-list nav-theme">
                        <li class="nl-simple" aria-haspopup="true"><a href="#hero-1" class="h-link">Home</a></li>
                        <li class="nl-simple" aria-haspopup="true"><a href="#about-us" class="h-link">About Us</a></li>
                        <li class="nl-simple" aria-haspopup="true"><a href="#features" class="h-link">Features</a></li>
                        @if( count(Auth()->user()->subscriptions) > 0)				
                        <li class="nl-simple" aria-haspopup="true"><a href="#pricing-1" class="h-link">Pricing</a></li>
                        @endif
                        <li class="nl-simple" aria-haspopup="true"><a href="#contacts-1" class="h-link">Countact Us</a></li>
                        @if(Auth::guard('user')->user())
                        <li class="nl-simple" aria-haspopup="true">
                            <a href="{{route('dashboard')}}" class="btn r-04 btn--theme hover--tra-white last-link">Dashboard</a>
                        </li> 
                        @else
                        <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                            <a href="{{route('login')}}" class="h-link">Sign in</a>
                        </li>
                        <li class="nl-simple" aria-haspopup="true">
                            <a href="{{route('register')}}" class="btn r-04 btn--theme hover--tra-white last-link">Sign up</a>
                        </li> 
                        @endif
                    </ul>
                </nav>	
            </div>
        </div>	
    </div>     
</header>	

