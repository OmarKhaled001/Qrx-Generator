@if($qrxCode->type == 'email')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-envelope fa-2xl"></i>
    </div>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> To : </h5>
    <button class="btn text-success h1 text-end" data-clipboard-text="{{$qrxCode->email->email}}"  >
        <i class="fa-solid fa-copy"></i>
    </button>
</div>
<div class="d-flex align-items-center">
<input readonly id="foo" class="form-control" value="{{$qrxCode->email->email}}" />
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Subject : </h5>
</div>
<div class="d-flex align-items-center">
<input readonly  id="foo" class="form-control" value="{{$qrxCode->email->subject}}" />
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Body : </h5>
</div>
<textarea readonly id="foo" class="form-control"  >{{$qrxCode->email->body}}</textarea>
<a class="btn btn-success h1 text-center mt-5" href="mailto:{{$qrxCode->email->email}}?&subject={{$qrxCode->email->subject}}?&body={{$qrxCode->email->body}}" >Send <i class="fa-solid fa-paper-plane"></i></a>
@endif