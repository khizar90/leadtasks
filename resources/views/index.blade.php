@extends('layouts1.base')
@section('title', 'Dashborad')
@section('main', 'Statistics Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <!-- Statistics -->
                <div class="col-lg-12 mb-4 col-md-12">
                    <div class="card card1">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title mb-0 fw-bold">Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 mb-4">
                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>Total Users</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $total }}</h4>
                                                    </div>
                                                </div>
                                                <span class="badge bg-label-primary rounded p-2">
                                                    <i class="ti ti-user ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              

                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>IOS Traffic</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $iosTraffic }}</h4>
                                                    </div>
                                                </div>
                                                <span class="badge bg-label-warning rounded p-2">
                                                    <i class="ti ti-user-exclamation ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="content-left">
                                                    <span>Android Traffic</span>
                                                    <div class="d-flex align-items-center my-1">
                                                        <h4 class="mb-0 me-2">{{ $androidTraffic }}</h4>
                                                    </div>
                                                </div>
                                                <span class="badge bg-label-warning rounded p-2">
                                                    <i class="ti ti-user-exclamation ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endsection
