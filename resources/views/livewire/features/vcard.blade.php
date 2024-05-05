@if($tab == 'vcard')
    <div class="p-3">
        <label class="h6 my-2">Full Name :</label>
        <input type="text" class="form-control my-1"  placeholder="write Full Name" wire:model="fullName">
        @error('fullName')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <label class="h6 my-2">Phone :</label>
        <input type="text" class="form-control my-1"  placeholder="write Full Name" wire:model="phone">
        @error('phone')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <label class="h6 my-2">Tel :</label>
        <input type="text" class="form-control my-1"  placeholder="write tel" wire:model="tel">
        @error('tel')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <label class="h6 my-2">Email :</label>
        <input type="email" class="form-control my-1"  placeholder="write email" wire:model="email">
        @error('email')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <label class="h6 my-2">Address :</label>
        <textarea class="form-control" wire:model="address" rows="3"  placeholder="write address"></textarea>
        @error('address')
        <div class="text-danger">{{ $message }}</div>
        @enderror
        <label class="h6 my-2">Note :</label>
        <textarea class="form-control my-1" wire:model="note" rows="3"  placeholder="write note"></textarea>
        @error('note')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
@endif