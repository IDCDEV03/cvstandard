@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

<div class="container-fluid">
    <div class="row pt-4 pb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="breadcrumb-main mb-0">
                <h4 class="text-capitalize breadcrumb-title">รายละเอียด Supply: <span class="text-primary">{{ $supply->supply_name }}</span></h4>
            </div>
            <a href="{{ route('company.supplies.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="uil uil-arrow-left"></i> ย้อนกลับ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 radius-xl">
                <div class="card-body">
                    
                    <div class="dm-tab tab-large">
                        <ul class="nav nav-tabs vertical-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info" role="tab" aria-selected="true">
                                    <i class="uil uil-info-circle"></i> ข้อมูลบริษัท
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-selected="false">
                                    <i class="uil uil-truck"></i> ข้อมูลรถ 
                                    <span class="badge bg-primary ms-2">{{ count($vehicles ?? []) }}/{{ $supply->vehicle_limit }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="drivers-tab" data-bs-toggle="tab" href="#drivers" role="tab" aria-selected="false">
                                    <i class="uil uil-users-alt"></i> พนักงานขับรถ
                                    <span class="badge bg-info ms-2">{{ count($drivers ?? []) }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-4">
                            
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3">
                                        @if($supply->supply_logo)
                                            <img src="{{ asset($supply->supply_logo) }}" class="img-thumbnail border-0 shadow-sm" style="max-width: 150px;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center mx-auto rounded shadow-sm" style="width: 150px; height: 150px;">
                                                <i class="uil uil-image text-muted fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="mb-3">{{ $supply->supply_name }}</h5>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">ที่อยู่:</div>
                                            <div class="col-sm-9">{{ $supply->supply_address ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">เบอร์โทรติดต่อ:</div>
                                            <div class="col-sm-9">{{ $supply->supply_phone ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">อีเมล:</div>
                                            <div class="col-sm-9">{{ $supply->supply_email ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">โควตารถ:</div>
                                            <div class="col-sm-9 text-primary fw-bold">{{ $supply->vehicle_limit }} คัน</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab">
                                <div class="d-flex justify-content-end mb-3">
                                    <a href="#" class="btn btn-secondary btn-sm">
                                        <i class="uil uil-plus"></i> ลงทะเบียนรถ
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table id="vehiclesTable" class="table table-bordered text-center w-100">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ทะเบียนรถ</th>
                                                <th>ยี่ห้อ / รุ่น</th>
                                                <th>ประเภทรถ</th>
                                                <th>สถานะ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vehicles as $car)
                                            <tr>
                                                <td class="fw-bold">{{ $car->car_plate }}</td>
                                                <td>{{ $car->car_brand }} / {{ $car->car_model }}</td>
                                                <td>{{ $car->car_type }}</td>
                                                <td>
                                                    @if($car->status == '1' || $car->status == 'active')
                                                        <span class="badge bg-success rounded-pill">Active</span>
                                                    @else
                                                        <span class="badge bg-danger rounded-pill">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center gap-2">
                                                        <li><a href="#" class="edit text-warning"><i class="uil uil-edit fs-20"></i></a></li>
                                                        <li><a href="#" class="remove text-danger"><i class="uil uil-trash-alt fs-20"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="drivers" role="tabpanel" aria-labelledby="drivers-tab">
                                <div class="d-flex justify-content-end mb-3">
                                    <a href="#" class="btn btn-info btn-sm text-white">
                                        <i class="uil uil-plus"></i> เพิ่มพนักงานขับรถ
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table id="driversTable" class="table table-bordered text-center w-100">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>เบอร์โทรศัพท์</th>
                                                <th>เลขที่ใบขับขี่</th>
                                                <th>อายุ / ประสบการณ์</th>
                                                <th>สถานะ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($drivers as $driver)
                                            <tr>
                                                <td class="text-start">
                                                    {{ $driver->ins_prefix }}{{ $driver->ins_name }} {{ $driver->ins_lastname }}
                                                </td>
                                                <td>{{ $driver->ins_phone ?? '-' }}</td>
                                                <td>{{ $driver->dl_number ?? '-' }}</td>
                                                <td>
                                                    ปีเกิด: {{ $driver->ins_birthyear ?? '-' }} <br>
                                                    <small class="text-muted">ประสบการณ์: {{ $driver->ins_experience ?? '-' }} ปี</small>
                                                </td>
                                                <td>
                                                    @if($driver->ins_status == '1' || $driver->ins_status == 'active')
                                                        <span class="badge bg-success rounded-pill">Active</span>
                                                    @else
                                                        <span class="badge bg-danger rounded-pill">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center gap-2">
                                                        <li><a href="#" class="edit text-warning"><i class="uil uil-edit fs-20"></i></a></li>
                                                        <li><a href="#" class="remove text-danger"><i class="uil uil-trash-alt fs-20"></i></a></li>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#vehiclesTable').DataTable({
            responsive: true,
            pageLength: 10,
            columnDefs: [{"orderable": false, "targets": [4]}],
            language: { search: "ค้นหา:", lengthMenu: "แสดง _MENU_ รายการ", info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", paginate: { next: "ถัดไป", previous: "ก่อนหน้า" } }
        });

        $('#driversTable').DataTable({
            responsive: true,
            pageLength: 10,
            columnDefs: [{"orderable": false, "targets": [5]}],
            language: { search: "ค้นหา:", lengthMenu: "แสดง _MENU_ รายการ", info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ", paginate: { next: "ถัดไป", previous: "ก่อนหน้า" } }
        });
    });
</script>
@endpush
 