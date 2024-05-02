@if($qrxCode->type == 'vcard')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-address-card fa-2xl"></i>
    </div>
</div>
<div class="p-4 text-center">
    <div class="mx-auto avatar-xl mb-3">

        <img src="
            @if($qrxCode->getMedia('qrxCodes')->first() !== null)
                {{$qrxCode->getMedia('qrxCode')->first()->getUrl()}}
            @else
                {{ Avatar::create($qrxCode->vcard->full_name)->toBase64() }}
            @endif
        " alt="" class="img-fluid rounded-circle">
    </div>
    <h3 class="mb-1">{{$qrxCode->vcard->full_name}}</h3>
</div>


<div class="d-flex align-items-center mt-5">
    <h5> Email : </h5>
</div>
<div class="d-flex align-items-center">
    <input  readonly id="foo" class="form-control" value="{{$qrxCode->vcard->email}}" />
    <a class="btn btn-success mx-1" href="mailto:{{$qrxCode->vcard->email}}"  >
        <i class="fa-solid fa-envelope"></i>
    </a>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Phone : </h5>
</div>
<div class="d-flex align-items-center">
    <input readonly  type="tel" class="form-control" value="{{$qrxCode->vcard->phone}}" id="tel" />
    <a class="btn btn-success mx-1" href="tel:{{$qrxCode->vcard->phone}}"  >
        <i class="fa-solid fa-phone"></i>
    </a>
</div>
@if($qrxCode->vcard->tel != null)
<div class="d-flex align-items-center mt-5">
    <h5> Tel : </h5>
</div>
<div class="d-flex align-items-center">
    <input  readonly id="foo" class="form-control" value="{{$qrxCode->vcard->tel}}" />
    <a class="btn btn-success mx-1" href="tel:{{$qrxCode->vcard->tel}}"  >
        <i class="fa-solid fa-phone"></i>
    </a>
</div>
@endif
@if($qrxCode->vcard->address != null)
<div class="d-flex align-items-center mt-5">
    <h5> Address : </h5>
</div>
<textarea readonly id="foo" class="form-control">{{$qrxCode->vcard->address}}</textarea>
@endif
@if($qrxCode->vcard->note != null)
<div class="d-flex align-items-center mt-5">
    <h5>Note : </h5>
</div>
<textarea readonly id="foo" class="form-control">{{$qrxCode->vcard->note}}</textarea>
@endif
@endif