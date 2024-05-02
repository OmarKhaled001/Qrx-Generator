@if($tab == 'email')
    <div class="p-3">
        <label class="form-label card-title d-block text-center">Email</label>
        <label class="h6 my-2">To :</label>
        <input type="text" class="form-control my-1"  placeholder="write email" wire:model="email">
        <label class="h6 my-2">Subject :</label>
        <input type="text" class="form-control my-1"  placeholder="write subject" wire:model="subject">
        <label class="h6 my-2">Body :</label>
        <textarea class="form-control" wire:model="body" rows="3"></textarea>
    </div>
@endif