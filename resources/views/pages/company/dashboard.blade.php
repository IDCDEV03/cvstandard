@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <style>
        .report-header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 32px;
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
        }

        .report-date {
            font-size: 24px;
            color: #e67e22;
            /* สีส้มตามภาพ */
            margin-bottom: 10px;
        }

        .dashed-divider {
            border-top: 2px dashed #b0b0b0;
            margin: 0 auto 20px auto;
            width: 100%;
        }

        /* สไตล์สำหรับกราฟวงกลม Mockup */
        .mockup-chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        .chart-circle {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: conic-gradient(#6fd8f8 0% 50%, #66dd92 50% 85%, #f78049 85% 100%);
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chart-circle::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            background-color: #fff;
            border-radius: 50%;
        }

        .chart-icon {
            position: relative;
            z-index: 10;
            font-size: 55px;
            color: #333;
        }

        /* สไตล์สำหรับป้ายสถิติข้างกราฟ */
        .stat-badge {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #333;
            /* ขอบดำตามภาพ */
            margin-bottom: 10px;
            width: 200px;
            color: #000;
        }

        .bg-blue-custom {
            background-color: #6fd8f8;
        }

        .bg-green-custom {
            background-color: #66dd92;
        }

        .bg-red-custom {
            background-color: #f78049;
        }

        .stat-badge .text-part {
            font-size: 12px;
            line-height: 1.2;
        }

        .stat-badge .num-part {
            font-size: 24px;
            font-weight: bold;
        }

        .stat-badge .unit-part {
            font-size: 12px;
            font-weight: normal;
        }

        /* สไตล์สำหรับการ์ดด้านล่าง */
        .bottom-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            height: 100%;
            position: relative;
        }

        .bottom-card h2 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }

        .bottom-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 0;
        }

        .bottom-card .icon-top-right {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 20px;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">{{ $companyDetails->company_name ?? $user->name }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="report-header">
                    <div class="report-title">รายงานระบบตรวจมาตรฐานรถ</div>
                    <div class="report-date">ข้อมูล ณ วันที่ {{ thai_date(now(), true) }}</div>
                    <div class="dashed-divider"></div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="mockup-chart-container">
                    <div class="chart-circle">
                        <i class="uil uil-truck chart-icon"></i>
                    </div>
                    <div class="chart-stats">
                        <div class="stat-badge bg-blue-custom">
                            <div class="text-part">จำนวนรถ<br>ที่ตรวจ</div>
                            <div><span class="num-part">44</span> <span class="unit-part">คัน</span></div>
                        </div>
                        <div class="stat-badge bg-green-custom">
                            <div class="text-part">จำนวนรถ<br>ที่ตรวจ ผ่าน</div>
                            <div><span class="num-part">37</span> <span class="unit-part">คัน</span></div>
                        </div>
                        <div class="stat-badge bg-red-custom">
                            <div class="text-part">จำนวนรถ<br>ที่ตรวจ ไม่ผ่าน</div>
                            <div><span class="num-part">7</span> <span class="unit-part">คัน</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="mockup-chart-container">
                    <div class="chart-circle">
                        <i class="uil uil-user-md chart-icon"></i>
                    </div>
                    <div class="chart-stats">
                        <div class="stat-badge bg-blue-custom">
                            <div class="text-part">พนักงานขับรถ</div>
                            <div><span class="num-part">44</span> <span class="unit-part">คน</span></div>
                        </div>
                        <div class="stat-badge bg-green-custom">
                            <div class="text-part">ผ่านการอบรม</div>
                            <div><span class="num-part">37</span> <span class="unit-part">คน</span></div>
                        </div>
                        <div class="stat-badge bg-red-custom">
                            <div class="text-part">ไม่ผ่านการอบรม</div>
                            <div><span class="num-part">7</span> <span class="unit-part">คน</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-4">
            <div class="row mb-3">

                <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div class="application-task-icon wh-60 bg-opacity-success-20 content-center">
                                <img class="svg" src="{{ asset('assets/img/svg/check-clipboard.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2><a href="{{route('company.form.index')}}"> {{ $formCount ?? 0 }}/{{ $companyDetails->form_limit ?? 5 }} </a>
                                </h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">ฟอร์มตรวจที่สร้างแล้ว</span>
                            </div>
                        </div>
                        <div>
                            <a href="#" class="btn btn-xs btn-outline-success rounded-pill">
                                <i class="uil uil-plus"></i> เพิ่มฟอร์มใหม่
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
                            <a href="{{ route('company.supplies.create') }}"
                                class="btn btn-xs btn-outline-info rounded-pill">
                                <i class="uil uil-plus"></i> เพิ่ม Supply ใหม่
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div class="application-task-icon wh-60 bg-opacity-primary-20 content-center">
                                <img class="svg" src="{{ asset('assets/img/svg/truck.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2>{{ $registeredVehicleCount ?? 0 }}/{{ $totalVehicleLimit ?? 0 }}</h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">รถที่ลงทะเบียน</span>
                            </div>
                        </div>
                        <div>
                            <a href="#" class="btn btn-xs btn-outline-secondary rounded-pill">
                                <i class="uil uil-plus"></i> ลงทะเบียนรถ
                            </a>
                        </div>
                    </div>
                </div>

                    <div class="col-md-4 mb-3">
                    <div class="feature-cards5 d-flex flex-column align-items-start radius-xl p-25">
                        <div class="application-task d-flex align-items-center mb-3">
                            <div class="application-task-icon wh-60 bg-opacity-danger-20
                            content-center" >
                                <img class="svg" src="{{ asset('assets/img/svg/users-01.svg') }}" alt="">
                            </div>
                            <div class="application-task-content">
                                <h2>0</h2>
                                <span class="text-light fs-14 mt-1 text-capitalize">พนักงานขับรถ/ช่างตรวจ</span>
                            </div>
                        </div>
                        <div>
                            <a href="#" class="btn btn-xs btn-outline-dark rounded-pill">
                                <i class="uil uil-plus"></i> ลงทะเบียนพนักงาน
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-25 border-0 radius-xl">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0">ข้อมูลบริษัทเบื้องต้น</h6>
                        </div>
                        <div class="card-body p-25">
                            <div class="d-flex align-items-center mb-3">
                                @if (!empty($companyDetails->company_logo))
                                    <img src="{{ asset($companyDetails->company_logo) }}" alt="Logo"
                                        class="img-thumbnail me-3 border-0"
                                        style="max-height: 80px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3"
                                        style="width: 80px; height: 80px; border-radius: 8px;">
                                        <i class="uil uil-image text-muted fs-24"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-1">{{ $companyDetails->company_name ?? '-' }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="uil uil-map-marker"></i>
                                        {{ $companyDetails->company_province ?? 'ไม่ระบุจังหวัด' }}
                                    </p>
                                </div>
                            </div>
                            <hr class="border-light">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1 text-muted fs-14">วันเริ่มใช้งาน</p>
                                    <h6 class="mb-0">{{ thai_date($companyDetails->start_date) }}</h6>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <p class="mb-1 text-muted fs-14">วันสิ้นสุดการใช้งาน</p>
                                    <h6 class="mb-0">{{ thai_date($companyDetails->expire_date) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
