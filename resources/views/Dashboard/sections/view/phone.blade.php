@if($qrxCode->type == 'phone')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-phone-volume fa-2xl"></i>
        </div>
</div>
<div class="d-flex align-items-center mt-5">
<input  readonly id="foo" class="form-control" value="{{$qrxCode->phone->phone}}" />
<a class="btn btn-success mx-1" href="tel:{{$qrxCode->phone->phone}}"  >
    <i class="fa-solid fa-phone"></i>
</a>
</div>
@endif