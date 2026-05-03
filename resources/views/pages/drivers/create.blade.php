 @php
     use App\Enums\Role;
     $role = Auth::user()->role;
 @endphp

 @section('title', 'ระบบตรวจมาตรฐานรถ')
 @section('description', 'ID Drives')
 @extends('layout.app')
 @push('styles')
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link rel="stylesheet"
         href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
 @endpush
 @section('content')
     <div class="container-fluid">
         <div class="row">
             <div class="col-lg-12">
                 <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                     <h4 class="text-capitalize">เพิ่มพนักงานขับรถ</h4>
                 </div>

                 <div class="card mb-50">
                     <div class="card-body">
                         <form action="{{ route('drivers.store') }}" method="POST">
                             @csrf
                             <div class="row">
                                 <!-- ข้อมูลส่วนตัว -->
                                 <div class="col-md-12">
                                     <h6 class="fw-bold text-primary">ข้อมูลส่วนตัว</h6>
                                 </div>
                                 <div class="border-top my-3"></div>

                                 <div class="row">
                                     @if ($role === Role::Staff)
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">บริษัทฯว่าจ้าง <span
                                                     class="text-danger">*</span></label>
                                             <select name="company_id" id="company_id" class="form-control select2"
                                                 required>
                                                 <option value="">--- พิมพ์ค้นหาชื่อบริษัท ---</option>
                                                 @foreach ($companies as $com)
                                                     <option value="{{ $com->company_id }}">{{ $com->company_name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">เลือก Supply <span
                                                     class="text-danger">*</span></label>
                                             <select name="supply_id" id="supply_id" class="form-control select2" required>
                                                 <option value="">--- รายชื่อ Supply ---</option>
                                             </select>
                                         </div>
                                     @elseif($role === Role::Company)
                                         <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">เลือก Supply <span
                                                     class="text-danger">*</span></label>
                                             <select name="supply_id" id="supply_id" class="form-control select2" required>
                                                 <option value="">--- รายชื่อ Supply ---</option>
                                                 @foreach ($supplies as $sup)
                                                     <option value="{{ $sup->sup_id }}">{{ $sup->supply_name }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     @elseif($role === Role::Supply)
                                         <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                         <input type="hidden" name="supply_id" value="{{ Auth::user()->supply_user_id }}">

                                     @endif
                                 </div>

                                 <div class="row">
                                     <div class="col-md-2 mb-3">
                                         <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                         <select name="prefix" class="form-control" required>
                                             <option value="นาย">นาย</option>
                                             <option value="นาง">นาง</option>
                                             <option value="นางสาว">นางสาว</option>
                                         </select>
                                     </div>
                                     <div class="col-md-5 mb-3">
                                         <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                         <input type="text" name="name" class="form-control" required>
                                     </div>
                                     <div class="col-md-5 mb-3">
                                         <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                         <input type="text" name="lastname" class="form-control" required>
                                     </div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เลขประจำตัวประชาชน <span
                                                 class="text-danger">*</span></label>
                                         <input type="text" name="id_card_no" class="form-control" maxlength="13"
                                             required>
                                         <div id="id-card-feedback" class="fs-12 mt-1"></div>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เบอร์โทรศัพท์</label>
                                         <input type="text" name="phone" class="form-control">
                                     </div>

                                     <!-- ข้อมูลการทำงาน -->
                                     <div class="col-md-12 mt-4">
                                         <h6 class="fw-bold text-primary">ข้อมูลการทำงานและ ใบขับขี่</h6>
                                     </div>
                                     <div class="border-top my-3"></div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เลขที่ใบอนุญาตขับขี่<span
                                                 class="text-danger">*</span></label>
                                         <input type="text" name="driver_license_no" class="form-control">
                                         <div id="license-feedback" class="fs-12 mt-1"></div>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">วันที่ใบขับขี่หมดอายุ</label>
                                         <input type="text" class="form-control date-th-input"
                                             data-hidden-id="real_license_date"
                                             placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 11/02/2570" maxlength="10">
                                         <input type="hidden" name="license_expire_date" id="real_license_date">
                                         <div class="fs-12 mt-1 date-feedback"></div>
                                     </div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">ทะเบียนรถที่ขับประจำ (ถ้ามี)</label>
                                         <input type="text" name="assigned_car_id" class="form-control">
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">วันที่บรรจุ/เริ่มงาน</label>
                                         <input type="text" class="form-control date-th-input"
                                             data-hidden-id="real_hire_date" placeholder="วว/ดด/ปปปป (พ.ศ.)"
                                             maxlength="10">
                                         <input type="hidden" name="hire_date" id="real_hire_date">
                                         <div class="fs-12 mt-1 date-feedback"></div>
                                     </div>

                                     <div class="col-md-12 mb-3">
                                         <label class="form-label">หมายเหตุเพิ่มเติม</label>
                                         <textarea name="remark" class="form-control" rows="3"></textarea>
                                     </div>

                                 </div>
                                 <div class="border-top my-3"></div>
                                 <div class="d-flex justify-content-end gap-2 mt-4">
                                     <a href="{{ route('drivers.index') }}" class="btn btn-light btn-squared">ยกเลิก</a>
                                     <button type="submit" class="btn btn-primary btn-squared">บันทึกข้อมูล</button>
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
    <script>
$(document).ready(function() {
    // ใช้ Class .date-th-input เพื่อรองรับหลายๆ ช่องในหน้าเดียว
    $('.date-th-input').on('input', function(e) {
        let input = $(this).val().replace(/[^0-9]/g, ''); 
        let formattedDate = '';
        
        // เติม Slash (/) อัตโนมัติ
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
        
        // ค้นหาช่องแจ้งเตือนและช่องซ่อนที่ "อยู่คู่กับช่องที่กำลังพิมพ์"
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
                feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง </span>');
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                hiddenInput.val('');
                feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง</span>');
                $(this).addClass('is-invalid').removeClass('is-valid');
            }
        } else {
            hiddenInput.val('');
            feedback.html('');
            $(this).removeClass('is-valid is-invalid');
        }

        // submit
        if($('.is-invalid').length > 0) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    });
});
</script>
     <script>
         $(document).ready(function() {
             // 1. เรียกใช้งาน Select2
             $('.select2').select2({
                 placeholder: "กรุณาเลือกข้อมูล",
                 theme: "bootstrap-5",
                 allowClear: true,
                 width: '100%' // ให้กว้างเต็มคอลัมน์
             });

             // 2. Logic เมื่อ Staff เปลี่ยนบริษัทแม่
             $('#company_id').on('change', function() {
                 let comId = $(this).val();
                 let supplySelect = $('#supply_id');

                 // ล้างข้อมูลในช่อง Supply ก่อน
                 supplySelect.empty().append('<option value="">--- กำลังโหลดข้อมูล ---</option>').trigger(
                     'change');

                 if (comId) {
                     $.ajax({
                         url: "{{ route('drivers.getSupplies') }}",
                         method: "GET",
                         data: {
                             company_id: comId
                         },
                         success: function(data) {
                             supplySelect.empty().append(
                                 '<option value="">--- เลือกหน่วยงานย่อย ---</option>');
                             $.each(data, function(key, val) {
                                 supplySelect.append('<option value="' + val.sup_id +
                                     '">' + val.supply_name + '</option>');
                             });
                             // สำคัญ: ต้องสั่ง trigger change เพื่อให้ Select2 อัปเดตหน้าตาตามข้อมูลใหม่
                             supplySelect.trigger('change');
                         }
                     });
                 } else {
                     supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>')
                         .trigger('change');
                 }
             });
         });
     </script>
     <script>
         $(document).ready(function() {

             // ฟังก์ชันหลักในการส่ง AJAX เช็คค่าซ้ำ
             function checkDuplicate(inputElement, feedbackElement, columnName) {
                 let val = inputElement.val();

                 if (val.length < 5) { // กำหนดขั้นต่ำที่อยากให้เริ่มเช็ค เช่น 5 หลักขึ้นไป
                     feedbackElement.html('');
                     return;
                 }

                 $.ajax({
                     url: "{{ route('drivers.checkDuplicate') }}",
                     method: "POST",
                     data: {
                         _token: "{{ csrf_token() }}",
                         column: columnName,
                         value: val
                     },
                     beforeSend: function() {
                         feedbackElement.html('<span class="text-info">กำลังตรวจสอบ...</span>');
                     },
                     success: function(response) {
                         if (response.exists) {
                             feedbackElement.html(
                                 '<span class="text-danger"><i class="uil uil-times-circle"></i> ข้อมูลนี้มีในระบบแล้ว</span>'
                             );
                             inputElement.addClass('is-invalid').removeClass('is-valid');
                             checkOverallStatus(); // ตรวจสอบสถานะปุ่มบันทึก
                         } else {
                             feedbackElement.html(
                                 '<span class="text-success"><i class="uil uil-check-circle"></i> สามารถใช้งานได้</span>'
                             );
                             inputElement.addClass('is-valid').removeClass('is-invalid');
                             checkOverallStatus();
                         }
                     }
                 });
             }

             // ฟังก์ชันคุมปุ่มบันทึก (ถ้ามีช่องไหน error ให้กดบันทึกไม่ได้)
             function checkOverallStatus() {
                 if ($('.is-invalid').length > 0) {
                     $('button[type="submit"]').attr('disabled', true);
                 } else {
                     $('button[type="submit"]').attr('disabled', false);
                 }
             }

             // เมื่อพิมพ์เลขบัตรประชาชน
             $('#id_card_no').on('blur', function() {
                 checkDuplicate($(this), $('#id-card-feedback'), 'id_card_no');
             });

             // เมื่อพิมพ์เลขใบขับขี่
             $('#driver_license_no').on('blur', function() {
                 checkDuplicate($(this), $('#license-feedback'), 'driver_license_no');
             });
         });
     </script>
 @endpush
