@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mt-4 mb-25">
                                <div class="card-header">
                                    <span class="fs-20 mb-0">รถที่ลงทะเบียน</span>
                                </div>
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <table class="table table-default table-bordered mb-0" id="table-one">
                                            <thead class="table-info">
                                                <tr>
                                                    <th class="text-sm fw-bold">#</th>
                                                    <th class="text-sm fw-bold">ทะเบียนรถ</th>
                                                    <th>ประเภทรถ</th>
                                                    <th class="text-sm fw-bold">วันที่ลงทะเบียน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($vehicles as $item)
                                                    <tr>
                                                        <td> {{ $loop->iteration }} </td>
                                                        <td> <a href="{{route('user.veh_detail',[$item->car_id])}}"> {{ $item->car_plate }} </a></td>
                                                        <td> {{ $item->vehicle_type }} </td>
                                                        <td> {{ thai_date($item->created_at) }} </td>
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
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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
