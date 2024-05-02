
@if($settings == 'size')
<div class="d-flex mt-3">
    <div class="d-flex flex-fill flex-column align-middle mx-2 ">
        <label  class="form-label flex-fill text-start h6 mx-2">Margin :</label>
        <select class="form-select w-100"  wire:model.live="margin">
            <option selected value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
    </div>
    <div class="d-flex flex-fill flex-column align-middle ">
        <label  class="form-label text-start h6 mx-2">Size :</label>
        <select class="form-select w-100"  wire:model.live="size">
            <option selected value="150">150</option>
            <option value="200">200</option>
            <option value="250">250</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
        </select>
    </div>
</div>
@endif