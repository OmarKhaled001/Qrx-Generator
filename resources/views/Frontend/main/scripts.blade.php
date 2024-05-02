
    <script src="{{asset('assets-frontend')}}/js/jquery-3.7.0.min.js"></script>
    <script src="{{asset('assets-frontend')}}/js/bootstrap.min.js"></script>	
    <script src="{{asset('assets-frontend')}}/js/modernizr.custom.js"></script>
    <script src="{{asset('assets-frontend')}}/js/jquery.easing.js"></script>
    <script src="{{asset('assets-frontend')}}/js/jquery.appear.js"></script>
    <script src="{{asset('assets-frontend')}}/js/menu.js"></script>
    <script src="{{asset('assets-frontend')}}/js/owl.carousel.min.js"></script>
    <script src="{{asset('assets-frontend')}}/js/pricing-toggle.js"></script>
    <script src="{{asset('assets-frontend')}}/js/jquery.magnific-popup.min.js"></script>
    <script src="{{asset('assets-frontend')}}/js/request-form.js"></script>	
    <script src="{{asset('assets-frontend')}}/js/jquery.validate.min.js"></script>
    <script src="{{asset('assets-frontend')}}/js/jquery.ajaxchimp.min.js"></script>	
    <script src="{{asset('assets-frontend')}}/js/popper.min.js"></script>
    <script src="{{asset('assets-frontend')}}/js/lunar.js"></script>
    <script src="{{asset('assets-frontend')}}/js/wow.js"></script>	
    <script src="{{asset('assets-frontend')}}/js/custom.js"></script>
    <script>
        $(document).on({
            "contextmenu": function (e) {
                console.log("ctx menu button:", e.which); 
                // Stop the context menu
                e.preventDefault();
            },
            "mousedown": function(e) { 
                console.log("normal mouse down:", e.which); 
            },
            "mouseup": function(e) { 
                console.log("normal mouse up:", e.which); 
            }
        });
    </script>
    <script>
        $(function() {
            $(".switch").click(function() {
            $("body").toggleClass("theme--dark");
                if( $( "body" ).hasClass( "theme--dark" )) {
                    $( ".switch" ).text( "Light" );
                } else {
                    $( ".switch" ).text( "Dark" );
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            if( $( "body" ).hasClass( "theme--dark" )) {
                $( ".switch" ).text( "Light" );
            } else {
                $( ".switch" ).text( "Dark" );
            }
        });
    </script>
    <script src="{{asset('assets-frontend')}}/js/changer.js"></script>
    <script defer src="{{asset('assets-frontend')}}/js/styleswitch.js"></script>	