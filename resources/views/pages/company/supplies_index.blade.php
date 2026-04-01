@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">จัดการ Supply ในเครือ</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <a href="{{ route('company.supplies.create') }}" class="btn btn-primary btn-sm btn-add">
                            <i class="uil uil-plus"></i> เพิ่ม Supply ใหม่
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-content">
                            <p>{{ session('success') }}</p>
                            <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert"
                                aria-label="Close">
                                <i class="uil uil-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="card mb-25">
                    <div class="card-body">
                        <div class="table-responsive">


                            <table id="supplyTable" class="table table-bordered">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th class="text-center"><span class="userDatatable-title">Logo</span></th>
                                        <th><span class="userDatatable-title">ชื่อบริษัทในเครือ</span></th>
                                        <th><span class="userDatatable-title">เบอร์โทร / อีเมล</span></th>
                                        <th><span class="userDatatable-title">โควตารถ</span></th>
                                        <th><span class="userDatatable-title">วันที่เริ่ม - หมดอายุ</span></th>
                                        <th><span class="userDatatable-title">สถานะ</span></th>
                                        <th class="text-center"><span class="userDatatable-title text-end">จัดการ</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplies as $item)
                                        <tr>
                                            <td>
                                                <div class="userDatatable-content d-flex justify-content-center">
                                                    @if ($item->supply_logo)
                                                        <img src="{{ asset($item->supply_logo) }}" class="rounded"
                                                            style="width: 40%; object-fit: cover;">
                                                    @else
                                                        <div class="bg-lighter d-flex align-items-center justify-content-center rounded"
                                                            style="width: 80px; height: 80px;">
                                                            <i class="uil uil-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <span
                                                        class="d-block fw-bold fs-16 text-dark">{{ $item->supply_name }}</span>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <span class="d-block">{{ $item->supply_phone ?? '-' }}</span>
                                                    <small class="text-muted">{{ $item->supply_email ?? '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    <span class="dm-tag tag-info tag-transparented fs-18">
                                                        {{ $item->vehicle_limit }} คัน
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content">
                                                    @if ($item->start_date && $item->expire_date)
                                                        <small class="d-block text-success">เริ่ม:
                                                            {{ thai_date($item->start_date) }}</small>
                                                        <small class="d-block text-danger">หมด:
                                                            {{ thai_date($item->expire_date) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="userDatatable-content d-inline-block">
                                                    @if ($item->supply_status == 'active')
                                                        <span
                                                            class="bg-opacity-success color-success rounded-pill userDatatable-content-status active">เปิดใช้งาน</span>
                                                    @else
                                                        <span
                                                            class="bg-opacity-danger color-danger rounded-pill userDatatable-content-status active">ปิดใช้งาน</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <ul
                                                    class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center">
                                                    <li>
                                                        <a href="#" class="edit">
                                                            <i class="uil uil-edit"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="remove"
                                                            onclick="return confirm('ยืนยันการลบสาขานี้?')">
                                                            <i class="uil uil-trash-alt"></i>
                                                        </a>
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
            $('#supplyTable').DataTable({
                responsive: true,
                columnDefs: [{
                    "orderable": false,
                    "targets": [0, 6]
                }],
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
