<div class="row card d-flex align-items-center" wire:poll>
    <div class="col-lg-6 col-sm-12 left-column wow fadeInRight">
        <div id="rw-2-2" class="review-2 bg--white-100 r-08">
            <div class="blog-post-img mb-35">
                <div class="img-fluid r-16 text-center ">
                    {{$QrCode}}
                </div>
            </div>
            <div class="review-ico ico-65"><span class="flaticon-qr"></span></div>
            <!-- Text -->
            
        </div>	
    </div>

    <div class="col-lg-6 col-sm-12">
        <div class="txt-block right-column wow fadeInLeft p-3">
                <div class="r-08 w-100">
                    <div class="col-md-12 my-2 contact-form">
                        <h5 class="p-lg">Text :</label>
                        <input type="text" class="form-control my-1"  placeholder="write text" wire:model="text">
                    </div>
                    <div class="col-md-12 ">
                        <h5  class="form-label text-start my-2">Settings</h5>
                        <div class=" p-2 text-center align-middle ">
                            <ul class="nav nav-pills text-center nav-success" role="tablist">
                                <li class="nav-item  ">
                                    <button class="nav-link  {{$settings == 'size' ? ' r-04 btn--theme hover--tra-white ' :''}} "wire:click="size" >Size</button>
                                </li>
                                <li class="nav-item ">
                                    <button class="nav-link {{$settings == 'style' ? ' r-04 btn--theme hover--tra-white ' :''}} "wire:click="style" >Style</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link {{$settings == 'color' ? ' r-04 btn--theme hover--tra-white ' :''}}"wire:click="color" >Color</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link  {{$settings == 'gradient' ? ' r-04 btn--theme hover--tra-white ' :''}}"wire:click="gradient" >Gradient</button>
                                </li>
                            </ul>
                            <div class="">
                                @include('livewire.settings.size')
                                @include('livewire.settings.style')
                                @include('livewire.settings.color')
                                @include('livewire.settings.gradient')
                            </div>
                            <div class="py-3 ">
                                <button   wire:click="download" class="btn w-100 r-04 btn--theme">
                                    <span class="flaticon-down-arrow-1 "></span> Download
                                </button>
                            </div>
                        </div>
                    </div>	

             </div>
        </div>
    </div>	
</div>
