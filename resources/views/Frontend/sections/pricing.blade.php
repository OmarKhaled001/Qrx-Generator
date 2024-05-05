<section id="pricing-1" class="gr--whitesmoke pb-40 inner-page-hero pricing-section">
    <div class="container">
        @if(count(Auth()->user()->subscriptions) == null)
        <div class="row justify-content-center">	
            <div class="col-md-10 col-lg-8">
                <div class="section-title mb-70">	
                    <h2 class="s-52 w-700">Our Plans</h2>	
                </div>	
            </div>
        </div>
        <div class="pricing-1-wrapper">
            <div class="row row-cols-1 row-cols-md-3">
                @foreach ($plans as $plan) 
                <div class="col">
                    <div id="pt-1-1" class="p-table pricing-1-table bg--white-100 block-shadow r-12 wow fadeInUp">
                        <div class="pricing-table-header">
                            <h5 class="s-24 w-700">{{$plan->title}}</h5>
                            <div class="price">								
                                <sup class="color--black">$</sup>								
                                <span class="color--black">{{$plan->price}}</span>
                                <sup class="validity color--grey">&nbsp;/&ensp;Year</sup>
                                <p class="color--grey">For professionals getting started with smaller projects.</p>
                            </div>
                            <a href="{{route('checkout',['id'=>$plan->id])}}" class="pt-btn btn r-04 btn--theme hover--theme">Get srarted</a>
                        </div>
                        <ul class="pricing-features color--black ico-10 ico--green mt-25">
                            <li><p><span class="flaticon-check"></span> {{$plan->qr_count}} Qr</p></li>
                            <li><p><span class="flaticon-check"></span> {{$plan->scan_count}} scan for qr</p></li>
                            <li><p><span class="flaticon-check"></span> For personal use</p></li>
                        </ul>	
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="row justify-content-center">	
            <div class="col-md-10 col-lg-8">
                <div class="section-title mb-70">	
                    <h2 class="s-52 w-700">Upgrade Plans</h2>	
                </div>	
            </div>
        </div>
        <div class="pricing-1-wrapper">
            <div class="row row-cols-1 row-cols-md-3">
                @foreach ($plans as $plan) 
                <div class="col">
                    <div id="pt-1-1" class="p-table pricing-1-table bg--white-100 block-shadow r-12 wow fadeInUp">
                        <div class="pricing-table-header">
                            <h5 class="s-24 w-700">{{$plan->title}}</h5>
                            <div class="price">								
                                <sup class="color--black">$</sup>								
                                <span class="color--black">{{$plan->price}}</span>
                                <sup class="validity color--grey">&nbsp;/&ensp;Year</sup>
                                <p class="color--grey">For professionals getting started with smaller projects.</p>
                            </div>
                            <a href="{{route('checkout',['id'=>$plan->id])}}" class="pt-btn btn r-04 btn--theme hover--theme">Upgrade</a>
                        </div>
                        <ul class="pricing-features color--black ico-10 ico--green mt-25">
                            <li><p><span class="flaticon-check"></span> {{$plan->qr_count}} Qr</p></li>
                            <li><p><span class="flaticon-check"></span> {{$plan->scan_count}} scan for qr</p></li>
                            <li><p><span class="flaticon-check"></span> For personal use</p></li>
                        </ul>	
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

