@if($tab == 'message')
    <div class="p-3">
        <label class="form-label card-title d-block text-center">SMS</label>
        <label class="h6 my-2">Phone Number :</label>
        <input type="text" class="form-control my-1"  placeholder="write Phone Number" wire:model="phone">
        <label class="h6 my-2">Message :</label>
        <textarea class="form-control" wire:model="message" rows="3"></textarea>
    </div>
@endif