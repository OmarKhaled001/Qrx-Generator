<div class="row" wire:poll>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Select Feature</h5>
                <ul class="nav nav-pills nav-success" role="tablist">
                    <li class="nav-item  ">
                        <button class="nav-link {{$tab == 'text' ? 'active' :''}} "wire:click="text" >Text</button>
                    </li>
                    <li class="nav-item {{$tab == 'url' ? 'active' :''}} ">
                        <button class="nav-link {{$tab == 'url' ? 'active' :''}} "wire:click="url" >Url</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{$tab == 'message' ? 'active' :''}}"wire:click="message" >SMS</button>
                    </li>
                    <li class="nav-item ">
                        <button class="nav-link {{$tab == 'email' ? 'active' :''}} "wire:click="email" >Email</button>
                    </li>
                    <li class="nav-item ">
                        <button class="nav-link {{$tab == 'vcard' ? 'active' :''}} "wire:click="vcard" >Vcard</button>
                    </li>
                    <li class="nav-item ">
                        <button class="nav-link  {{$tab == 'phone' ? 'active' :''}}"wire:click="phone" >Phone</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link  {{$tab == 'wifi' ? 'active' :''}} "wire:click="wifi" >Wi-Fi</button>
                    </li>
                    <li class="nav-item ">
                        <button class="nav-link {{$tab == 'location' ? 'active' :''}}"wire:click="location" >Location</button>
                    </li>
                    {{-- <li class="nav-item">
                        <button class="nav-link  {{$tab == 'mp3' ? 'active' :''}} "wire:click="mp3" >Mp3</button>
                    </li> --}}
                </ul>
            </div>
            <div class="p-3">
                <label class="form-label card-title text-start">Qr Name:</label>
                <input type="text" class="form-control"  placeholder="Write Name For Qr" wire:model="qrxName">
            </div>
            @include('livewire.features.text')
            @include('livewire.features.url')
            @include('livewire.features.email')
            @include('livewire.features.message')
            @include('livewire.features.phone')
            @include('livewire.features.vcard')
            @include('livewire.features.wifi')
            <div class="d-flex text-center p-3">
                <button type="submit" class="btn btn-success  my-2" wire:click="generat" >Edit</button>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 p-3 text-center align-middle">
            <label class="form-label card-title text-start">Preview</label>
                <div class="row">
                    {{ $QrCode }}
                </div>
    </div>
    <div class="card border-0 p-2 text-center align-middle my-2">
        <label  class="form-label card-title text-start">Settings</label>
        <ul class="nav nav-pills text-center nav-success" role="tablist">
            <li class="nav-item  ">
                <button class="nav-link {{$settings == 'size' ? 'active' :''}} "wire:click="size" >Size</button>
            </li>
            <li class="nav-item ">
                <button class="nav-link {{$settings == 'style' ? 'active' :''}} "wire:click="style" >Style</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{$settings == 'color' ? 'active' :''}}"wire:click="color" >Color</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{$settings == 'gradient' ? 'active' :''}}"wire:click="gradient" >Gradient</button>
            </li>
        </ul>
        @include('livewire.settings.size')
        @include('livewire.settings.style')
        @include('livewire.settings.color')
        @include('livewire.settings.gradient')
    </div>
</div>
