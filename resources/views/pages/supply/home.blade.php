@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold breadcrumb-title">รายการตรวจรถ
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <table class="table table-bordered" id="forms-table">
                                <thead>
                                    <tr>
                                        <th>#</th>  
                                        <th>ทะเบียนรถ</th>
                                        <th>รายละเอียด</th>
                                        <th>ตรวจเมื่อ</th>
                                      
                                        <th>หมายเลขรถ</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($chk_list as $data)
                                        
                                  
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                     <td> {{$data->car_plate}} </td>
                                     <td>
                                        <a href="{{route('form_report',['rec'=>$data->record_id])}}" class="btn btn-xs btn-info btn-shadow-info">
                                                       ผลการตรวจ
                                                    </a>
                                     </td>
                                     <td>{{ thai_datetime($data->date_check) }}</td>
                                     <td> {{$data->car_number_record}} </td>
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
