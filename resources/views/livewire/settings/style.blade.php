@if($settings == 'style')
<div class="d-flex mt-3"> 
    <div class="d-flex flex-fill flex-column align-middle mx-2 ">
        <label  class="form-label flex-fill text-start h6 mx-2">Style :</label>
        <select class="form-select w-100"  wire:model.live="style">
            <option selected value="square">square</option>
            <option value="dot">dot</option>
            <option value="round">round</option>
        </select>
    </div>
    <div class="d-flex flex-fill flex-column align-middle ">
        <label  class="form-label text-start h6 mx-2">eye :</label>
        <select class="form-select w-100"  wire:model.live="eye">
            <option selected value="square">square</option>
            <option value="circle">circle</option>
        </select>
    </div>
</div>
@endif