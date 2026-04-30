<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class InspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function index()
    {
        // ดึงกลุ่มฟอร์มที่เปิดใช้งาน (Master Forms)
        $formGroups = DB::table('form_groups')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->get();

        return view('pages.user.inspection.step1_select', compact('formGroups'));
    }

    // ระบบค้นหารถยนต์ (จำกัดสิทธิ์ตามประเภทช่าง)
    public function searchVehicle(Request $request)
    {
        $user = Auth::user()->user_id;
        $search = $request->get('q');

        // 1. ดึงข้อมูล Profile ของช่างตรวจที่ล็อกอินอยู่
        // (สมมติว่าตาราง users มีคอลัมน์ user_id ที่ตรงกับ inspector_datas)
        $inspector = DB::table('inspector_datas')
            ->where('ins_id', '=', $user)
            ->first();

        // ถ้าไม่พบข้อมูลช่าง ให้คืนค่าว่างเปล่า (ป้องกัน Error)
        if (!$inspector) {
            return response()->json([]);
        }

        // 2. เริ่ม Query จากตาราง vehicles_detail ของคุณ
        $query = DB::table('vehicles_detail')
            ->where('car_plate', 'LIKE', "%{$search}%")
            ->where('status', '!=', '2'); // สมมติว่า 2 คือสถานะห้ามใช้งาน

        // ==========================================
        // 🔒 Data Access Logic (สิทธิ์การมองเห็นรถ)
        // ==========================================
        if ($inspector->inspector_type == 1) {
            // ช่าง Company: ดูได้ทุก Supply ในบริษัทแม่
            $query->where('company_code', $inspector->company_code);
        } elseif ($inspector->inspector_type == 2) {
            // ช่าง Supply: ดูได้เฉพาะรถใน Supply ตัวเอง
            $query->where('company_code', $inspector->company_code)
                ->where('supply_id', $inspector->sup_id);
        } elseif ($inspector->inspector_type == 3) {
            // ช่าง Outsource: ดูตามสิทธิ์ที่ Staff ให้ในตาราง Pivot
            $allowedSupplyIds = DB::table('inspector_supply_access')
                ->where('ins_id', $inspector->ins_id)
                ->pluck('supply_id');

            $query->where('company_code', $inspector->company_code)
                ->whereIn('supply_id', $allowedSupplyIds);
        }

        // 3. ดึงข้อมูลส่งกลับให้ Select2
        $vehicles = $query->limit(10)->get([
            'car_id',
            'car_plate',
            'car_brand',
            'car_model'
        ]);

        return response()->json($vehicles);
    }

    // กดปุ่มเริ่มตรวจ (สร้าง Record)
    public function start(Request $request)
    {
        $user = Auth::user()->user_id;
        $request->validate([
            'car_id' => 'required',
            'form_group_id' => 'required'
        ]);
        // 1. ดึงข้อมูลช่างตรวจ
        $inspector = DB::table('inspector_datas')->where('ins_id', $user)->first();
        // 2. ดึงข้อมูลรถ เพื่อเอา supply_id มาเก็บไว้เป็นสถิติ
        $car = DB::table('vehicles_detail')->where('car_id', $request->car_id)->first();
        // 3. ดึงข้อมูลกลุ่มฟอร์ม เพื่อเอา form_id แบบสตริง (เช่น FRM-001)
        $formGroup = DB::table('form_groups')->where('id', $request->form_group_id)->first();
        // 4. สร้างรหัสใบตรวจ (Running Number อย่างง่าย)
        // ผลลัพธ์ตัวอย่าง: CHK-20260425-120530
        $generateRecordId = 'CHK-' . date('Ymd-His');

        // 5. สร้างประวัติการตรวจ (สถานะ Draft)
        $insertedId = DB::table('chk_records')->insertGetId([
            'user_id'    => $user,
            'veh_id'     => $request->car_id,
            'record_id'  => $generateRecordId,
            'form_group_id'    => $formGroup->form_group_id, // เก็บเป็นรหัสฟอร์ม
            'form_id'   => $formGroup->check_item_form_id,
            'supply_id'  => $car->supply_id ?? '', // ถ้ารถไม่มี supply_id ให้ใส่ค่าว่าง
            'chk_status' => '0', // 0 = Draft / ตรวจยังไม่ครบ
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ส่ง Primary Key (id) ไปยังหน้า Step 2 (ถ่ายรูป)
        return redirect()->route('user.inspection.step2', ['record_id' => $generateRecordId]);
    }

    public function step2($record_id)
    {
        // 1. ดึงข้อมูลใบตรวจ
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->route('user.inspection.index')->with('error', 'ไม่พบประวัติการตรวจนี้');


        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();


        if (empty($formGroup->pre_inspection_template_id)) {
            return redirect()->route('user.inspection.step3', ['record_id' => $record_id]);
        }

        $preFields = DB::table('pre_inspection_fields')
            ->where('template_id', $formGroup->pre_inspection_template_id)
            ->orderBy('sort_order')
            ->get();

        return view('pages.user.inspection.step2_pre_inspect', compact('record', 'formGroup', 'preFields'));
    }

    public function storeStep2(Request $request, $record_id)
    {
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        $user = DB::table('users')->where('user_id', Auth::user()->user_id)->first();

        // 1. จัดการบันทึกรูปภาพ (ลง vehicle_image_records เหมือนเดิม)
        if ($request->hasFile('photos')) {
            $imageData = [
                'user_create_id' => $user->user_id,
                'company_id'     => $user->company_code ?? '',
                'supply_id'      => $record->supply_id ?? '',
                'record_id'      => $record->record_id,
                'veh_id'         => $record->veh_id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            foreach ($request->file('photos') as $index => $file) {
                if ($index <= 8) {
                    $filename = 'pre_img' . $index . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('public/pre_inspections/' . $record->record_id, $filename);
                    $imageData['image' . $index] = 'storage/pre_inspections/' . $record->record_id . '/' . $filename;
                }
            }
            DB::table('vehicle_image_records')->insert($imageData);
        }

        // 2. จัดการบันทึกข้อมูล Text และ GPS (ลง pre_inspection_results)
        if ($request->has('fields')) {
            $dynamicData = [];
            foreach ($request->fields as $field_id => $value) {
                if (!empty($value)) {
                    $dynamicData[] = [
                        'record_id'   => $record->record_id,
                        'field_id'    => $field_id,
                        'field_value' => $value,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
            if (count($dynamicData) > 0) {
                DB::table('pre_inspection_results')->insert($dynamicData);
            }
        }
        return redirect()->route('user.inspection.step3', ['record_id' => $record_id]);
    }

    // โหลดหน้า Step 3
    public function step3(Request $request, $record_id)
    {

       $record = DB::table('chk_records')->where('record_id', $record_id)->first();
    if (!$record) return redirect()->route('user.inspection.index');

    $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();

    // 2. ดึงหมวดหมู่ทั้งหมดของฟอร์มนี้
    $categories = DB::table('check_categories')
        ->where('form_id', $record->form_id)
        ->orderBy('cates_no', 'asc')
        ->get();

    // 3. ดึงข้อตรวจ "ทั้งหมด" ของทุกหมวดหมู่ในฟอร์มนี้ และจัดกลุ่มตาม category_id
    // ใช้ groupBy เพื่อให้ใน Blade สามารถเรียก $itemsByCategory[$cat->category_id] ได้เลย
    $itemsByCategory = DB::table('check_items')
        ->whereIn('category_id', $categories->pluck('category_id'))
        ->orderBy('item_no', 'asc')
        ->get()
        ->groupBy('category_id');

    // 4. ดึงผลการตรวจเดิมและรูปภาพ (ถ้ามี)
    $existingResults = DB::table('check_records_result')
        ->where('record_id', $record->record_id)
        ->get()
        ->keyBy('item_id');

    $existingImages = DB::table('check_result_images')
        ->where('record_id', $record->record_id)
        ->get()
        ->groupBy('item_id');

    $vehicle = DB::table('vehicles_detail')->where('car_id', $record->veh_id)->first();

    return view('pages.user.inspection.step3_checklist', compact(
        'record', 
        'formGroup', 
        'categories', 
        'itemsByCategory', // ส่งแบบจัดกลุ่มไป
        'existingResults', 
        'existingImages', 
        'vehicle'
    ));
}
    

    // Auto-Save ข้อมูลทั่วไปผ่าน AJAX
    public function saveResult(Request $request)
    {
        $user = Auth::user()->user_id;
        $data = $request->all();

        DB::table('check_records_result')->updateOrInsert(
            [
                'record_id' => $data['record_id'], // รหัส string (CHK-...)
                'item_id'   => $data['item_id']
            ],
            [
                'user_id'       => $user,
                'result_status' => $data['result_status'] ?? null, // '1' = ผ่าน, '0' = ไม่ผ่าน
                'result_value'  => $data['result_value'] ?? null,
                'user_comment'  => $data['user_comment'] ?? null,
                'updated_at'    => now()
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // อัปโหลดรูปภาพย่อยผ่าน AJAX (จำกัด 10 ภาพ)
    public function uploadItemImage(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|max:5120', // ขนาดไม่เกิน 5MB
            'record_id' => 'required',
            'item_id'   => 'required'
        ]);

        // เช็คว่าอัปโหลดเกิน 10 ภาพหรือยัง
        $imgCount = DB::table('check_result_images')
            ->where('record_id', $request->record_id)
            ->where('item_id', $request->item_id)
            ->count();

        if ($imgCount >= 10) {
            return response()->json(['status' => 'error', 'message' => 'อัปโหลดได้สูงสุด 10 ภาพต่อข้อ'], 422);
        }

        // บันทึกไฟล์ (ปรับ path ตามโครงสร้างโปรเจกต์ของคุณ)
        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/fileupload/' . $request->record_id, $filename);
        $dbPath = 'storage/fileupload/' . $request->record_id . '/' . $filename;

        // บันทึกลงตาราง check_result_images
        $imageId = DB::table('check_result_images')->insertGetId([
            'record_id'  => $request->record_id,
            'item_id'    => $request->item_id,
            'image_path' => $dbPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'status' => 'success',
            'image_id' => $imageId,
            'image_url' => asset($dbPath)
        ]);
    }

    // ลบรูปภาพย่อย
    public function deleteItemImage(Request $request)
    {
        $imageId = $request->image_id;
        $image = DB::table('check_result_images')->where('id', $imageId)->first();

        if ($image) {
            \Illuminate\Support\Facades\Storage::delete(str_replace('storage/', 'public/', $image->image_path));
            DB::table('check_result_images')->where('id', $imageId)->delete();
        }
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'ลบรูปภาพเรียบร้อย'
        ]);
    }

    public function step4($record_id)
    {
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->route('user.inspection.index');


        $totalItems = DB::table('check_items')
            ->join('check_categories', 'check_items.category_id', '=', 'check_categories.category_id')
            ->where('check_categories.form_id', $record->form_id)
            ->count();


        $results = DB::table('check_records_result')->where('record_id', $record->record_id)->get();

        $passCount = $results->where('result_status', '1')->count();
        $failCount = $results->where('result_status', '0')->count();
        $AlmostCount = $results->where('result_status', '2')->count();
        $uncheckedCount = $totalItems - ($passCount + $failCount + $AlmostCount);

        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();
        $vehicle = DB::table('vehicles_detail')->where('car_id', $record->veh_id)->first();

        return view('pages.user.inspection.step4_summary', compact('record', 'totalItems', 'passCount', 'failCount', 'uncheckedCount', 'formGroup', 'vehicle', 'AlmostCount'));
    }

    // บันทึกการตรวจ
    public function submitInspection(Request $request, $record_id)
    {
        // 1. ดึงข้อมูล record เดิมมาก่อน
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->back()->with('error', 'ไม่พบข้อมูลใบตรวจ');

        // 2. ตรวจสอบ Validation (แยกเงื่อนไขตามปุ่มที่กด)
        if ($request->submit_type == 'final') {
            $request->validate([
                'evaluate_status' => 'required',
                'inspector_sign_data' => 'required', // กดยืนยันผล บังคับลายเซ็น
            ]);

            // ถ้าเลือกสถานะ 3 บังคับให้ต้องมีวันที่นัดตรวจใหม่
            if ($request->evaluate_status == '3') {
                $request->validate([
                    'next_inspect_date' => 'required|date'
                ]);
            }
        }

        // 3. แปลงข้อมูลลายเซ็น (Base64) ให้เป็นไฟล์รูปภาพ (PNG)
        $inspectorSignPath = $record->inspector_sign; // เก็บค่าเดิมไว้ เผื่อบันทึกแบบร่างแล้วไม่ได้เซ็นซ้ำ
        if ($request->filled('inspector_sign_data')) {
            $image_parts = explode(";base64,", $request->inspector_sign_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'sign_ins_' . $record->record_id . '_' . time() . '.png';
                $path = 'public/signatures/' . $filename;
                \Illuminate\Support\Facades\Storage::put($path, $image_base64);
                $inspectorSignPath = 'storage/signatures/' . $filename;
            }
        }

        $driverSignPath = $record->driver_sign; // เก็บค่าเดิม
        if ($request->filled('driver_sign_data')) {
            $image_parts = explode(";base64,", $request->driver_sign_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'sign_drv_' . $record->record_id . '_' . time() . '.png';
                $path = 'public/signatures/' . $filename;
                \Illuminate\Support\Facades\Storage::put($path, $image_base64);
                $driverSignPath = 'storage/signatures/' . $filename;
            }
        }

        // 4. เตรียมชุดข้อมูลสำหรับอัปเดตลงตาราง chk_records
        $updateData = [
            'inspector_sign' => $inspectorSignPath,
            'driver_sign'    => $driverSignPath,
            'updated_at'     => now(),
        ];

        // ถ้ามีการเลือกสถานะการใช้งาน ให้เก็บค่าลงไปด้วย
        if ($request->has('evaluate_status')) {
            $updateData['evaluate_status'] = $request->evaluate_status;

            // ถ้าเป็นสถานะ 3 เก็บวันที่ตรวจใหม่ ถ้าไม่ใช่ ให้ล้างค่าทิ้ง (เผื่อช่างเปลี่ยนใจ)
            if ($request->evaluate_status == '3') {
                $updateData['next_inspect_date'] = $request->next_inspect_date;
            } else {
                $updateData['next_inspect_date'] = null;
            }
        }

        // 5. แยกการจบงาน (Draft vs Final)
        if ($request->submit_type == 'final') {
            $updateData['chk_status'] = '1'; // 1 = ตรวจเสร็จสิ้น 100%
            $message = 'บันทึกและยืนยันผลการตรวจสภาพรถเรียบร้อยแล้ว';
        } else {
            $updateData['chk_status'] = '2';
            // กรณีเป็น Draft ปล่อย chk_status ไว้ตามค่าเดิมของระบบ (เช่น '0' กำลังดำเนินการ)
            $message = 'บันทึกแบบร่างสำเร็จ คุณสามารถกลับมาตรวจต่อได้ในภายหลัง';
        }

        // 6. อัปเดตตาราง chk_records
        DB::table('chk_records')->where('record_id', $record_id)->update($updateData);

        // นำไปหน้าแจ้งเตือนความสำเร็จ
        return redirect()->route('local.home')->with('success', $message);
    }

    public function viewReport($record_id)
    {
        // 1. ดึงข้อมูลบันทึกการตรวจหลัก
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->back()->with('error', 'ไม่พบข้อมูล');

        // 2. ดึงข้อมูล Form Group เพื่อใช้เป็นสะพานเชื่อม
        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();

        // 3. ข้อมูลพาหนะ, บริษัท, Supply และ พนักงานตรวจ
        $vehicle = DB::table('vehicles_detail')
            ->leftJoin('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->where('vehicles_detail.car_id', $record->veh_id)
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type')
            ->first();

        $company = DB::table('company_details')->where('company_id', $vehicle->company_code)->first();
        $supply = DB::table('supply_datas')->where('sup_id', $vehicle->supply_id)->first();
        $form = DB::table('forms')->where('form_id', $record->form_id)->first();
        $inspector = DB::table('users')->where('user_id', $record->user_id)->first();

        // 4. ดึง Pre-inspection Fields (ข้อมูลที่ช่างกรอกก่อนตรวจ)
        $preInspectFields = DB::table('pre_inspection_results')
            ->join('pre_inspection_fields', 'pre_inspection_results.field_id', '=', 'pre_inspection_fields.id')
            ->where('pre_inspection_results.record_id', $record_id)
            ->where('pre_inspection_fields.template_id', $formGroup->pre_inspection_template_id)
            ->get();

        $preImages = DB::table('vehicle_image_records')->where('record_id', $record_id)->first();

        // 5. ดึง Checklist Categories
        $categories = DB::table('check_categories')
            ->where('form_id', $formGroup->check_item_form_id)
            ->orderBy('cates_no', 'asc')
            ->get();

        $results = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.item_id')
            ->where('check_records_result.record_id', $record_id)
            ->get()
            ->groupBy('category_id');

        $itemImages = DB::table('check_result_images')->where('record_id', $record_id)->get()->groupBy('item_id');

        // 6. ดึง Report Template และ Report Template Fields
        $reportTemplate = DB::table('report_templates')->where('id', $formGroup->report_template_id)->first();
        $reportFields = DB::table('report_template_fields')->where('template_id', $formGroup->report_template_id)->get();



        $checked = '<strong style="color: #000; font-size: 18px;">&#9745;</strong>';
        $unchecked = '<span style="font-size: 18px; color: #666;">&#9744;</span>';

        $replacements = [
            '[car_plate]'            => $vehicle->car_plate ?? '-',
            '[car_trailer_plate]'    => $vehicle->car_trailer_plate ?? '-',
            '[car_insure]'           => $vehicle->car_insure ?? '-',
            '[car_insurance_expire]' => $vehicle->car_insurance_expire ? thai_date($vehicle->car_insurance_expire) : '-',
            '[car_tax]'              => $vehicle->car_tax ?? '-',
            '[car_register_date]'    => $vehicle->car_register_date ? thai_date($vehicle->car_register_date) : '-',
            '[car_total_weight]'     => $vehicle->car_total_weight ?? '-',
            '[car_fuel]'             => $vehicle->car_fuel_type ?? '-',
            '[car_mileage]'          => $vehicle->car_mileage ?? '-',

            '[company_name]'         => $company->company_name ?? '-',
            '[logo_company]'         => ($company && $company->company_logo) ? '<img src="' . asset($company->company_logo) . '" style="max-height:50px;">' : '',
            '[supply_name]'          => $supply->supply_name ?? '-',

            '[form_code]'            => $form->form_code ?? '-',
            '[form_name]'            => $form->form_name ?? '-',

            '[inspect_date]'         => thai_date($record->created_at),
            '[next_inspect_date]'    => $record->next_inspect_date ? thai_date($record->next_inspect_date) : '-',

            '[inspector_name]'       => $inspector ? ($inspector->prefix . $inspector->name . ' ' . $inspector->lastname) : '-',
            '[inspector_sign]'       => $record->inspector_sign ? '<img src="' . asset($record->inspector_sign) . '" style="max-height:50px;">' : '',

            '[check_status_1]'       => ($record->evaluate_status == 1) ? $checked : $unchecked,
            '[check_status_2]'       => ($record->evaluate_status == 2) ? $checked : $unchecked,
            '[check_status_3]'       => ($record->evaluate_status == 3) ? $checked : $unchecked,
        ];

        // 6.2 นำ Tag จาก report_template_fields (เช่น [driver_name]) มาจับคู่กับข้อมูลที่กรอกไว้
        if ($reportFields->isNotEmpty()) {
            foreach ($reportFields as $rField) {
                $tag = '[' . $rField->field_key . ']';

                $matchedField = $preInspectFields->firstWhere('field_label', $rField->field_label);

                if ($matchedField) {
                    $replacements[$tag] = $matchedField->field_value;
                } elseif (!isset($replacements[$tag])) {
                    // ถ้าไม่เจอ และยังไม่มีใน Tag พื้นฐาน ให้แสดงเป็นค่าว่าง หรือ '-'
                    $replacements[$tag] = '-';
                }
            }
        }


        // 6.3 ประมวลผลลบ Tag ออกและใส่ข้อมูลจริงลงใน HTML
        if ($reportTemplate) {
            $reportTemplate->header_html = str_replace(array_keys($replacements), array_values($replacements), $reportTemplate->header_html);
            if (!empty($reportTemplate->footer_html)) {
                $reportTemplate->footer_html = str_replace(array_keys($replacements), array_values($replacements), $reportTemplate->footer_html);
            }
        }

        return view('pages.user.inspection.report', compact(
            'record',
            'vehicle',
            'formGroup',
            'preInspectFields',
            'preImages',
            'categories',
            'results',
            'itemImages',
            'reportTemplate'
        ));
    }


  
}
