<div class="row">
    <h4><span class="h2" ></span>{{$plan->title}}</h4>
    <div class="col-xl-6 col-sm-12">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="avatar-sm float-end">
                    <div class="avatar-title bg-success-subtle text-success fs-3xl rounded">
                        <i class="ph ph-rocket-launch"></i>
                    </div>
                </div>
                <h4><span class="counter-value" data-target="{{$qrxs}}"></span> used</h4>
                <p class="text-muted mb-4">Qrx Codes ({{$plan->qr_count}})</p>
                <p class="text-muted mb-0"><span class="badge bg-success-subtle text-success"><i class="bi bi-arrow-up"></i> {{$qrxs}}</span> vs last month</p>
            </div>
            <div class="progress progress-sm rounded-0" role="progressbar" aria-valuenow="{{$qrxs}}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-success" style="width: {{$qrxs/$plan->qr_count *100}}%"></div>
            </div>
        </div>
    </div><!--end col-->
    <div class="col-xl-6 col-sm-12">
        <div class="card overflow-hidden">
            <div class="card-body">
                <div class="avatar-sm float-end">
                    <div class="avatar-title bg-warning-subtle text-warning fs-3xl rounded">
                        <i class="ph ph-heartbeat"></i>
                    </div>
                </div>
                <h4><span class="counter-value" data-target="{{$totalDays-$countDay}}"></span>day left</h4>
                <p class="text-muted mb-4">Total count of days :({{$totalDays}} day) </p>
                <p class="text-muted mb-0"><span class="badge bg-success-subtle text-success">expire at : {{$endDate->format('d-m-Y')}}</span> </p>
            </div>
            <div class="progress progress-sm rounded-0" role="progressbar" aria-valuenow="{{$countDay}}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-warning" style="width: {{$countDay+1/$totalDays*100}}%"></div>
            </div>
        </div>
    </div>
</div>