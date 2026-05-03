@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            
            <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                <h4 class="text-capitalize">จัดการพนักงานขับรถ</h4>
            </div>

            <!-- แสดงข้อความแจ้งเตือน -->
            @if(session('success'))
                <div class="alert alert-success bg-success text-white border-0" role="alert">
                    <div class="alert-icon"><i class="uil uil-check-circle"></i></div>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card mb-50">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fs-18 fw-bold">รายชื่อพนักงานขับรถทั้งหมด</span>
                    <a href="{{ route('drivers.create') }}" class="btn btn-primary btn-sm btn-squared">
                        <i class="uil uil-plus"></i> เพิ่มพนักงานขับรถ
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered " id="forms-table">
                            <thead class="table-warning">
                                <tr>
                                    <th>#</th>
                                    <th>รหัสพนักงาน</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>เลขบัตร ปชช.</th>
                                    <th>เบอร์โทรศัพท์</th>
                                    <th>สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drivers as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="fw-bold">{{ $item->driver_id }}</span></td>
                                        <td>{{ $item->prefix }}{{ $item->name }} {{ $item->lastname }}</td>
                                        <td>{{ $item->id_card_no }}</td>
                                        <td>{{ $item->phone ?? '-' }}</td>
                                        <td>
                                            @if($item->driver_status == 1)
                                                <span class="dm-tag tag-success tag-transparented fs-14">ปกติ</span>
                                            @else
                                                <span class="dm-tag tag-danger tag-transparented fs-14">ลาออก/พักงาน</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center gap-2">
                                                <li>
                                                    <a href="{{ route('drivers.edit', $item->id) }}" class="btn btn-warning btn-xs shadow-sm" title="แก้ไข">
                                                        <i class="uil uil-edit"></i> แก้ไข
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('drivers.destroy', $item->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบข้อมูลพนักงานขับรถท่านนี้?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-xs shadow-sm border-0" title="ลบ">
                                                            <i class="uil uil-trash-alt"></i> ลบ
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
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
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#forms-table').DataTable({
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