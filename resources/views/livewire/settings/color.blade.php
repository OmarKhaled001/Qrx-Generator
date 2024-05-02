@if($settings == 'color')
<div class="d-flex mt-3">
    <div class="d-flex flex-fill flex-column align-middle mx-2 ">
        <label  class="form-label flex-fill text-start h6 mx-2">Color : </label>
        <input  class="form-control form-control-color w-100 " wire:click="color" type="color" wire:model.live="color" name="color">
    </div>
    <div class="d-flex flex-fill flex-column align-middle ">
        <label  class="form-label text-start h6 mx-2">Bg Color :</label>
        <input class="form-control form-control-color w-100" type="color"  wire:model.live="bgColor" name="bg_color">
    </div>
</div>
@endif