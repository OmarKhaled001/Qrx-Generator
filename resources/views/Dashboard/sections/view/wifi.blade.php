@if($qrxCode->type == 'wifi')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-wifi fa-2xl"></i>
    </div>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Wireless Ssid : </h5>
</div>
<div class="d-flex align-items-center">
<input readonly id="foo" class="form-control" value="{{$qrxCode->wifi->wireless_ssid}}" />
<button class="btn btn-success " data-clipboard-text="{{$qrxCode->wifi->wireless_ssid}}"  >
    <i class="fa-solid fa-copy"></i>
</button>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Password : </h5>
</div>
<div class="d-flex align-items-center">
<div class="position-relative auth-pass-inputgroup w-100">
    <input readonly type="password" id="foo" class="form-control" value="{{$qrxCode->wifi->password}}" />
    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon " type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
</div>
<button class="btn btn-success " data-clipboard-text="{{$qrxCode->wifi->password}}"  >
    <i class="fa-solid fa-copy"></i>
</button>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Encryption : </h5>
</div>
<div class="d-flex align-items-center">
<input readonly  id="foo" class="form-control" value="{{$qrxCode->wifi->encryption}}" />
</div>
<a class="btn btn-success h1 text-center mt-5" href="WIFI:S:{{$qrxCode->wifi->wireless_ssid}};T:{{$qrxCode->wifi->encryption}};P:{{$qrxCode->wifi->password}};;" >Connect</a>
@endif