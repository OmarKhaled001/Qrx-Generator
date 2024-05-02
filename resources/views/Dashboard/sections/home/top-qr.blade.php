<div class="row">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Top 3 Qrxs</h4>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <a class="btn btn-success  my-2 text-end" href="{{route('dashboard.qr.all')}}">View All</a>
                <a class="btn btn-success  my-2 text-end" href="{{route('dashboard.qr.create')}}">Create New Qr</a>
                <table class="table table-striped table-nowrap align-middle text-center mb-0 p-3">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Url</th>
                            <th scope="col">status</th>
                            <th scope="col">Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topQrxs as $topQrx)
                        <a><tr>
                            <td class="fw-medium"># {{$loop->iteration}}</td>
                            <td>{{$topQrx->name}}</td>
                            <td>{{$topQrx->type}}</td>
                            <td><a target="_blank" href="{{route('qr.show', $topQrx->code) }}"> {{route('qr.show', $topQrx->code) }}</a></td>
                            <td><span class="badge @if($topQrx->status == 'active') bg-success @elseif($topQrx->status == 'pause') bg-warning @else bg-danger @endif ">{{$topQrx->status}}</span></td>
                            <td><span class="badge bg-success">{{$topQrx->scan_count}}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- end card-body -->
    </div><!-- end card -->
</div>