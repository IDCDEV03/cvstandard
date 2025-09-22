@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class=" alert alert-primary " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold"> {{ $forms->form_name }} </span>
                        </div>
                    </div>

                    <div class="card card-default card-sm mb-4">
                        <div class="card-header">
            <label class="fw-bold">รายการหมวดหมู่</label>
        </div>
                        <div class="card-body">
                            
{{-- เมนูเลือกหมวด --}}
<div class="d-flex flex-wrap gap-2 mb-3">
    @foreach($allCategories as $cat)
        <a href="{{ route('user.chk_step2', ['rec' => $record->record_id, 'cats' => $cat->category_id]) }}"
           class="btn btn-xs {{ $category->category_id == $cat->category_id ? 'btn-secondary' : 'btn-outline-secondary' }}">
            {{ $cat->cates_no }}. {{ $cat->chk_cats_name }}
        </a>
    @endforeach
</div>
                            
                        </div>
                    </div>

                    <div class="card shadow-sm mb-25">
                        <div class="card-body">


                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <p class="fs-20 fw-bold">หมวดหมู่ที่ {{ $category->cates_no }} : {{ $category->chk_cats_name }}
                            </p>
                            <form method="POST"
                                action="{{ route('user.chk_insert_step2', [$record->record_id, $category->category_id]) }}"
                                enctype="multipart/form-data">
                                @csrf

                                @foreach ($items as $item)
                                    <div class="mb-3 border-success rounded p-3">
                                        <label class="fw-bold fs-18">{{ $item->item_no }}. {{ $item->item_name }}</label>
                                        <div class="text-danger d-block mb-2 mt-2">{{ $item->item_description }}</div>
                                        @if (empty($item->item_image))
                                        @else
                                            <p></p><img src="{{ asset($item->item_image) }}" class="img-thumbnail mb-2"
                                                width="400px" alt=""><br>
                                        @endif
                                        @if ($item->item_type == '1')
                                            <select name="item_result[{{ $item->id }}]" class="form-select mt-2"
                                                required>

                                                <option value="1" selected>✅ ผ่าน</option>
                                                <option value="2">⚠️ ผ่าน แต่ต้องแก้ไขปรับปรุง</option>
                                                <option value="0">❌ ไม่ผ่าน</option>

                                            </select>
                                            <textarea name="user_comment[{{ $item->id }}]" class="form-control mt-2"
                                                placeholder="ความคิดเห็นเพิ่มเติม (ถ้ามี)"></textarea>
                                            <label class="mt-2">อัปโหลดภาพ (ไม่เกิน 10 ภาพ)</label>
                                            <input type="file" name="item_images[{{ $item->id }}][]"
                                                class="form-control image-input-multi" multiple accept="image/*">
                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '2')
                                            <select name="item_result[{{ $item->id }}]" class="form-select mt-2"
                                                required>
                                                <option value="1" selected>✅ ปกติ</option>
                                                <option value="2">⚠️ ไม่ปกติ แต่ยังสามารถใช้งานได้</option>
                                                <option value="0">❌ ไม่สามารถใช้งานได้</option>
                                                <option value="3">⛔ ไม่เกี่ยวข้อง</option>
                                            </select>
                                            <textarea name="user_comment[{{ $item->id }}]" class="form-control mt-2"
                                                placeholder="ความคิดเห็นเพิ่มเติม (ถ้ามี)"></textarea>
                                            <label class="mt-2">อัปโหลดภาพ (ไม่เกิน 10 ภาพ)</label>
                                            <input type="file" name="item_images[{{ $item->id }}][]"
                                                class="form-control image-input-multi" multiple accept="image/*">
                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '3')
                                            <textarea name="item_result[{{ $item->id }}]" class="form-control mt-2" required></textarea>
                                            <label class="mt-2">อัปโหลดภาพ (ไม่เกิน 10 ภาพ)</label>
                                            <input type="file" name="item_images[{{ $item->id }}][]"
                                                class="form-control image-input-multi" multiple accept="image/*">
                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '4')
                                            <input type="text" id="date_input" name="item_result[{{ $item->id }}]" class="form-control"
                                                placeholder="เลือกวันที่" required>
                                        @endif


                                    </div>
                                @endforeach

                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-block btn-success fs-18">บันทึกและไปต่อ <i
                                        class="fas fa-arrow-right"></i></button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <script>
        flatpickr("#date_input", {
            dateFormat: 'j F Y',
            altInput: true,
            altFormat: "j F Y", 
            locale: "th",
            disableMobile: true, 
            defaultDate: "today",     
             
        });    
   
    </script>
    <script>
        document.querySelectorAll('.image-input-multi').forEach(input => {
            input.addEventListener('change', function() {
                const previewContainer = this.nextElementSibling;
                previewContainer.innerHTML = '';

                const files = this.files;
                if (files.length > 10) {
                    alert('ไม่สามารถอัปโหลดได้เกิน 10 รูป');
                    this.value = '';
                    return;
                }

                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '120px';
                        img.classList.add('rounded', 'border');
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
@endpush
