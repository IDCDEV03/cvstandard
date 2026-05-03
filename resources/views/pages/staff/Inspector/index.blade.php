@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <!-- Hexadash Breadcrumb & Header -->
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main user-member justify-content-sm-between ">
                <div class=" d-flex flex-wrap justify-content-center breadcrumb-main__wrapper">
                    <div class="d-flex align-items-center user-member__title justify-content-center me-sm-25">
                        <h4 class="text-capitalize fw-500 breadcrumb-title">จัดการข้อมูลช่างตรวจ</h4>
                    </div>
                </div>
                
                    <a href="{{ route('staff.inspectors.create') }}" class="btn btn-lg btn-secondary">
                        <i class="las la-plus fs-16"></i> เพิ่มช่างใหม่
                    </a>
                
            </div>
        </div>
    </div>

    <!-- แจ้งเตือนเมื่อทำงานสำเร็จ -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-25" role="alert">
            <div class="alert-content">
                <p><strong>สำเร็จ!</strong> {{ session('success') }}</p>
                <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                    <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg" aria-hidden="true">
                </button>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-25">
                <div class="card-body p-0">
                    <div class="table4  p-25 mb-30">
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-hover" id="forms-table">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th class="text-center">#</th>
                                        <th>ชื่อ - นามสกุล</th>
                                        <th>เบอร์โทร</th>
                                        <th>บริษัทฯว่าจ้าง </th>
                                        <th>ประเภทช่าง / สังกัด Supply</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inspectors as $key => $ins)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                           
                                            <td>                                                
                                                    {{ $ins->ins_prefix }}{{ $ins->ins_name }} {{ $ins->ins_lastname }}   
                                            </td>
                                        
                                            <td>  {{ $ins->ins_phone ?? '-' }}
                                            </td>

                                            <td>    {{ $ins->company_name ?? 'ไม่ระบุ' }}
                                            </td>                                            
                                           
                                            <td>
                                                <div class="userDatatable-content d-inline-block">
                                                    @if($ins->inspector_type == '1')
                                                        <span class="bg-opacity-primary color-primary rounded-pill userDatatable-content-status active">ช่างประจำบริษัทฯว่าจ้าง</span>
                                                    @elseif($ins->inspector_type == '2')
                                                        <span class="bg-opacity-info color-info rounded-pill userDatatable-content-status active mb-1">ช่างประจำ Supply</span>
                                                        <br><small class="text-dark fw-500"><i class="las la-building"></i> {{ $ins->supply_name ?? 'ไม่ระบุ' }}</small>
                                                    @elseif($ins->inspector_type == '3')
                                                        <span class="bg-opacity-warning color-warning rounded-pill userDatatable-content-status active">ช่าง นอกสังกัด (รับจ้าง)</span>
                                                    @else
                                                        <span class="bg-opacity-secondary color-secondary rounded-pill userDatatable-content-status active">ไม่ระบุ</span>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <!-- สถานะ -->
                                            <td class="text-center">
                                                <div class="userDatatable-content d-inline-block">
                                                    @if($ins->ins_status == '1')
                                                        <span class="bg-opacity-success color-success rounded-pill userDatatable-content-status active">ใช้งาน</span>
                                                    @else
                                                        <span class="bg-opacity-danger color-danger rounded-pill userDatatable-content-status active">ระงับ</span>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <!-- จัดการ (ปุ่ม Edit / Delete แบบ Hexadash) -->
                                            <td>
                                                 <ul
                                                    class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center">
                                                    <li>
                                                        <a href="#" class="edit" title="แก้ไข">
                                                            <i class="uil uil-edit"></i>
                                                        </a>
                                                    </li>
                                                    @if($ins->ins_status == '1')
                                                    <li>
                                                        <form action="{{ route('staff.inspectors.destroy', $ins->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะระงับการใช้งานช่างคนนี้?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="remove bg-transparent border-0 p-0" title="ระงับใช้งาน">
                                                                <i class="uil uil-trash-alt text-danger"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="userDatatable-content text-muted">ไม่พบข้อมูลช่างตรวจในระบบ</div>
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