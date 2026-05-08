@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row pt-4 pb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div class="breadcrumb-main mb-0">
                    <h4 class="text-capitalize breadcrumb-title">รายละเอียด Supply: <span
                            class="text-primary">{{ $supply->supply_name }}</span></h4>
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
                                    <a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles"
                                        role="tab" aria-selected="true">
                                        <i class="uil uil-truck"></i> ข้อมูลรถ
                                        <span
                                            class="badge bg-primary ms-2">{{ count($vehicles ?? []) }}/{{ $supply->vehicle_limit }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="drivers-tab" data-bs-toggle="tab" href="#drivers" role="tab"
                                        aria-selected="false">
                                        <i class="uil uil-users-alt"></i> พนักงานขับรถ
                                        <span class="badge bg-info ms-2">{{ count($drivers ?? []) }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab"
                                        aria-selected="false">
                                        <i class="uil uil-info-circle"></i> ข้อมูลบริษัท
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-4">

                                <div class="tab-pane fade show active" id="vehicles" role="tabpanel"
                                    aria-labelledby="vehicles-tab">


                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fs-15 fw-500 text-light">
                                            จำนวนรถที่ลงทะเบียน:
                                             <span class="dm-tag tag-success tag-transparented fs-18">{{ count($vehicles ?? []) }}/{{ $supply->vehicle_limit }}</span>
                                        </div>

                                     
                                    </div>


                                    <div class="table-responsive">
                                        <table id="vehiclesTable" class="table table-bordered w-100">
                                            <thead style="background-color: #F3E3D0;">
                                                <tr>
                                                    <th>ทะเบียนรถ</th>
                                                    <th>ยี่ห้อ / รุ่น / เลขรถ</th>
                                                    <th>ประเภทรถ</th>
                                                    <th>สถานะ</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vehicles as $car)
                                                    <tr>
                                                        <td class="fw-bold">{{ $car->car_plate }}</td>
                                                        <td>{{ $car->car_brand }} / {{ $car->car_model }} /
                                                            {{ $car->car_number_record }}</td>
                                                        <td>{{ $car->type_name ?? '-' }}</td>
                                                        <td class="text-center">
                                                            @if ($car->status == '1' || $car->status == 'active')
                                                                <span class="badge bg-success rounded-pill">Active</span>
                                                            @else
                                                                <span class="badge bg-danger rounded-pill">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <ul
                                                                class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center gap-2">
                                                                <li>

                                                                    <a href="#" class="view text-info"
                                                                        title="จัดการรถ">
                                                                        <i class="uil uil-eye fs-20"></i>
                                                                    </a>
                                                                </li>
                                                             
                                                               
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

                                <div class="tab-pane fade" id="drivers" role="tabpanel" aria-labelledby="drivers-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="{{ route('drivers.create') }}" class="btn btn-info btn-sm text-white">
                                            <i class="uil uil-plus"></i> เพิ่มพนักงานขับรถ
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="driversTable" class="table table-bordered text-center w-100">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ-นามสกุล</th>
                                                    <th>เบอร์โทรศัพท์</th>
                                                    <th>เลขที่ใบขับขี่</th>
                                                    
                                                    <th>สถานะ</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($drivers as $driver)
                                                    <tr>
                                                        <td class="text-start">
                                                            {{ $driver->prefix }}{{ $driver->name }}
                                                            {{ $driver->lastname }}
                                                        </td>
                                                        <td>{{ $driver->phone ?? '-' }}</td>
                                                        <td>{{ $driver->driver_license_no ?? '-' }}</td>
                                                      
                                                        <td>
                                                            @if ($driver->driver_status == '1' || $driver->driver_status == 'active')
                                                                <span class="badge bg-success rounded-pill">Active</span>
                                                            @else
                                                                <span class="badge bg-danger rounded-pill">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <ul
                                                                class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-center gap-2">
                                                                <li>
                                                                    <a href="{{ route('drivers.show', $driver->driver_id) }}"
                                                            class="view text-info" title="จัดการรถและพนักงาน">
                                                            <i class="uil uil-eye fs-20"></i>
                                                        </a>
                                                                </li>
                                                                <li><a href="#" class="edit text-warning"><i
                                                                            class="uil uil-edit fs-20"></i></a></li>
                                                                <li><a href="#" class="remove text-danger"><i
                                                                            class="uil uil-trash-alt fs-20"></i></a></li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            @if ($supply->supply_logo)
                                                <img src="{{ asset($supply->supply_logo) }}"
                                                    class="img-thumbnail border-0 shadow-sm" style="max-width: 150px;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center mx-auto rounded shadow-sm"
                                                    style="width: 150px; height: 150px;">
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
                                                <div class="col-sm-9 text-primary fw-bold">{{ $supply->vehicle_limit }}
                                                    คัน</div>
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
    </div>
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete-car');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // ดึง URL และ Token 
                    const url = this.getAttribute('data-url');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content');

                    Swal.fire({
                        title: 'ยืนยันการลบข้อมูล?',
                        text: "ข้อมูลรถและการตรวจรถ จะถูกลบอย่างถาวร",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ใช่',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {


                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'กำลังลบข้อมูล...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'ลบสำเร็จ!',
                                            text: data.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire('ล้มเหลว!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('ข้อผิดพลาด!',
                                        'เกิดปัญหาในการเชื่อมต่อกับเซิร์ฟเวอร์',
                                        'error');
                                });
                        }
                    });
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#vehiclesTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [],
                columnDefs: [{
                    "orderable": false,
                    "targets": [4]
                }],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });

            $('#driversTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [],
                columnDefs: [{
                    "orderable": false,
                    "targets": [4]
                }],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
