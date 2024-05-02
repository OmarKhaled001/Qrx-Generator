<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{$title}}</h4>
            
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Dashboard</li>
                    <li class="breadcrumb-item active"> {{$title}} </li>
                </ol>
            </div>
            
        </div>
    </div>
</div>
<a class="btn btn-success my-2" href="{{route('dashboard.qr.create')}}">Create New Qr</a>
