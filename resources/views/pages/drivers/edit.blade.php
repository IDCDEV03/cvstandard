@php
    use App\Enums\Role;
    $role = Auth::user()->role;
    $roleString = is_object($role) ? $role->value : $role;
@endphp

@section('title', 'แก้ไขข้อมูลพนักงานขับรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                <h4 class="text-capitalize">แก้ไขข้อมูลพนักงานขับรถ ({{ $driver->driver_id }})</h4>
            </div>

            <div class="card mb-50">
                <div class="card-body">
                    <form action="{{ route('drivers.update', $driver->driver_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- สำคัญสำหรับ Update -->
                        
                        <div class="row">
                            <div class="col-md-12"><h6 class="fw-bold text-primary">ข้อมูลส่วนตัว</h6></div>
                            <div class="border-top my-3"></div>

                            <!-- ส่วนจัดการ Role: Staff, Company, Supply -->
                            <div class="row">
                                @if ($roleString === 'staff' || $roleString === Role::Staff->value)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">บริษัทฯว่าจ้าง <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-control select2" required>
                                            <option value="">--- พิมพ์ค้นหาชื่อบริษัท ---</option>
                                            @foreach ($companies as $com)
                                                <option value="{{ $com->company_id }}" {{ $driver->company_id == $com->company_id ? 'selected' : '' }}>
                                                    {{ $com->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลือก Supply <span class="text-danger">*</span></label>
                                        <select name="supply_id" id="supply_id" class="form-control select2" required>
                                            <option value="">--- รายชื่อ Supply ---</option>
                                            @foreach ($staffSupplies as $sup)
                                                <option value="{{ $sup->sup_id }}" {{ $driver->supply_id == $sup->sup_id ? 'selected' : '' }}>
                                                    {{ $sup->supply_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($roleString === 'company' || $roleString === Role::Company->value)
                                    <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลือก Supply <span class="text-danger">*</span></label>
                                        <select name="supply_id" id="supply_id" class="form-control select2" required>
                                            <option value="">--- รายชื่อ Supply ---</option>
                                            @foreach ($supplies as $sup)
                                                <option value="{{ $sup->sup_id }}" {{ $driver->supply_id == $sup->sup_id ? 'selected' : '' }}>
                                                    {{ $sup->supply_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($roleString === 'supply' || $roleString === Role::Supply->value)
                                    <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                    <input type="hidden" name="supply_id" value="{{ Auth::user()->supply_user_id }}">
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                    <select name="prefix" class="form-control" required>
                                        <option value="นาย" {{ $driver->prefix == 'นาย' ? 'selected' : '' }}>นาย</option>
                                        <option value="นาง" {{ $driver->prefix == 'นาง' ? 'selected' : '' }}>นาง</option>
                                        <option value="นางสาว" {{ $driver->prefix == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                    </select>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ $driver->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" value="{{ $driver->lastname }}" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เลข ปชช. <span class="text-danger">*</span></label>
                                    <input type="text" name="id_card_no" id="id_card_no" value="{{ $driver->id_card_no }}" class="form-control" maxlength="13" required>
                                    <div id="id-card-feedback" class="fs-12 mt-1"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="text" name="phone" value="{{ $driver->phone }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สถานะการทำงาน</label>
                                    <select name="driver_status" class="form-control">
                                        <option value="1" {{ $driver->driver_status == 1 ? 'selected' : '' }}>ปกติ</option>
                                        <option value="2" {{ $driver->driver_status == 2 ? 'selected' : '' }}>ลาออก / พักงาน</option>
                                    </select>
                                </div>

                                {{-- รูปภาพประจำตัว --}}
                                <div class="col-md-12 mb-3 mt-2">
                                    <label class="form-label fw-500">รูปภาพประจำตัว</label>
                                    <div class="d-flex align-items-start gap-4 mt-1">
                                        <div class="flex-shrink-0">
                                            <img id="profile-preview-img"
                                                 src="{{ $driver->driver_profile ? Storage::url($driver->driver_profile) : asset('user.png') }}"
                                                 alt="รูปประจำตัว"
                                                 class="rounded border"
                                                 style="width:110px;height:110px;object-fit:cover;cursor:pointer;"
                                                 onclick="document.getElementById('profile-file-input').click()">
                                        </div>
                                        <div>
                                            <p class="text-muted fs-12 mb-2">รองรับ JPG, PNG, WEBP · ขนาดสูงสุด 5 MB<br>คลิกที่รูปหรือกดปุ่มด้านล่างเพื่อเปลี่ยน</p>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    onclick="document.getElementById('profile-file-input').click()">
                                                <i class="fa fa-upload me-1"></i> เปลี่ยนรูป
                                            </button>
                                            <input type="file" id="profile-file-input" name="driver_profile"
                                                   accept="image/jpeg,image/png,image/webp" class="d-none">
                                            <div id="profile-feedback" class="fs-12 mt-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ข้อมูลการทำงาน -->
                                <div class="col-md-12 mt-4"><h6 class="fw-bold text-primary">ข้อมูลการทำงานและ ใบขับขี่</h6></div>
                                <div class="border-top my-3"></div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่ใบอนุญาตขับขี่</label>
                                    <input type="text" name="driver_license_no" id="driver_license_no" value="{{ $driver->driver_license_no }}" class="form-control">
                                    <div id="license-feedback" class="fs-12 mt-1"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่ใบขับขี่หมดอายุ</label>
                                    <input type="text" class="form-control date-th-input" data-hidden-id="real_license_date" value="{{ $driver->license_expire_date_show }}" placeholder="วว/ดด/ปปปป (พ.ศ.)" maxlength="10">
                                    <input type="hidden" name="license_expire_date" id="real_license_date" value="{{ $driver->license_expire_date }}">
                                    <div class="fs-12 mt-1 date-feedback"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ทะเบียนรถที่ขับประจำ</label>
                                    <input type="text" name="assigned_car_id" value="{{ $driver->assigned_car_id }}" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่บรรจุ/เริ่มงาน</label>
                                    <input type="text" class="form-control date-th-input" data-hidden-id="real_hire_date" value="{{ $driver->hire_date_show }}" placeholder="วว/ดด/ปปปป (พ.ศ.)" maxlength="10">
                                    <input type="hidden" name="hire_date" id="real_hire_date" value="{{ $driver->hire_date }}">
                                    <div class="fs-12 mt-1 date-feedback"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">หมายเหตุเพิ่มเติม</label>
                                    <textarea name="remark" class="form-control" rows="3">{{ $driver->remark }}</textarea>
                                </div>
                            </div>

                            <div class="border-top my-3"></div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('drivers.index') }}" class="btn btn-light btn-squared">ยกเลิก</a>
                                <button type="submit" class="btn btn-warning btn-squared fw-bold">อัปเดตข้อมูล</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- สคริปต์จัดการวันที่ พ.ศ. (เหมือนหน้า Create เป๊ะๆ) -->
<script>
$(document).ready(function() {
    $('.date-th-input').on('input', function(e) {
        let input = $(this).val().replace(/[^0-9]/g, ''); 
        let formattedDate = '';
        if (input.length > 2) {
            formattedDate += input.substring(0, 2) + '/';
            if (input.length > 4) {
                formattedDate += input.substring(2, 4) + '/';
                formattedDate += input.substring(4, 8);
            } else {
                formattedDate += input.substring(2);
            }
        } else {
            formattedDate = input;
        }
        $(this).val(formattedDate);
        
        let feedback = $(this).siblings('.date-feedback');
        let hiddenInputId = $(this).data('hidden-id');
        let hiddenInput = $('#' + hiddenInputId);

        if (formattedDate.length === 10) {
            let parts = formattedDate.split('/');
            let day = parts[0];
            let month = parts[1];
            let yearBE = parseInt(parts[2]);

            if(yearBE > 2500) {
                let yearCE = yearBE - 543;
                let finalDateForDB = yearCE + '-' + month + '-' + day;
                hiddenInput.val(finalDateForDB);
                feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง</span>');
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                hiddenInput.val('');
                feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง</span>');
                $(this).addClass('is-invalid').removeClass('is-valid');
            }
        } else {
            // สำคัญ! ในหน้า Edit ถ้าผู้ใช้ลบวันที่จนหมด เราก็ลบค่าใน hidden ทิ้ง เพื่อให้ DB อัปเดตเป็น null ได้
            hiddenInput.val(''); 
            feedback.html('');
            $(this).removeClass('is-valid is-invalid');
        }

        if($('.is-invalid').length > 0) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    });
});
</script>

<!-- สคริปต์ Select2 และการเช็คซ้ำ -->
<script>
$(document).ready(function() {
    $('.select2').select2({ theme: "bootstrap-5", width: '100%' });

    $('#company_id').on('change', function() {
        let comId = $(this).val();
        let supplySelect = $('#supply_id');
        supplySelect.empty().append('<option value="">--- กำลังโหลดข้อมูล ---</option>').trigger('change');

        if (comId) {
            $.ajax({
                url: "{{ route('drivers.getSupplies') }}",
                method: "GET",
                data: { company_id: comId },
                success: function(data) {
                    supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>');
                    $.each(data, function(key, val) {
                        supplySelect.append('<option value="' + val.sup_id + '">' + val.supply_name + '</option>');
                    });
                    supplySelect.trigger('change');
                }
            });
        } else {
            supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>').trigger('change');
        }
    });

    // แนบ ID ปัจจุบันไปกับการเช็คข้อมูลซ้ำด้วย
    let currentDriverId = "{{ $driver->id }}"; 

    function checkDuplicate(inputElement, feedbackElement, columnName) {
        let val = inputElement.val();
        if (val.length < 5) {
            feedbackElement.html('');
            return;
        }
        $.ajax({
            url: "{{ route('drivers.checkDuplicate') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                column: columnName,
                value: val,
                ignore_id: currentDriverId // ส่ง ID ตัวเองไปยกเว้น
            },
            beforeSend: function() { feedbackElement.html('<span class="text-info">กำลังตรวจสอบ...</span>'); },
            success: function(response) {
                if (response.exists) {
                    feedbackElement.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ข้อมูลนี้มีในระบบแล้ว</span>');
                    inputElement.addClass('is-invalid').removeClass('is-valid');
                    checkOverallStatus();
                } else {
                    feedbackElement.html('<span class="text-success"><i class="uil uil-check-circle"></i> สามารถใช้งานได้</span>');
                    inputElement.addClass('is-valid').removeClass('is-invalid');
                    checkOverallStatus();
                }
            }
        });
    }

    function checkOverallStatus() {
        if ($('.is-invalid').length > 0) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    }

    $('#id_card_no').on('blur', function() {
        checkDuplicate($(this), $('#id-card-feedback'), 'id_card_no');
    });

    $('#driver_license_no').on('blur', function() {
        checkDuplicate($(this), $('#license-feedback'), 'driver_license_no');
    });
});
</script>
<script>
(function() {
    var fileInput  = document.getElementById('profile-file-input');
    var previewImg = document.getElementById('profile-preview-img');
    var feedback   = document.getElementById('profile-feedback');
    var MAX_IMG    = 5 * 1024 * 1024;

    fileInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            Swal.fire('ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์รูปภาพเท่านั้น', 'warning');
            this.value = '';
            return;
        }
        if (file.size > MAX_IMG) {
            Swal.fire('ไฟล์ใหญ่เกินไป', 'ขนาดรูปต้องไม่เกิน 5 MB', 'warning');
            this.value = '';
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            feedback.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> พร้อมอัปโหลด — กด "อัปเดตข้อมูล" เพื่อบันทึก</span>';
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush