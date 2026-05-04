@section('title', 'รายการตรวจสอบสภาพรถ')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                <h4 class="text-capitalize">สถานะการตรวจสภาพรถ</h4>
            </div>


            <div class="row mb-4 mt-30">
    <div class="col-12 d-flex justify-content-center gap-10 flex-wrap">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
           class="btn btn-primary btn-squared fs-16 {{ $filter == 'all' ? 'active' : 'btn-default' }}">
            <i class="uil uil-clipboard-alt me-2"></i>รายการทั้งหมด <span class="badge bg-white text-dark ms-2">{{ $totalInspections }}</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'passed']) }}"
           class="btn btn-success btn-squared fs-16 {{ $filter == 'passed' ? 'active' : 'btn-default' }}">
            <i class="uil uil-check me-2"></i>รถตรวจผ่าน <span class="badge bg-white text-dark ms-2">{{ $passedInspections }}</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiting']) }}"
           class="btn btn-warning btn-squared fs-16 {{ $filter == 'waiting' ? 'active' : 'btn-default' }}">
            <i class="uil uil-clock me-2"></i>รอตรวจอีกครั้ง <span class="badge bg-white text-dark ms-2">{{ $waitingInspections }}</span>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'failed']) }}"
           class="btn btn-danger btn-squared fs-16 {{ $filter == 'failed' ? 'active' : 'btn-default' }}">
            <i class="uil uil-times me-2"></i>รถที่ตรวจไม่ผ่าน <span class="badge bg-white text-dark ms-2">{{ $failedInspections }}</span>
        </a>
    </div>
</div>

<!-- ตารางแสดงข้อมูล -->
<div class="card mb-50">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-default table-bordered mb-0" id="table-one">
                <thead class="table-info">
                    <tr>
                        <th class="text-sm fw-bold">#</th>
                        <th class="text-sm fw-bold">ทะเบียนรถ / สาขา</th>
                        <th class="text-sm fw-bold">สถานะล่าสุด</th>
                        <th class="text-sm fw-bold">ประวัติผลการตรวจ</th>
                        <th class="text-sm fw-bold text-center">รายงาน (Report)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $item)
                        <tr>
                            <td> {{ $loop->iteration }} </td>
                            
                            <!-- ทะเบียนรถ และ สาขา -->
                            <td>
                                <a href="#" class="fw-bold fs-16 text-primary">{{ $item->car_plate }}</a>
                                <br><small class="text-muted"><i class="uil uil-building"></i> {{ $item->supply_name ?? 'ไม่ระบุสาขา' }}</small>
                            </td>

                            <!-- สถานะการตรวจล่าสุด -->
                            <td class="text-center">
                                @if ($item->inspect_count == 0)
                                    <span class="text-muted small">ยังไม่มีประวัติ</span>
                                @elseif ($item->latest_record->chk_status === '1')
                                    <span class="dm-tag tag-success tag-transparented fs-18">บันทึกสมบูรณ์</span>
                                @elseif ($item->latest_record->chk_status === '2')
                                    <span class="dm-tag tag-warning tag-transparented fs-18">กำลังตรวจ...</span>
                                @endif
                            </td>

                            <!-- สรุปผลการตรวจ -->
                            <td>
                                @if ($item->inspect_count > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($item->history as $index => $record)
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
                                                            <br><small class="text-danger ms-4"> ซ่อมและตรวจซ้ำ: {{ thai_date( \Carbon\Carbon::parse($record->next_inspect_date)->format('d/m/Y')) }}</small>
                                                        @endif
                                                    @endif
                                                </div>
                                            @elseif($record->chk_status == '2')
                                                <div class="border-bottom pb-1 mb-1">
                                                    <small class="text-muted fw-bold">ครั้งที่ {{ $item->inspect_count - $index }}: </small>
                                                    <span class="text-warning fs-14">แบบร่าง / ยังไม่เสร็จสิ้น</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <!-- รายงาน (Report) -->
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
                                                    <span class="badge bg-light text-dark rounded-pill fs-11">รอ Inspector ส่งเรื่อง</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">ไม่มีเอกสาร</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="uil uil-box fs-24 d-block mb-2"></i>ไม่มีข้อมูลรถในสถานะนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
