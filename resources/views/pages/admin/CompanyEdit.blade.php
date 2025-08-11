@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">แก้ไขข้อมูลบริษัท</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-25">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif


                        <div class="tab-wrapper">
                            <div class="dm-tab tab-horizontal">
                                <ul class="nav nav-tabs vertical-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-v-1-tab" data-bs-toggle="tab" href="#tab-v-1"
                                            role="tab" aria-selected="true">ข้อมูลทั่วไป</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-v-2-tab" data-bs-toggle="tab" href="#tab-v-2"
                                            role="tab" aria-selected="false">เปลี่ยน Username</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-v-3-tab" data-bs-toggle="tab" href="#tab-v-3"
                                            role="tab" aria-selected="false">เปลี่ยนรหัสผ่าน</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-v-1" role="tabpanel"
                                        aria-labelledby="tab-v-1-tab">
                                        <form action="{{ route('admin.cp_update', ['id' => request()->id,'tab'=>'part1']) }}" method="POST">
                                            @csrf

                                            @foreach ($company_detail as $data)
                                                <div class="mb-3">
                                                    <label>ชื่อบริษัท <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_name" class="form-control"
                                                        value="{{ $data->name }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label>ที่อยู่บริษัท <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="company_address" rows="3">{{ $data->company_address }}</textarea>
                                                </div>


                                                <div class="mb-3">
                                                    <label>จังหวัด <span class="text-danger">*</span></label>
                                                    <input type="text" name="province" class="form-control"
                                                        value="{{ $data->company_province }}">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                                            <input type="text" name="company_phone" class="form-control"
                                                                value="{{ $data->user_phone }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label>อีเมล (ถ้ามี)</label>
                                                            <input type="text" name="company_email" class="form-control"
                                                                value="{{ $data->email }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border-top my-3"></div>
                                            @endforeach
                                            <button type="submit"
                                                class="fs-18 btn btn-block btn-primary">บันทึกการแก้ไข</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="tab-v-2" role="tabpanel" aria-labelledby="tab-v-2-tab">
                                            <form action="{{ route('admin.cp_update', ['id' => request()->id,'tab'=>'part2']) }}" method="POST">
                                            @csrf
                                        <div class="mb-3 mt-3">
                                            <label class="mb-3">กำหนด Username ใหม่</label>
                                            <input type="text" name="company_user" id="company_user"
                                                class="form-control">
                                            <div id="username-alert" class="alert alert-danger mt-2" style="display: none;">
                                            </div>
                                        </div>
                                          <div class="border-top my-3"></div>
                                              <button type="submit"
                                                class="fs-18 btn btn-block btn-secondary">บันทึกการแก้ไข</button>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="tab-v-3" role="tabpanel" aria-labelledby="tab-v-3-tab">
                                           <form action="{{ route('admin.cp_update', ['id' => request()->id,'tab'=>'part3']) }}" method="POST">
                                            @csrf
                                        <div class="mb-3">
                                            <label class="mb-3">กำหนด Password ใหม่</label>
                                            <input type="text" name="company_password" class="form-control">
                                        </div>
                                          <div class="border-top my-3"></div>
                                            <div class="border-top my-3"></div>
                                              <button type="submit"
                                                class="fs-18 btn btn-block btn-secondary">บันทึกการแก้ไข</button>
                                        </form>
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
                $('#company_user').blur(function() {
                    var username = $(this).val();
                    if (!username) {
                        $('#username-alert').hide();
                        return;
                    }

                    $.ajax({
                        url: '/check-username',
                        method: 'GET',
                        data: {
                            company_user: username
                        },
                        success: function(data) {
                            if (data.exists) {
                                $('#username-alert')
                                    .text('Username นี้ถูกใช้แล้ว กรุณาใช้ชื่ออื่น')
                                    .show();
                            } else {
                                $('#username-alert').hide();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
