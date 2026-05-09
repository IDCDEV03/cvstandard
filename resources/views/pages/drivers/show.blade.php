@php $userRole = Auth::user()->role->value; @endphp
@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-20 mt-20">
                    <div class="card-body">
                        {{-- Topbar --}}
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-arrow-left me-1"></i> รายการพนักงานขับรถ
                                </a>
                                <span class="text-muted">/</span>
                                <span class="fw-500 fs-15">ข้อมูลพนักงานขับรถ</span>
                            </div>
                            @if (!in_array($userRole, ['company', 'supply']))
                                <a href="{{ route('drivers.edit', $driver->driver_id) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-edit me-1"></i>แก้ไขข้อมูล
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="row g-3">

                    {{-- ===== LEFT COLUMN ===== --}}
                    <div class="col-md-4">

                        {{-- ข้อมูลส่วนตัว --}}
                        <div class="card mb-3">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="fa fa-user text-muted fs-16"></i>
                                <span class="fw-500 fs-16">ข้อมูลส่วนตัว</span>
                            </div>
                            <div class="card-body text-center pb-3">

                                {{-- profile photo --}}
                                <div class="mb-3">
                                    <img src="{{ $driver->driver_profile ? Storage::url($driver->driver_profile) : asset('user.png') }}"
                                         alt="รูปประจำตัว"
                                         class="rounded"
                                         style="width:120px;height:120px;object-fit:cover;object-position:center top;
                                                box-shadow:0 0 0 3px #fff, 0 0 0 5px #dee2e6;
                                                image-rendering:-webkit-optimize-contrast;">
                                </div>

                                <div class="fs-20 fw-bold mb-1">
                                    {{ $driver->prefix }}{{ $driver->name }} {{ $driver->lastname }}
                                </div>
                                <div class="text-muted fs-16 mb-2">
                                    <i class="fa fa fa-building text-muted mt-1 flex-shrink-0"></i>
                                    {{ $driver->supply_name ?? '-' }}
                                </div>

                                {{-- สถานะ --}}
                                @if ($driver->driver_status == 1)
                                    <span class="dm-tag tag-success tag-transparented">
                                        <i class="fa fa-check-circle me-1 text-success"></i>ปกติ
                                    </span>
                                @else
                                    <span class="dm-tag tag-danger tag-transparented">
                                        <i class="fa fa-times-circle me-1"></i>พักงาน / ลาออก
                                    </span>
                                @endif

                                <hr class="my-3">

                                {{-- รายละเอียด --}}
                                <div class="text-start">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <i class="fa fa-credit-card text-muted fs-15 mt-1 flex-shrink-0"></i>
                                        <div>
                                            <div class="text-muted fs-16">เลขบัตรประชาชน</div>
                                            <div class="fs-16">{{ $driver->id_card_no ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <i class="fa fa-phone text-muted fs-15 mt-1 flex-shrink-0"></i>
                                        <div>
                                            <div class="text-muted fs-16">เบอร์โทรศัพท์</div>
                                            <div class="fs-16">{{ $driver->phone ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa fa-calendar text-muted fs-15 mt-1 flex-shrink-0"></i>
                                        <div>
                                            <div class="text-muted fs-16">วันที่เริ่มงาน</div>
                                            <div class="fs-16">
                                                {{ $driver->hire_date ? thai_date($driver->hire_date) : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- รถที่รับผิดชอบ --}}
                        <div class="card mb-25">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="fa fa-truck text-muted fs-16"></i>
                                <span class="fw-500 fs-14">รถที่รับผิดชอบ</span>
                            </div>
                            <div class="card-body">
                                @if ($driver->car_plate)
                                    <div class="d-flex align-items-center gap-3 p-2 rounded bg-light">
                                        <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0"
                                            style="width:38px;height:38px;background:#E6F1FB">
                                            <i class="fa fa-truck fs-18" style="color:#185FA5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-500 fs-14">{{ $driver->car_plate }}</div>
                                            <div class="text-muted fs-16">
                                                {{ $driver->car_brand ?? '' }}
                                                {{ $driver->car_model ? '· ' . $driver->car_model : '' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-muted fs-16 py-3">
                                        <i class="fa fa-circle-dashed fs-24 d-block mb-1"></i>
                                        ยังไม่ได้กำหนดรถประจำตัว
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end left col --}}

                    {{-- ===== RIGHT COLUMN ===== --}}
                    <div class="col-md-8">

                        {{-- ข้อมูลใบขับขี่ --}}
                        <div class="card mb-3">
                            <div class="card-header d-flex align-items-center gap-2">
                                <i class="fa fa-id-badge text-muted fs-16"></i>
                                <span class="fw-500 fs-16">ข้อมูลใบขับขี่</span>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0" style="table-layout:fixed">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted fs-16 ps-3" style="width:40%">เลขที่ใบขับขี่</td>
                                            <td class="fs-16 pe-3">{{ $driver->driver_license_no ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fs-16 ps-3">วันหมดอายุ</td>
                                            <td class="fs-16 pe-3">
                                                @if ($driver->license_expire_date)
                                                    @php
                                                        $expire = \Carbon\Carbon::parse($driver->license_expire_date);
                                                        $daysLeft = now()->diffInDays($expire, false);
                                                    @endphp
                                                    {{ thai_date($expire) }}
                                                    @if ($daysLeft < 0)
                                                        <span class="dm-tag tag-danger tag-transparented ms-2">
                                                            <i class="fa fa-alert-circle me-1"></i>หมดอายุแล้ว
                                                        </span>
                                                    @elseif($daysLeft <= 90)
                                                        <span class="dm-tag tag-warning tag-transparented ms-2">
                                                            <i class="fa fa-alert-triangle me-1"></i>ใกล้หมดอายุ
                                                        </span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- เอกสารแนบ --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa fa-files text-muted fs-16"></i>
                                    <span class="fw-500 fs-16">เอกสารแนบ</span>
                                    <span class="dm-tag tag-primary tag-transparented">
                                        {{ $documents->count() }} ไฟล์
                                    </span>
                                </div>
                                @if (!in_array($userRole, ['company', 'supply']))
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalUploadDoc">
                                        <i class="fa fa-upload me-1"></i>เพิ่มเอกสาร
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">

                                {{-- รายการเอกสารที่อัปโหลดแล้ว --}}
                                @if ($documents->count())
                                    <div class="fs-16 fw-500 text-muted text-uppercase mb-2" style="letter-spacing:.04em">
                                        เอกสารที่อัปโหลดแล้ว
                                    </div>
                                    @foreach ($documents as $doc)
                                        <div
                                            class="d-flex align-items-center gap-3 py-2
                                        border-bottom border-light-subtle">
                                            {{-- icon --}}
                                            <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0"
                                                style="width:34px;height:34px;
                                            background:{{ $doc->file_extension === 'pdf' ? '#FCEBEB' : '#E6F1FB' }}">
                                                <i class="fs-16 {{ $doc->file_extension === 'pdf' ? 'lar la-file-pdf' : 'lar la-file-alt' }}"
                                                    style="color:{{ $doc->file_extension === 'pdf' ? '#A32D2D' : '#185FA5' }}"
                                                    aria-hidden="true"></i>
                                            </div>
                                            {{-- info --}}
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fs-16 text-truncate">{{ $doc->doc_name }}</div>
                                                <div class="text-muted fs-16">
                                                    {{ number_format($doc->file_size / 1024, 1) }} KB
                                                </div>
                                            </div>
                                            {{-- actions --}}
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                @if ($doc->file_extension === 'pdf')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-view-doc"
                                                        data-url="{{ Storage::url($doc->file_path) }}"
                                                        data-name="{{ $doc->doc_name }}" title="ดูไฟล์">
                                                        <i class="fa fa-eye fs-14"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ Storage::url($doc->file_path) }}" download
                                                    class="btn btn-sm btn-outline-secondary" title="ดาวน์โหลด">
                                                    <i class="fa fa-download fs-14"></i>
                                                </a>
                                                @if (!in_array($userRole, ['company', 'supply']))
                                                    <form method="POST"
                                                        action="{{ route('drivers.documents.destroy', [$driver->driver_id, $doc->id]) }}"
                                                        onsubmit="return confirm('ยืนยันการลบเอกสารนี้?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="ลบ">
                                                            <i class="fa fa-trash fs-14"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted fs-16 py-3">
                                        <i class="fa fa-file-off fs-24 d-block mb-1"></i>
                                        ยังไม่มีเอกสาร
                                    </div>
                                @endif

                                {{-- เอกสารที่ยังขาด --}}
                                @php
                                    $uploadedTypes = $documents->pluck('doc_type')->toArray();
                                    $allTypes = [
                                        'medical' => 'ใบรับรองแพทย์',
                                        'license' => 'สำเนาใบขับขี่',
                                        'id_card' => 'สำเนาบัตรประชาชน',
                                        'cert' => 'Certificate การอบรม',
                                    ];
                                    $missingTypes = array_diff_key($allTypes, array_flip($uploadedTypes));
                                @endphp

                                @if (count($missingTypes))
                                    <div class="mt-3 p-3 rounded" style="background:var(--color-background-secondary)">
                                        <div class="fs-16 fw-500 text-muted mb-2">เอกสารที่ยังไม่ได้อัปโหลด</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($missingTypes as $label)
                                                <span class="dm-tag tag-warning tag-transparented">
                                                    <i
                                                        class="fa fa-exclamation-circle text-warning me-1"></i>{{ $label }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>{{-- end right col --}}
                </div>
            </div>

            {{-- ===== Modal Upload Document ===== --}}
            <div class="modal fade" id="modalUploadDoc" tabindex="-1" aria-labelledby="modalUploadDocLabel">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('drivers.documents.store', $driver->driver_id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fs-15" id="modalUploadDocLabel">
                                    <i class="fa fa-upload me-2"></i>เพิ่มเอกสาร
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label fs-16">ประเภทเอกสาร</label>
                                    <select name="doc_type" class="form-select form-select-sm" required>
                                        <option value="">-- เลือกประเภท --</option>
                                        <option value="medical">ใบรับรองแพทย์</option>
                                        <option value="license">สำเนาใบขับขี่</option>
                                        <option value="id_card">สำเนาบัตรประชาชน</option>
                                        <option value="cert">Certificate การอบรม</option>
                                        <option value="other">อื่นๆ</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="wrap-doc-name" style="display:none">
                                    <label class="form-label fs-16">ชื่อเอกสาร</label>
                                    <input type="text" name="doc_name" class="form-control form-control-sm"
                                        maxlength="200" placeholder="ระบุชื่อเอกสาร">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fs-16">ไฟล์เอกสาร</label>
                                    <input type="file" name="doc_file" id="modal-file-input" accept=".pdf,.docx"
                                        class="form-control form-control-sm" required>
                                    <div class="text-muted fs-16 mt-1">
                                        <i class="fa fa-info-circle me-1"></i>รองรับ PDF และ DOCX · สูงสุด 10 MB
                                    </div>
                                </div>

                                {{-- preview --}}
                                <div id="modal-preview" class="d-none p-2 rounded"
                                    style="background:var(--color-background-secondary)">
                                    <div class="d-flex align-items-center gap-2">
                                        <i id="modal-file-icon" class="fs-16 flex-shrink-0"></i>
                                        <span id="modal-file-name" class="fs-16 text-truncate flex-grow-1"></span>
                                        <span id="modal-file-size" class="fs-16 text-muted flex-shrink-0"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-upload me-1"></i>อัปโหลด
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== Modal View Document (PDF) ===== --}}
            <div class="modal fade" id="modalViewDoc" tabindex="-1" aria-labelledby="modalViewDocLabel">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content" style="height: 90vh;">
                        <div class="modal-header py-2">
                            <h6 class="modal-title fs-15" id="modalViewDocLabel">
                                <i class="fa fa-file-pdf me-2 text-danger"></i>
                                <span id="viewDocName"></span>
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0 flex-grow-1" style="overflow: hidden;">
                            <iframe id="viewDocFrame" src=""
                                style="width:100%; height:100%; border:none; display:block;"></iframe>
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('scripts')
            <script>
                (function() {
                    const MAX = 10 * 1024 * 1024;
                    const EXT = /\.(pdf|docx)$/i;

                    function iconClass(name) {
                        return /\.pdf$/i.test(name) ?
                            'fa fa-file-type-pdf' :
                            'fa fa-file-type-doc';
                    }

                    function iconColor(name) {
                        return /\.pdf$/i.test(name) ? '#A32D2D' : '#185FA5';
                    }

                    function fmtSize(b) {
                        return b < 1048576 ?
                            (b / 1024).toFixed(1) + ' KB' :
                            (b / 1048576).toFixed(1) + ' MB';
                    }

                    // --- แสดง/ซ่อน field ชื่อเอกสาร เฉพาะ other ---
                    document.querySelector('select[name="doc_type"]')
                        .addEventListener('change', function() {
                            document.getElementById('wrap-doc-name').style.display =
                                this.value === 'other' ? 'block' : 'none';

                            // ถ้าเลือก other ให้ required
                            var nameInput = document.querySelector('input[name="doc_name"]');
                            nameInput.required = this.value === 'other';
                        });

                    // --- preview ไฟล์ใน modal ---
                    document.getElementById('modal-file-input')
                        .addEventListener('change', function() {
                            var file = this.files[0];
                            var preview = document.getElementById('modal-preview');

                            if (!file) {
                                preview.classList.add('d-none');
                                return;
                            }

                            if (!EXT.test(file.name)) {
                                Swal.fire('ประเภทไฟล์ไม่ถูกต้อง',
                                    'รองรับเฉพาะ PDF และ DOCX เท่านั้น', 'warning');
                                this.value = '';
                                preview.classList.add('d-none');
                                return;
                            }
                            if (file.size > MAX) {
                                Swal.fire('ไฟล์ใหญ่เกินไป',
                                    'ขนาดไฟล์ต้องไม่เกิน 10 MB', 'warning');
                                this.value = '';
                                preview.classList.add('d-none');
                                return;
                            }

                            var icon = document.getElementById('modal-file-icon');
                            icon.className = iconClass(file.name) + ' fs-16 flex-shrink-0';
                            icon.style.color = iconColor(file.name);

                            document.getElementById('modal-file-name').textContent = file.name;
                            document.getElementById('modal-file-size').textContent = fmtSize(file.size);
                            preview.classList.remove('d-none');
                        });
                })();

                // Open PDF in view modal
                document.querySelectorAll('.btn-view-doc').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.getElementById('viewDocName').textContent = this.dataset.name;
                        document.getElementById('viewDocFrame').src = this.dataset.url;
                        new bootstrap.Modal(document.getElementById('modalViewDoc')).show();
                    });
                });

                // Clear iframe src on modal close to stop PDF rendering
                document.getElementById('modalViewDoc').addEventListener('hidden.bs.modal', function() {
                    document.getElementById('viewDocFrame').src = '';
                });
            </script>
        @endpush
