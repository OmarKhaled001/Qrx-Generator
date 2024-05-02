@if($qrxCode->type == 'url')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-link fa-2xl"></i>
    </div>
</div>
<div class="d-flex align-items-center mt-5">
    <input readonly id="foo" class="form-control" value="{{$qrxCode->url->url}}" />
    <a class="btn btn-success mx-1" href="{{$qrxCode->url->url}}" target="_blank"  >
        <i class="fa-solid fa-link"></i>
    </a>
    <button class="btn btn-success" data-clipboard-text="{{$qrxCode->url->url}}"  >
        <i class="fa-solid fa-copy"></i>
    </button>
</div>
@endif