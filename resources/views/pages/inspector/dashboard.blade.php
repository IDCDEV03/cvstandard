@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
    <style>
        .btn-inspect {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 28px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1677ff 0%, #0ea5e9 100%);
            box-shadow: 0 4px 16px rgba(22, 119, 255, 0.35);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-inspect:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 119, 255, 0.4);
            color: #fff;
        }

        .btn-inspect i {
            font-size: 22px;
            line-height: 1;
        }
    </style>
    <style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush
@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-end flex-wrap ms-auto">
                        <nav aria-label="breadcrumb">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex align-items-center gap-1 px-3 py-1"
                                    style="border-radius: 999px; border: 1px solid #dee2e6; background: #fff;">
                                    <span style="font-size: 14px; color: #8c9097;" id="clock-date"></span>
                                    <span style="color: #dee2e6; font-size: 13px;">·</span>
                                    <i class="uil uil-clock" style="font-size: 14px; line-height: 1; color: #8c9097;"></i>
                                    <span class="fw-600" style="font-size: 13px; font-variant-numeric: tabular-nums;"
                                        id="clock-time"></span>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-10 mb-20">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <a href="{{ route('inspection.index') }}" class="btn-inspect">
                            <i class="uil uil-truck"></i>
                            เริ่มตรวจรถ
                        </a>
                    </div>
                </div>
            </div>
        </div>


<div class="row">

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'passed']) }}" class="text-decoration-none">     
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'passed' ? 'border border-success border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-success">{{ $passedInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ตรวจผ่านแล้ว</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-success color-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-check-circle fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

      <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiting']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'waiting' ? 'border border-warning border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-warning">{{ $waitingInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">รอตรวจครั้งที่ 2</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-warning color-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-clock fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'failed']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'failed' ? 'border border-danger border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-danger">{{ $failedInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ไม่ผ่านการตรวจ</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-danger color-danger d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-times-circle fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'all' ? 'border border-primary border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-primary">{{ $totalInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ประวัติการตรวจทั้งหมด</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-primary color-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-clipboard-notes fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

         <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 mb-25 shadow-sm">
                    <div class="card-header border-0 pb-0 pt-25 px-25">
                        <span class="fs-20 fw-bold mb-0">รายการรถ</span>
                        <div class="card-extra">
                            <a href="#" class="btn btn-sm btn-light">ดูทั้งหมด</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table4 p-25">
                            <div class="table-responsive">
                               <table class="table table-default table-bordered mb-0" id="table-one">
    <thead class="table-info">
        <tr>
            <th class="text-sm fw-bold">#</th>
            <th class="text-sm fw-bold">ทะเบียนรถ</th>
            <th class="text-sm fw-bold">สถานะล่าสุด</th>
            <th class="text-sm fw-bold">ประวัติผลการตรวจ</th>
            <th class="text-sm fw-bold text-center">รายงาน (Report)</th>
            <th class="text-sm fw-bold">ประเภทรถ</th>
            <th>จัดการรถ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vehicles as $item)
            <tr>
                <td> {{ $loop->iteration }} </td>
                
                <!-- ทะเบียนรถ -->
                <td>
                    <a href="#" class="fw-bold fs-16">
                        {{ $item->car_plate }}
                    </a>
                </td>

                <!-- สถานะการตรวจ (โชว์เฉพาะครั้งล่าสุด) -->
                <td class="text-center">
                    @if ($item->inspect_count == 0)
                        <span class="text-muted small">ยังไม่มีประวัติ</span>
                    @elseif ($item->latest_record->chk_status === '1')
                        <span class="dm-tag tag-success tag-transparented fs-18">บันทึกสมบูรณ์</span>
                    @elseif ($item->latest_record->chk_status === '2')
                        <span class="dm-tag tag-warning tag-transparented fs-18">บันทึกแบบร่าง</span>
                    @endif
                </td>

                <!-- สรุปผลการตรวจ (วนลูปแสดงทุกครั้งที่ตรวจ) -->
                <td>
                    @if ($item->inspect_count > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($item->history as $index => $record)
                                <!-- เฉพาะที่ตรวจเสร็จแล้วถึงจะแสดงผลประเมิน -->
                                @if($record->chk_status == '1')
                                    <div class="border-bottom pb-1 mb-1">
                                        <small class="text-muted fw-bold">ครั้งที่ {{ $item->inspect_count - $index }} : </small>
                                        
                                        @if ($record->evaluate_status == 1)
                                            <span class="text-success fw-bold fs-14">ปกติ อนุญาตให้ใช้งานได้</span>
                                        @elseif ($record->evaluate_status == 2)
                                            <span class="text-warning fw-bold fs-14">ไม่ปกติ แต่ปฏิบัติงานได้</span>
                                        @elseif ($record->evaluate_status == 3)
                                            <span class="text-danger fw-bold fs-14">ไม่ปกติ ไม่อนุญาตให้ใช้งาน</span>
                                            @if ($record->next_inspect_date)
                                                <br><small class="text-danger"> กำหนดตรวจซ้ำ: {{ thai_date( \Carbon\Carbon::parse($record->next_inspect_date)->format('d/m/Y')) }}</small>
                                            @endif
                                        @endif
                                    </div>
                                @elseif($record->chk_status == '2')
                                     <div class="border-bottom pb-1 mb-1">
                                        <small class="text-muted fw-bold">ครั้งที่ {{ $item->inspect_count - $index }}: </small>
                                        <span class="text-muted fs-14">กำลังดำเนินการตรวจ...</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>

                <!-- รายงาน (วนลูปแสดงปุ่ม Report คู่กับสรุปผล) -->
                <td class="text-center">
                     @if ($item->inspect_count > 0)
                        <div class="d-flex flex-column gap-2 align-items-center">
                            @foreach($item->history as $index => $record)
                                <div class="border-bottom pb-1 mb-1 w-100 text-center">
                                    @if ($record->chk_status === '1')
                                        <a href="{{ route('inspection.report', $record->record_id) }}" class="btn btn-info btn-xs shadow-sm">
                                            <i class="uil uil-file-alt"></i> Report ครั้งที่ {{ $item->inspect_count - $index }}
                                        </a>
                                    @elseif ($record->chk_status === '2')
                                         @if (Auth::user()->user_id == $record->user_id)
                                            <a href="{{ route('inspection.step3', [$record->record_id]) }}" class="btn btn-secondary btn-default btn-squared btn-xs">
                                                ตรวจต่อ <i class="uil uil-arrow-right" style="font-size:12px;"></i>
                                            </a>
                                        @else
                                            <span class="badge bg-danger rounded-pill fs-11">ติดค้างโดย {{ $record->user_id }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small">ยังไม่มี Report</span>
                    @endif
                </td>

                <!-- ประเภทรถ -->
                <td> {{ $item->vehicle_type }} </td>
                
                <!-- จัดการรถ -->
                <td>
                    <a href="#" class="btn btn-outline-primary btn-xs">แก้ไขข้อมูลรถ</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function updateClock() {
            const now = new Date();
            const d = now.getDate();
            const m = now.getMonth();
            const y = now.getFullYear() + 543;
            const h = pad(now.getHours());
            const min = pad(now.getMinutes());
            const s = pad(now.getSeconds());

            document.getElementById('clock-time').textContent = `${h}:${min}:${s}`;
            document.getElementById('clock-date').textContent = `${d} ${thaiMonths[m]} ${y}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>

    <script>
        $(document).ready(function() {
            $('#table-one').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
