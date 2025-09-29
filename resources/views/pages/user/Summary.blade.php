@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">


                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <label class="fw-bold fs-20">เลือกเพื่อกลับไปยังหมวดหมู่</label>
                    </div>
                    <div class="card-body">
                                         
                        
<div class="d-flex flex-wrap gap-2 mb-3">
 @foreach($categories as $cat)
         @php
            $catId     = $cat->category_id;
            $isChecked = in_array($catId, $checkedCategories, true);

            $btnClass = $isChecked ? 'btn-success' : 'btn-outline-primary';
        @endphp
       <a href="{{ route('user.chk_step2', ['rec' => $record->record_id, 'cats' => $cat->category_id]) }}"
           class="btn btn-sm {{ $btnClass }}">
            @if($isChecked) <i class="fas fa-check"></i> @endif
            {{ $cat->cates_no }}. {{ $cat->chk_cats_name }}
        </a>
    @endforeach
</div>
   

                        {{-- แจ้งเตือน --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                    </div>
                </div>

                <div class="card mt-20 mb-20">
                    <div class="card-header bg-info">
                        <label class="fw-bold fs-20 text-dark">สรุปผลการตรวจ :
                            {{ $veh_detail->car_plate }} ({{ $veh_detail->car_brand }})

                        </label>
                    </div>



                    <div class="card-body">

                        <div class="mb-4">
                            <label class="fw-bold">ความคืบหน้าการตรวจ: {{ $checkedItems }}/{{ $totalItems }} ข้อ
                                ({{ $progress }}%)</label>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                        </div>

                        @foreach ($categories as $cat)
                            <div class="alert alert-info fs-20 fw-bold">
                                {{ $cat->cates_no }}.{{ $cat->chk_cats_name }}
                            </div>

                            @php
                                $catItems = $itemsByCategory->get($cat->category_id, collect());
                            @endphp
                            <table class="table table-sm table-bordered fixed-table">
                                <thead>
                                    <tr>
                                        <th style="width:33%">รายการตรวจประเมิน</th>
                                        <th style="width:33%">ผลการประเมิน</th>
                                        <th style="width:34%">สิ่งที่ตรวจพบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($catItems as $item)
                                        @php
                                            $res = $results->get($item->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $item->item_no }}.{{ $item->item_name }}</td>
                                            <td>
                                                @if ($res)
                                                    @if ($res->result_value == '1')
                                                        <span class="text-success"> ผ่าน </span>
                                                    @elseif($res->result_value == '0')
                                                        <span class="text-danger">ไม่ผ่าน</span>
                                                    @elseif($res->result_value == '2')
                                                        <span class="text-secondary"> ผ่าน แต่ต้องแก้ไขปรับปรุง
                                                        </span>
                                                    @elseif($res->result_value == '3')
                                                        <span class="text-muted"> ไม่เกี่ยวข้อง
                                                        </span>
                                                    @else
                                                        {{ $res->result_value }}
                                                    @endif
                                                @else
                                                    <span class="text-danger fw-bold">ยังไม่ได้ตรวจ</span>
                                                @endif
                                            </td>
                                            <td>{{ $res->user_comment ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">ไม่มีข้อตรวจในหมวดนี้</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="border-top my-3"></div>
                        @endforeach
                    </div>
                </div>

                @if ($record->chk_status === '0')
                    <form method="POST" action="{{ route('user.chk_confirm', $record->id) }}">
                        @csrf
                        <button type="submit" class="mb-4 fs-18 btn btn-success"
                            onclick="return confirm('ยืนยันผลการตรวจนี้หรือไม่? หลังจากยืนยันแล้วจะไม่สามารถแก้ไขได้')">
                            ยืนยันการตรวจ
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mt-3">การตรวจนี้ถูกยืนยันแล้ว ไม่สามารถแก้ไขได้</div>
                @endif


            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .fixed-table {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endpush
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
