@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h5 class="text-capitalize breadcrumb-title">{{ $companyDetails->company_name ?? $user->name }}</h5>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <span class="fs-24 fw-bold" style="color: #0609aa;">รายงานระบบตรวจมาตรฐานรถ</span>
                <br>
                <span class="fs-18 text-warning fw-bold">ข้อมูล ณ วันที่ {{ thai_date(now(), true) }}</span>
                <div class="border-top my-3"></div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
    Donut Charts Row
───────────────────────────────────────── --}}
        <div class="row mb-25">

            {{-- Vehicle Chart Card --}}
            <div class="col-xxl-6 col-lg-6 col-12 mb-25">
                <div class="card border-0 box-shadow-none">
                    <div class="card-header">
                        <h6 class="fw-500">
                            <i class="uil uil-truck me-2 color-primary"></i>สถานะรถบรรทุก 2569
                        </h6>

                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-5 text-center">
                                <div id="vehicle-chart"></div>
                                <p class="fs-13 color-gray mt-10 mb-0">จำนวนรถทั้งหมด <strong
                                        class="color-dark">{{ $totalVehicles }} คัน</strong></p>
                            </div>
                            <div class="col-sm-7">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-primary me-10 rounded-circle"></span>
                                            <span class="fs-13 color-gray">จำนวนรถที่ตรวจ</span>
                                        </div>
                                        <span class="fs-14 fw-600 color-primary">{{ $totalInspected }} <small
                                                class="fw-400 color-gray">คัน</small></span>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-success me-10 rounded-circle"></span>
                                            <a href="#" class="fs-13 color-gray text-decoration-none">ตรวจผ่าน</a>
                                        </div>
                                        <span class="fs-14 fw-600 color-success">{{ $passCount }} <small
                                                class="fw-400 color-gray">{{ $passPercent }}%</small></span>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between py-10">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-danger me-10 rounded-circle"></span>
                                            <a href="#" class="fs-13 color-gray text-decoration-none">ตรวจไม่ผ่าน</a>
                                        </div>
                                        <span class="fs-14 fw-600 color-danger">{{ $failCount }} <small
                                                class="fw-400 color-gray">{{ $failPercent }}%</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Driver Chart Card --}}
            <div class="col-xxl-6 col-lg-6 col-12 mb-25">
                <div class="card border-0 box-shadow-none">
                    <div class="card-header">
                        <h6 class="fw-500">
                            <i class="uil uil-user-circle me-2 color-success"></i>พนักงานขับรถ
                        </h6>

                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-5 text-center">
                                <div id="driver-chart"></div>
                                <p class="fs-13 color-gray mt-10 mb-0">
                                    จำนวนพนักงานทั้งหมด
                                    <strong class="color-dark">{{ $totalDrivers }} คน</strong>
                                </p>
                            </div>
                            <div class="col-sm-7">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-info me-10 rounded-circle"></span>
                                            <span class="fs-13 color-gray">พนักงานขับรถ</span>
                                        </div>
                                        <span class="fs-14 fw-600 color-info">
                                            {{ $totalDrivers }}
                                            <small class="fw-400 color-gray">คน</small>
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between py-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-success me-10 rounded-circle"></span>
                                            <span class="fs-13 color-gray">ผ่านการอบรม</span>
                                        </div>
                                        <span class="fs-14 fw-600 color-success">
                                            {{ $certDriverIds }}
                                            <small class="fw-400 color-gray">{{ $certPercent }}%</small>
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between py-10">
                                        <div class="d-flex align-items-center">
                                            <span class="bullet bullet-sm bg-danger me-10 rounded-circle"></span>
                                            <span class="fs-13 color-gray">ไม่มีข้อมูล</span>
                                        </div>
                                        <span class="fs-14 fw-600 color-danger">
                                            {{ $noCertDrivers }}
                                            <small class="fw-400 color-gray">{{ $noPercent }}%</small>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
    Action Buttons
───────────────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-center gap-10 flex-wrap">
                <a href="{{ route('company.vehicles.inform') }}" class="btn btn-primary btn-default btn-squared fs-16">
                    <i class="uil uil-clipboard-alt me-2"></i>รายการทั้งหมด
                </a>
                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'passed']) }}"
                    class="btn btn-success btn-squared fs-16 {{ request('filter') == 'passed' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-check me-2"></i>รถตรวจผ่าน
                </a>

                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'waiting']) }}"
                    class="btn btn-warning btn-squared fs-16 {{ request('filter') == 'waiting' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-clock me-2"></i>รอตรวจอีกครั้ง
                </a>

                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'failed']) }}"
                    class="btn btn-danger btn-squared fs-16 {{ request('filter') == 'failed' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-times me-2"></i>รถที่ตรวจไม่ผ่าน
                </a>
            </div>
        </div>

        <div class="pt-4 pb-4">
            <div class="row mb-3">

                <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div class="application-task-icon wh-60 bg-opacity-primary-20 content-center">
                                <img class="svg" src="{{ asset('assets/img/svg/truck.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2>{{ $totalVehicles ?? 0 }}/{{ $totalVehicleLimit ?? 0 }}</h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">รถที่ลงทะเบียน</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('company.vehicles.list') }}"
                                class="btn btn-xs btn-outline-secondary rounded-pill">
                                รายการรถ
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div class="application-task-icon wh-60 bg-opacity-info-20 content-center">
                                <img class="svg" src="{{ asset('assets/img/svg/building.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2><a href="{{ route('company.supplies.index') }}"> {{ $supplyCount ?? 0 }} </a></h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">บริษัทในเครือ (Supply)</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('company.supplies.index') }}"
                                class="btn btn-xs btn-outline-info rounded-pill">
                                รายการ Supply
                            </a>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div
                                class="application-task-icon wh-60 bg-opacity-warning-20
                            content-center">
                                <img class="svg" src="{{ asset('assets/img/svg/users-01.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2><a href="{{ route('drivers.index') }}">{{ $driverCount ?? 0 }} </a></h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">พนักงานขับรถ</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('drivers.index') }}" class="btn btn-xs btn-outline-dark rounded-pill">
                                รายการพนักงานขับรถ
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-25 border-0 radius-xl">

                        <div class="card-header border-bottom">
                            <h6 class="mb-0">Developed by</h6>
                        </div>
                        <div class="card-body p-25">
                            <div class="d-flex align-items-center mb-3">

                                <img src="{{ asset('iddrives.png') }}" alt="Logo"
                                    class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                                <img src="{{ asset('tz.png') }}" alt="Logo" class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                                <img src="{{ asset('id_inspection.png') }}" alt="Logo"
                                    class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passData = {{ $passCount }};
            const failData = {{ $failCount }};
            const totalInspected = "{{ $totalInspected }}";

            const chartSeries = (passData === 0 && failData === 0) ? [0, 0] : [passData, failData];

            const vehicleChart = new ApexCharts(document.getElementById('vehicle-chart'), {
                chart: {
                    type: 'donut',
                    width: 180,
                    height: 180
                },
                series: chartSeries,
                labels: ['ผ่าน', 'ไม่ผ่าน'],
                colors: ['#36AE7C', '#EB5353'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'ที่ตรวจแล้ว',
                                    fontSize: '11px',
                                    color: '#9299B8',
                                    formatter: () => totalInspected // แสดงยอดรวมตรงกลาง
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: val => val + ' คัน'
                    }
                }
            });

            vehicleChart.render();


            const driverChart = new ApexCharts(document.getElementById('driver-chart'), {
                chart: {
                    type: 'donut',
                    width: 180,
                    height: 180
                },
                series: [{{ $certDriverIds }}, {{ $noCertDrivers }}],
                labels: ['ผ่าน', 'ไม่พบข้อมูล'],
                colors: ['#36AE7C', '#EB5353'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'ทั้งหมด',
                                    fontSize: '11px',
                                    color: '#9299B8',
                                    formatter: () => '{{ $totalDrivers }}'
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: val => val + ' คน'
                    }
                }
            });
            driverChart.render();

        });
    </script>
@endpush
