@if($tab == 'wifi')
    <div class="p-3">
        <label class="form-label card-title d-block text-center">Wi-Fi</label>

        <label class="h6 my-2">Wireless SSID :</label>
        <input type="text" class="form-control my-1"  placeholder="write Full Name" wire:model="wireless">

        <label class="h6 my-2">Password :</label>
        <input type="password" class="form-control my-1"  placeholder="write Password" wire:model="password">

        <label class="h6 my-2">Encryption :</label>
        <select class="form-select my-1"  wire:model="encryption">
            <option  value="WPA/WEP">WPA/WEP</option>
            <option selected value="WEP">WEP</option>
        </select>

    </div>
@endif