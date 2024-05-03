
<div class="row my-4">
    <div class="col-12">
        <div class="row">
            @if ($qrxCodes)
            @foreach ($qrxCodes as $qrxCode)
            <div class="col-xxl-4 col-xl-6">
                <div class="card">
                    <div class="row g-3">
                        <div class="col-md-6 text-center">
                            
                            <div class="align-middle pt-2" >
                                <img class="rounded-start img-fluid h-100 object-cover" src="{{asset('storage/'.$qrxCode->path)}}" alt="{{$qrxCode->type}}">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- card -->
                            <div class="dropdown float-end">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" >
                                    <span class="text-muted m-1 fs-xl"><i class="mdi mdi-dots-vertical align-middle"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item fw-bold" href="{{ route('dashboard.qr.edit', $qrxCode->id) }}">Edit</a>
                                    <form action="{{ route('dashboard.qr.status', $qrxCode->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        @if ($qrxCode->status == 'active')
                                            <button type="submit" class="dropdown-item text-warning fw-bold" >Pause</button>
                                            @else
                                            <button type="submit" class="dropdown-item text-success fw-bold">active</button>
                                        @endif
                                    </form>
                                    <form action="{{ route('dashboard.qr.delete', $qrxCode->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger fw-bold">Delete</button>
                                    </form>
                                </div>
                                @include('Dashboard.sections.models.download-qrx')

                            </div>
                            <h2>

                                <span class="badge badge-gradient-info px-2 ">{{$qrxCode->type}}</span>
                            </h2>

                            <div class="card-header">
                                <h6 class="card-title mb-0">{{$qrxCode->name}} <span class="badge @if($qrxCode->status == 'active') bg-success @elseif($qrxCode->status == 'pause') bg-warning @else bg-danger @endif align-middle fs-3xs">{{$qrxCode->status}}</span></h6>
                            </div>
                            
                            <div class="card shadow-none border-end-md rounded-0 mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center align-middle gap-2">
                                        <h4 class="h1 text-success"><i class="ph ph-barcode"></i></h4> 
                                        <h4 class="fw-semibold mb-3 align-middle"><span class="counter-value" data-target="{{$qrxCode->scan_count }}">0</span></h4>
                                    </div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate fs-sm">Short Url : <a target="blank" href=" {{ route('qr.show', $qrxCode->code) }}"> {{ route('qr.show', $qrxCode->code) }}</a></p>
                                    <div class="mt-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-muted mb-0">last scan at : {{$qrxCode->updated_at->format('d/m/Y')}}</p>
                                            <a type="button" class="text-success text-end  fw-bold h3 " data-bs-toggle="modal" data-bs-target="#downloadModal{{$qrxCode->id}}">
                                                <i class="ph ph-download-simple"></i>
                                            </a>
                                            <a type="button" class="text-success text-end  fw-bold h3 " href="{{asset('storage/'.$qrxCode->path)}}" download>
                                                {{-- <i class="ph ph-download-simple"></i> --}}
                                            </a>
                                        </div>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                    </div>
                </div>
            </div> 
            @endforeach
            @endif


    </div>
</div>