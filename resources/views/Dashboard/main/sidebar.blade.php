<?php
use App\Models\QrxCode;
$qrxs = QrxCode::where('user_id', Auth()->user()->id)->get()->first();
$qrxsActive = QrxCode::where('user_id', Auth()->user()->id)->where('status','active')->count();
$qrxsPause = QrxCode::where('user_id', Auth()->user()->id)->where('status','pause')->count();
$qrxsExp = QrxCode::where('user_id', Auth()->user()->id)->where('status','exp')->count();

?>
    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <a href="{{route('home')}}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ asset('assets-dashboard/images/logo-sm.png') }}" alt="" height="38">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('assets-dashboard/images/logo-dark.png') }}" alt="" height="38">
                </span>
            </a>
            <a href="{{route('home')}}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ asset('assets-dashboard/images/logo-sm.png') }}" alt="" height="38">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('assets-dashboard/images/logo-light.png') }}" alt="" height="38">
                </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>
        <div id="scrollbar">
            <div class="container-fluid">

                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link menu-link {{Request::url() == route('dashboard')? 'active': ''}}" href="{{route('dashboard')}}">
                            <i class="ph ph-house"></i><span data-key="t-index">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link text-success" href="{{route('dashboard.qr.create')}}">
                            <i class="ph ph-plus-circle"></i> <span data-key="#">New Qrx</span>
                        </a>
                    </li>
                    @if ($qrxs)
                    <li class="menu-title"><span>My QR Codes</span></li>
                    
                    <li class="nav-item">
                        <a class="nav-link menu-link {{Request::url() == route('dashboard.qr.all')? 'active': ''}}" href="{{route('dashboard.qr.all')}}">
                            <i class="ph ph-list-bullets"></i> <span data-key="t-index">All <span class="badge bg-success mx-2"></span></span>
                        </a>
                    </li>
                    @if ($qrxsActive)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{Request::url() == route('dashboard.qr.active')? 'active': ''}}" href="{{route('dashboard.qr.active')}}">
                            <i class="ph ph-rocket"></i> <span data-key="t-active">Active  <span class="badge bg-success mx-2">{{$qrxsActive}}</span></span>
                        </a>
                    </li>
                    @endif
                    @if ($qrxsPause)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{Request::url() == route('dashboard.qr.pause')? 'active': ''}}" href="{{route('dashboard.qr.pause')}}">
                            <i class="ph ph-pause"></i> <span data-key="t-pause">Pause  <span class="badge bg-warning mx-2">{{$qrxsPause}}</span></span>
                        </a>
                    </li>
                    @endif
                    @if ($qrxsExp)
                    <li class="nav-item">
                        <a class="nav-link menu-link {{Request::url() == route('dashboard.qr.expire') ? 'active': ''}}" href="{{route('dashboard.qr.expire')}}">
                            <i class="ph ph-warning"></i> <span data-key="t-expired">Expired  <span class="badge bg-danger mx-2">{{$qrxsExp}}</span></span>
                        </a>
                    </li>
                    @endif

                    @endif

                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
