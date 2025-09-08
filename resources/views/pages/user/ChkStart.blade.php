@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class=" alert alert-primary " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold">เลือกแบบฟอร์ม</span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-header d-flex justify-content-end align-items-center">

                            <small class="text-muted" id="live-clock">

                        </div>
                        <div class="card-body">

                            @if (session('error'))
                                <div class="alert alert-danger fs-20 fw-bold">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('user.insert1', request()->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="inputName"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">ทะเบียนรถ</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control radius-xs b-light fs-20"
                                            value="{{ $veh_detail->car_plate }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="inputName"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">เลือกแบบฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="form_id" id="select-alerts2" class="form-control " required>
                                            <option value="0" selected disabled>-- กรุณาเลือก --</option>
                                            @foreach ($forms as $form)
                                                <option value="{{ $form->form_id }}" class="fs-18">{{ $form->form_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="border-top my-3"></div>

                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="front_image"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">ภาพหน้ารถ<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="file" name="front_image" id="front_image"
                                            accept="image/*" onchange="previewImage(this, 'preview_front')" required>
                                        <img id="preview_front" src="#" alt="Preview" class="img-thumbnail mt-2"
                                            style="display: none; max-width: 200px;">
                                    </div>
                                </div>
 <div class="border-top my-3"></div>
                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="front_image"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">ภาพข้างรถ<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="file" name="side_image" id="side_image"
                                            accept="image/*" onchange="previewImage(this, 'preview_side')" required>
                                        <img id="preview_side" src="#" alt="Preview" class="img-thumbnail mt-2"
                                            style="display: none; max-width: 200px;">
                                    </div>
                                </div>
 <div class="border-top my-3"></div>
                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="front_image"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">สภาพโดยรวม<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="file" name="overall_image" id="overall_image"
                                            accept="image/*" onchange="previewImage(this, 'preview_overall')" required>
                                        <img id="preview_overall" src="#" alt="Preview" class="img-thumbnail mt-2"
                                            style="display: none; max-width: 200px;">
                                    </div>
                                </div>



                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-secondary fs-20">เริ่มการตรวจ &nbsp;<i
                                        class="fas fa-arrow-right"></i> </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }


        function updateClock() {
            const now = new Date();

            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear() + 543;

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const fullDateTime = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

            document.getElementById('live-clock').textContent = fullDateTime;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
@endpush
