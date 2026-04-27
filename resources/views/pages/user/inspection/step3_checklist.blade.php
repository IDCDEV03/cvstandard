@section('title', 'การตรวจรถ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
    <style>
        /* ซ่อน Scrollbar ของเมนูหมวดหมู่ */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* ปรับแต่งปุ่มเลือกสถานะให้ดูละมุนและมีมิติ */
        .btn-status-custom {
            background-color: #F4F6F9;
            border: 1px solid #E4E7EC !important;
            color: #4A5568;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.25s ease;
        }

        .btn-status-custom:hover {
            background-color: #E2E8F0;
        }

        /* สถานะเมื่อกด "ผ่าน/ปกติ" (Value = 1) */
        .btn-check:checked+.btn-pass-custom {
            background-color: #10B981 !important;
            border-color: #10B981 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important;
        }

        /* สถานะเมื่อกด "ผ่าน แต่แก้ไข / ไม่ปกติฯ" (Value = 2) */
        .btn-check:checked+.btn-warning-custom {
            background-color: #F59E0B !important;
            border-color: #F59E0B !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important;
        }

        /* สถานะเมื่อกด "ไม่ผ่าน / ปรับปรุง" (Value = 0) */
        .btn-check:checked+.btn-fail-custom {
            background-color: #EF4444 !important;
            border-color: #EF4444 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important;
        }

        .checklist-card {
            border-radius: 12px !important;
            border: 1px solid #EAEDF2 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02) !important;
        }
    </style>
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">

                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border"
                    style="border-color: #EAEDF2 !important;">
                    <div class="d-flex flex-column">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i
                                class="uil uil-clipboard-notes text-primary me-2"></i>{{ $formGroup->form_group_name ?? 'แบบฟอร์มการตรวจ' }}
                        </h5>

                        <div class="mt-1" style="padding-left: 28px;">
                            <span class="fs-16 fw-bold text-primary">
                                {{ $vehicle->car_plate ?? 'ไม่ระบุ' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('user.inspection.step4', $record->record_id) }}"
                        class="btn btn-success btn-sm radius-xs shadow-sm fw-bold px-3">
                        สรุปผล <i class="uil uil-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="category-scroll-container mb-4 pb-2 border-bottom">
                    <div class="d-flex flex-nowrap overflow-auto hide-scrollbar" style="-webkit-overflow-scrolling: touch;">
                        @foreach ($categories as $cat)
                            <a href="{{ route('user.inspection.step3', ['record_id' => $record->record_id, 'cat_id' => $cat->category_id]) }}"
                                class="btn btn-sm btn-rounded me-2 text-nowrap fw-bold category-tab {{ $currentCategoryId == $cat->category_id ? 'active-tab' : '' }}">
                                {{ $cat->chk_cats_name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    @foreach ($items as $item)
                        @php
                            $result = $existingResults->get($item->item_id);
                            $status = $result ? $result->result_status : '';
                            $val = $result ? $result->result_value : '';
                            $comment = $result ? $result->user_comment : '';
                            $images = $existingImages->get($item->item_id) ?? collect();

                            // กำหนด Label ตามเงื่อนไขของคุณ
                            $isType2 = $item->item_type == 2;
                            $lblPass = $isType2 ? 'ปกติ' : 'ผ่าน';
                            $lblAlmost = $isType2 ? 'ไม่ปกติ แต่ยังสามารถใช้งานได้' : 'ผ่าน แต่แก้ไข';
                            $lblFail = $isType2 ? 'ปรับปรุง' : 'ไม่ผ่าน';
                        @endphp

                        <div class="card checklist-card">
                            <div class="card-body p-4">

                                <div class="mb-3">
                                    <div class="fw-bold text-dark lh-base fs-16">
                                        {{ $loop->iteration }}. {{ $item->item_name }}
                                    </div>

                                    @if (!empty($item->item_description))
                                        <div class="text-danger fs-14 mt-1">
                                            {!! nl2br(e($item->item_description)) !!}
                                        </div>
                                    @endif
                                </div>

                                <input type="hidden" class="data-record" value="{{ $record->record_id }}">
                                <input type="hidden" class="data-item" value="{{ $item->item_id }}">

                                @if (in_array($item->item_type, [1, 2, 6]))
                                    <div class="d-flex flex-column gap-2 mb-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="radio" class="btn-check btn-pass"
                                                    name="status_{{ $item->item_id }}" id="pass_{{ $item->item_id }}"
                                                    value="1" {{ $status === '1' ? 'checked' : '' }}>
                                                <label
                                                    class="btn btn-status-custom btn-pass-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2"
                                                    for="pass_{{ $item->item_id }}">
                                                    <i class="uil uil-check-circle fs-18"></i>
                                                    <span>{{ $lblPass }}</span>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" class="btn-check btn-fail"
                                                    name="status_{{ $item->item_id }}" id="fail_{{ $item->item_id }}"
                                                    value="0" {{ $status === '0' ? 'checked' : '' }}>
                                                <label
                                                    class="btn btn-status-custom btn-fail-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2"
                                                    for="fail_{{ $item->item_id }}">
                                                    <i class="uil uil-times-circle fs-18"></i>
                                                    <span>{{ $lblFail }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        @if ($item->item_type == 1 || $item->item_type == 2)
                                            <div class="w-100">
                                                <input type="radio" class="btn-check btn-warning"
                                                    name="status_{{ $item->item_id }}" id="almost_{{ $item->item_id }}"
                                                    value="2" {{ $status === '2' ? 'checked' : '' }}>
                                                <label
                                                    class="btn btn-status-custom btn-warning-custom w-100 py-2 fs-13 d-flex align-items-center justify-content-center gap-2"
                                                    for="almost_{{ $item->item_id }}">
                                                    <i class="uil uil-exclamation-triangle fs-18"></i>
                                                    <span>{{ $lblAlmost }}</span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if (in_array($item->item_type, [3, 4, 5, 6]))
                                    <div class="mb-2">
                                        @if ($item->item_type == 3 || $item->item_type == 6)
                                            <input type="text"
                                                class="form-control bg-light input-value radius-xs border-0"
                                                placeholder="ระบุข้อความ..." value="{{ $val }}">
                                        @elseif($item->item_type == 4)
                                            <input type="date"
                                                class="form-control bg-light input-value radius-xs border-0"
                                                value="{{ $val }}">
                                        @elseif($item->item_type == 5)
                                            <input type="number"
                                                class="form-control bg-light input-value radius-xs border-0"
                                                placeholder="ระบุตัวเลข..." value="{{ $val }}">
                                        @endif
                                    </div>
                                @endif

                                <div class="detail-box pt-3 mt-3 border-top {{ $status === '0' || $status === '2' ? '' : 'd-none' }}"
                                    id="detail_box_{{ $item->item_id }}">
                                    <label
                                        class="small fw-bold mb-2 {{ $status === '0' ? 'text-danger' : 'text-warning' }}">
                                        <i class="uil uil-comment-info"></i> สิ่งที่ตรวจพบ
                                    </label>
                                    <textarea class="form-control bg-light input-comment radius-xs border-0 mb-3" rows="2"
                                        placeholder="อธิบายเพิ่มเติม...">{{ $comment }}</textarea>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="small text-dark fw-bold mb-0">ภาพประกอบ <span
                                                id="count_{{ $item->item_id }}">({{ $images->count() }}/10)</span></label>
                                        <button type="button" class="btn btn-outline-primary btn-xs radius-xs"
                                            onclick="document.getElementById('file_{{ $item->item_id }}').click();">
                                            <i class="uil uil-camera-plus"></i> เพิ่มภาพ
                                        </button>
                                        <input type="file" id="file_{{ $item->item_id }}" class="d-none image-uploader"
                                            accept="image/*" capture="environment" data-item="{{ $item->item_id }}">
                                    </div>

                                    <div class="row g-2 image-gallery" id="gallery_{{ $item->item_id }}">
                                        @foreach ($images as $img)
                                            <div class="col-3 col-md-2 position-relative img-wrapper-{{ $img->id }}">
                                                <img src="{{ asset($img->image_path) }}" class="img-fluid rounded border"
                                                    style="height: 70px; width: 100%; object-fit: cover;">
                                                <button type="button"
                                                    class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0"
                                                    style="width: 20px; height: 20px;"
                                                    onclick="deleteImage({{ $img->id }}, '{{ $item->item_id }}')">
                                                    <i class="uil uil-times fs-12"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ฟังก์ชันบันทึกข้อมูลหลัก
            function saveItemData(card) {
                const recordId = card.querySelector('.data-record').value;
                const itemId = card.querySelector('.data-item').value;

                let resultStatus = null;
                const checkedRadio = card.querySelector('input[type="radio"]:checked');
                if (checkedRadio) resultStatus = checkedRadio.value;

                let resultValue = null;
                const valueInput = card.querySelector('.input-value');
                if (valueInput) resultValue = valueInput.value;

                let userComment = null;
                const commentInput = card.querySelector('.input-comment');
                if (commentInput) userComment = commentInput.value;

                fetch('{{ route('user.inspection.saveResult') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        record_id: recordId,
                        item_id: itemId,
                        result_status: resultStatus,
                        result_value: resultValue,
                        user_comment: userComment
                    })
                });
            }

            // ดักจับการเปลี่ยนแปลงสถานะ Radio
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const card = this.closest('.card-body');
                    const itemId = card.querySelector('.data-item').value;
                    const detailBox = document.getElementById('detail_box_' + itemId);

                    // เปิดกล่องหมายเหตุถ้าเลือก 0 (ไม่ผ่าน) หรือ 2 (ผ่านแต่แก้ไข)
                    if (this.value === '0' || this.value === '2') {
                        detailBox.classList.remove('d-none');
                    } else {
                        detailBox.classList.add('d-none');
                    }
                    saveItemData(card);
                });
            });

            // ดักจับการกรอกข้อความ/คอมเมนต์
            document.querySelectorAll('.input-value, .input-comment').forEach(input => {
                input.addEventListener('change', function() {
                    saveItemData(this.closest('.card-body'));
                });
            });

          // --- ส่วนงานอัปโหลดรูปภาพ (อัปเกรดใหม่ แสดงผลทันที มีระบบกันเหนียว) ---
            document.querySelectorAll('.image-uploader').forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.dataset.item;
                    const recordId = this.closest('.card-body').querySelector('.data-record').value;
                    const file = this.files[0];
                    if (!file) return;

                    const gallery = document.getElementById('gallery_' + itemId);

                    // 1. สร้างพรีวิวให้เห็นทันทีด้วย URL.createObjectURL (ลื่นไหล ไม่ต้องรอโหลดหน้า)
                    const tempId = Date.now();
                    const objectUrl = URL.createObjectURL(file);
                    const tempHtml = `
                        <div class="col-3 col-md-2 position-relative img-wrapper-temp-${tempId}">
                            <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover; opacity: 0.5;">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <i class="uil uil-spinner fa-spin text-primary fs-20"></i>
                            </div>
                        </div>
                    `;
                    gallery.insertAdjacentHTML('beforeend', tempHtml);

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('item_id', itemId);
                    formData.append('record_id', recordId);

                    fetch('{{ route('user.inspection.uploadItemImage') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            body: formData
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.text(); // ใช้ .text() รับค่ามาก่อนเพื่อป้องกัน Error กรณีไม่ใช่ JSON
                        })
                        .then(text => {
                            try {
                                const data = JSON.parse(text); // ลองแปลงเป็น JSON
                                
                                // ถ้า Controller ส่ง JSON สำเร็จ กลับมา
                                if (data.success || data.status === 'success') {
                                    document.querySelector(`.img-wrapper-temp-${tempId}`).remove();
                                    
                                    // ถ้าระบบส่ง ID กลับมาให้ด้วย ให้สร้างรูปลงแกลลอรี่ทันที
                                    if(data.image_id) {
                                        const finalHtml = `
                                            <div class="col-3 col-md-2 position-relative img-wrapper-${data.image_id}">
                                                <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover;">
                                                <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px;" onclick="deleteImage(${data.image_id}, '${itemId}')">
                                                    <i class="uil uil-times fs-12"></i>
                                                </button>
                                            </div>
                                        `;
                                        gallery.insertAdjacentHTML('beforeend', finalHtml);
                                        
                                        // อัปเดตตัวเลขจำนวนรูป
                                        const countSpan = document.getElementById('count_' + itemId);
                                        if(countSpan) {
                                            let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                            countSpan.innerText = `(${currentCount + 1}/10)`;
                                        }
                                    } else {
                                        location.reload(); // ถ้าไม่ส่ง ID กลับมา บังคับรีโหลด
                                    }
                                } else {
                                    location.reload();
                                }
                            } catch (e) {
                                // 🌟 ตัวแก้บั๊กหลัก: ถ้า Controller ไม่ได้ Return JSON (เช่นเกิดการ Redirect)
                                // แปลว่าข้อมูลลง DB แล้ว ให้ทำการบังคับรีโหลดหน้าเว็บเพื่อดึงรูปจากฐานข้อมูล
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Upload Error:', error);
                            location.reload(); // เผื่อเหนียว ถ้าเน็ตกระตุกหรือ Error ให้รีโหลดไว้ก่อน
                        });
                        
                    // ล้างค่า input ป้องกันบั๊กเลือกไฟล์เดิมซ้ำไม่ได้
                    this.value = '';
                });
            });

            // --- ส่วนงานลบรูปภาพ (อัปเกรดใหม่ ลบปุ๊บหายปั๊บ) ---
            function deleteImage(imageId, itemId) {
                if (!confirm('ยืนยันการลบรูปภาพ?')) return;

                // 1. ทำภาพให้จางลงก่อน เพื่อบอกผู้ใช้ว่ากำลังประมวลผลอยู่
                const imgWrapper = document.querySelector(`.img-wrapper-${imageId}`);
                if (imgWrapper) {
                    imgWrapper.style.opacity = '0.5';
                }

                fetch('{{ route('user.inspection.deleteItemImage') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            image_id: imageId
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response error');
                        return res.text(); // ใช้ .text() ดักไว้ก่อนกัน Error ถ้าไม่ใช่ JSON
                    })
                    .then(text => {
                        try {
                            const data = JSON.parse(text); // ลองแปลงเป็น JSON
                            
                            // ถ้าลบสำเร็จ (รองรับทั้ง key: success และ status: success)
                            if (data.success || data.status === 'success') {
                                if (imgWrapper) imgWrapper.remove(); // ลบออกจากหน้าจอทันที
                                
                                // อัปเดตตัวเลขจำนวนรูปให้ลดลง
                                const countSpan = document.getElementById('count_' + itemId);
                                if (countSpan) {
                                    let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                    if (currentCount > 0) {
                                        countSpan.innerText = `(${currentCount - 1}/10)`;
                                    }
                                }
                            } else {
                                // ถ้าลบไม่สำเร็จ ให้รีโหลดหน้าจอเพื่อดึงข้อมูลล่าสุด
                                location.reload();
                            }
                        } catch (e) {
                            // กรณี Controller ไม่ได้ Return เป็น JSON (เช่น Return กลับมาเป็น Redirect)
                            // ถือว่าทำงานเสร็จแล้ว ให้รีโหลดเพื่ออัปเดตหน้าจอ
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Delete Error:', error);
                        // ถ้าเน็ตหลุดหรือมี Error ให้รีโหลดเพื่อความชัวร์
                        location.reload();
                    });
            
            }
        </script>
    @endpush
@endsection
