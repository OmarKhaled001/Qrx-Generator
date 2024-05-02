@if($qrxCode->type == 'message')
<div class="p-2 mt-5 text-center">
    <div class="h1">
        <i class="fa-solid fa-comment-sms fa-2xl"></i>
    </div>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Phone : </h5>
    <button class="btn text-success h1 text-end" data-clipboard-text="{{$qrxCode->message->phone}}"  >
        <i class="fa-solid fa-copy"></i>
    </button>
</div>
<div class="d-flex align-items-center">
<input  readonly id="foo" class="form-control" value="{{$qrxCode->message->phone}}" />
<a class="btn btn-success mx-1" href="tel:{{$qrxCode->message->phone}}"  >
    <i class="fa-solid fa-phone"></i>
</a>
</div>
<div class="d-flex align-items-center mt-5">
    <h5> Masseage : </h5><button class="btn text-success h1 text-end" data-clipboard-text="{{$qrxCode->message->message}}"  ><i class="fa-solid fa-copy"></i></button>
</div>
<textarea readonly  id="foo" class="form-control"  >{{$qrxCode->message->message}}</textarea>
<a class="btn btn-success h1 text-center mt-5" href="sms:{{$qrxCode->message->phone}}?&body={{$qrxCode->message->message}}">Send <i class="fa-solid fa-paper-plane"></i></a>
@endif