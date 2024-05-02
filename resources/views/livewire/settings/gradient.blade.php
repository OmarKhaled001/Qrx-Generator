@if($settings == 'gradient')
<div class="d-flex mt-3">
    <div class="d-flex flex-fill flex-column align-middle mx-2 ">
        <label  class="form-label flex-fill text-start h6 mx-2">From :</label>
        <input  class="form-control form-control-color w-100 " type="color" wire:model.live="gradientFrom" name="gradient_from">
    </div>
    <div class="d-flex flex-fill flex-column align-middle ">
        <label  class="form-label text-start h6 mx-2">To :</label>
        <input class="form-control form-control-color w-100" type="color"  wire:model.live="gradientTo" name="gradient_to">
    </div>
</div>
@endif