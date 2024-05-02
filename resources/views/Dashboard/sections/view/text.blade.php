@if($qrxCode->type == 'text')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-quote-left fa-2xl"></i>                                        
    </div>
</div>
<div class="d-flex align-items-center mt-5">
    <input readonly id="foo" class="form-control" value="{{$qrxCode->text->text}}" />
    <button class="btn btn-success mx-1" data-clipboard-text="{{$qrxCode->text->text}}"  >
        <i class="fa-solid fa-copy"></i>
    </button>
</div>
@endif