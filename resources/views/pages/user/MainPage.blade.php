@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('user.veh_regis') }}" class="text-decoration-none">
                                <div class="mt-25 card card-md border-2 card-bordered card-default text-center"
                                    style=" border-color: #a071ff;background-color: #f8f4ff;">
                                    <div class="card-body">
                                        <div class="mb-3">
                                           <img src="{{asset('bus.png')}}" alt="" width="120px">
                                        </div>
                                        <span class="fs-24 fw-bold text-dark mb-1">ลงทะเบียนรถ</span>
                                        <p class="fs-20 text-muted mt-1">คลิกเพื่อกรอกข้อมูลทะเบียนรถ</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>


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
                                                        <td> <a href="{{route('veh.detail',[$item->veh_id])}}"> {{ $item->plate }} {{ $item->province }}</a></td>
                                                        <td> {{ $item->veh_type_name }} </td>
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
