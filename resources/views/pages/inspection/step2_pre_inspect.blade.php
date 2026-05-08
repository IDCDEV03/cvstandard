@section('title', 'บันทึกข้อมูลก่อนตรวจ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
    <style>
        .photo-card {
            border: 2px dashed #E3E6EF !important;
            transition: all 0.2s;
        }

        .photo-card.has-photo {
            border-style: solid !important;
            border-color: ข้อมูลก่อนตรวจรถs #20C997 !important;
            background: #fff !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="d-flex align-items-center mb-4 mt-2">
                    <div class="bg-info text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="uil uil-clipboard-notes fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">ข้อมูลก่อนตรวจรถ</h5>
                        <span class="small text-muted">ขั้นตอนที่ 2 : กรอกข้อมูลก่อนตรวจรถ</span>
                    </div>
                </div>

                <form action="{{ route('inspection.storeStep2', $record->record_id) }}" method="POST"
                    enctype="multipart/form-data" id="step2Form">
                    @csrf

                    {{-- ============================================== --}}
                    {{-- Card 1: Update vehicle info (tax / register / insurance) --}}                   
                    {{-- ============================================== --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">                            
                                <div>
                                    <span class="fs-22 fw-bold">
                                        ทะเบียนรถ: <strong class="text-primary">{{ $vehicle->car_plate ?? '-' }}</strong>
                                    </span>
                                </div>
                            </div>
                          

                            <div class="row g-3">
                                {{-- Field 1: Car tax expire date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันหมดอายุภาษี
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_tax"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 11/02/2570"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_tax]" id="real_car_tax">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_tax ? thai_date($vehicle->car_tax) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
<div class="border-top"></div>
                                {{-- Field 2: Vehicle register date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันที่จดทะเบียน
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_register_date"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 15/06/2560"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_register_date]" id="real_car_register_date">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_register_date ? thai_date($vehicle->car_register_date) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
<div class="border-top"></div>
                                {{-- Field 3: Insurance expire date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันที่ประกันหมดอายุ
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_insurance_expire"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 20/12/2569"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_insurance_expire]" id="real_car_insurance_expire">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_insurance_expire ? thai_date($vehicle->car_insurance_expire) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================== --}}
                    {{-- Card 2: Pre-inspection fields (existing - unchanged) --}}
                    {{-- ============================================== --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3 mb-4">

                                @php $imgIndex = 1; @endphp

                                @foreach ($preFields as $field)
                                    @if ($field->field_type == 'image')
                                        @if ($imgIndex <= 8)
                                            <div class="col-6">
                                                <div class="card shadow-none radius-xs h-100 photo-card"
                                                    id="card_{{ $field->id }}">
                                                    <div class="card-body p-2 text-center position-relative d-flex flex-column align-items-center justify-content-center"
                                                        style="min-height: 140px;">
                                                        <span
                                                            class="badge bg-success position-absolute top-0 end-0 mt-2 me-2 d-none"
                                                            id="badge_{{ $field->id }}"><i
                                                                class="uil uil-check"></i></span>
                                                        <label for="input_{{ $field->id }}" class="w-100 h-100 m-0">
                                                            <img id="preview_{{ $field->id }}" src=""
                                                                class="img-fluid rounded d-none"
                                                                style="width: 100%; height: 120px; object-fit: cover;">
                                                            <div id="content_{{ $field->id }}">
                                                                <i class="uil uil-camera-plus fs-32 text-primary"></i>
                                                                <span
                                                                    class="fw-bold text-dark fs-14 d-block">{{ $field->field_label }}</span>
                                                            </div>
                                                        </label>
                                                        <input type="file" name="photos[{{ $imgIndex }}]"
                                                            id="input_{{ $field->id }}" class="d-none file-input"
                                                            accept="image/*" capture="environment"
                                                            data-id="{{ $field->id }}"
                                                            {{ $field->is_required ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            @php $imgIndex++; @endphp
                                        @endif
                                    @elseif($field->field_type == 'text')
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-bold text-dark mb-1">{{ $field->field_label }}
                                                {!! $field->is_required ? '<span class="text-danger">*</span>' : '' !!}</label>
                                            <input type="text" name="fields[{{ $field->id }}]"
                                                class="form-control radius-xs"
                                                placeholder="กรอก{{ $field->field_label }}..."
                                                {{ $field->is_required ? 'required' : '' }}>
                                        </div>
                                    @elseif($field->field_type == 'gps')
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                {{ $field->field_label }} {!! $field->is_required ? '<span class="text-danger">*</span>' : '' !!}
                                            </label>

                                            <button
                                                class="btn btn-outline-primary get-gps-btn radius-xs w-100 mb-2 py-2 shadow-none"
                                                type="button" data-id="{{ $field->id }}">
                                                <i class="uil uil-map-marker"></i> กดเพื่อบันทึกพิกัดปัจจุบัน
                                            </button>

                                            <input type="text" name="fields[{{ $field->id }}]"
                                                id="gps_{{ $field->id }}"
                                                class="form-control radius-xs bg-light border-0" readonly
                                                placeholder="พิกัดจะปรากฏที่นี่อัตโนมัติ..."
                                                {{ $field->is_required ? 'required' : '' }}>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="border-top my-3"></div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="btnNextStep"
                                    class="btn btn-info text-white btn-lg w-100 py-3 fw-bold radius-xs shadow-sm">
                                    ถัดไป <i class="uil uil-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
   @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ==========================================
                // Image preview handler (existing - unchanged)
                // ==========================================
                document.querySelectorAll('.file-input').forEach(input => {
                    input.addEventListener('change', function(e) {
                        const id = this.getAttribute('data-id');
                        if (this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('content_' + id).classList.add('d-none');
                                document.getElementById('preview_' + id).src = e.target.result;
                                document.getElementById('preview_' + id).classList.remove('d-none');
                                document.getElementById('badge_' + id).classList.remove('d-none');
                                document.getElementById('card_' + id).classList.add('has-photo');
                            }
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                });

                // ==========================================
                // GPS coordinate fetcher (existing - unchanged)
                // ==========================================
                document.querySelectorAll('.get-gps-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.getElementById('gps_' + id);

                        this.innerHTML = '<i class="uil uil-spinner fa-spin"></i> รอพิกัด...';

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                input.value = position.coords.latitude + ',' + position.coords.longitude;
                                btn.innerHTML = '<i class="uil uil-check"></i> สำเร็จ';
                                btn.classList.replace('btn-outline-primary', 'btn-success');
                            }, function(error) {
                                alert('ไม่สามารถดึงตำแหน่งได้ กรุณาเปิด Location Service ของมือถือครับ');
                                btn.innerHTML = '<i class="uil uil-map-marker"></i> ลองใหม่';
                            });
                        } else {
                            alert('เบราว์เซอร์นี้ไม่รองรับ GPS');
                        }
                    });
                });
            });
        </script>

        <script>
            // ==========================================
            // Thai Buddhist Date Input Handler
            // Format: dd/mm/yyyy (Buddhist Era) -> auto-converted to Y-m-d (CE) for DB
            // ==========================================
            $(document).ready(function() {

                // Helper: validate if date is real (handles Feb, leap year, 30/31 days)
                function isValidDate(day, month, yearCE) {
                    const d = new Date(yearCE, month - 1, day);
                    return d.getFullYear() === yearCE
                        && (d.getMonth() + 1) === month
                        && d.getDate() === day;
                }

                $('.date-th-input').on('input', function(e) {
                    let input = $(this).val().replace(/[^0-9]/g, '');
                    let formattedDate = '';

                    // Auto-insert slashes
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

                    // Find paired feedback element + hidden input
                    let feedback = $(this).closest('.col-12').find('.date-feedback');
                    let hiddenInputId = $(this).data('hidden-id');
                    let hiddenInput = $('#' + hiddenInputId);

                    // Empty input = clear (this field is optional)
                    if (formattedDate.length === 0) {
                        hiddenInput.val('');
                        feedback.html('');
                        $(this).removeClass('is-valid is-invalid');
                        toggleSubmitButton();
                        return;
                    }

                    // Full date entered (10 chars: dd/mm/yyyy)
                    if (formattedDate.length === 10) {
                        let parts = formattedDate.split('/');
                        let day = parseInt(parts[0]);
                        let month = parseInt(parts[1]);
                        let yearBE = parseInt(parts[2]);

                        // Validate: year must be Buddhist era (> 2400 to be safe)
                        if (yearBE < 2400 || yearBE > 2700) {
                            hiddenInput.val('');
                            feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง (เช่น 2570)</span>');
                            $(this).addClass('is-invalid').removeClass('is-valid');
                            toggleSubmitButton();
                            return;
                        }

                        // Convert BE -> CE
                        let yearCE = yearBE - 543;

                        // Validate real date (catches 31/02, 30/02, 32/13, etc.)
                        if (!isValidDate(day, month, yearCE)) {
                            hiddenInput.val('');
                            feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> วันที่ไม่ถูกต้อง</span>');
                            $(this).addClass('is-invalid').removeClass('is-valid');
                            toggleSubmitButton();
                            return;
                        }

                        // All checks passed - format for DB (Y-m-d)
                        let dayStr   = String(day).padStart(2, '0');
                        let monthStr = String(month).padStart(2, '0');
                        let finalDateForDB = yearCE + '-' + monthStr + '-' + dayStr;

                        hiddenInput.val(finalDateForDB);
                        feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง</span>');
                        $(this).addClass('is-valid').removeClass('is-invalid');
                    } else {
                        // Incomplete input (less than 10 chars but not empty)
                        hiddenInput.val('');
                        feedback.html('');
                        $(this).removeClass('is-valid is-invalid');
                    }

                    toggleSubmitButton();
                });

                // Disable submit button if any date input is invalid
                function toggleSubmitButton() {
                    if ($('.date-th-input.is-invalid').length > 0) {
                        $('#btnNextStep').attr('disabled', true);
                    } else {
                        $('#btnNextStep').attr('disabled', false);
                    }
                }
            });
        </script>
    @endpush

