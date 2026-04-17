<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class CompanyPreInspectionController extends Controller
{
      public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function create()
    {
        return view('pages.company.pre_inspection_create');
    }

    // บันทึกข้อมูลลง Database
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'template_name'        => 'required|string|max:255',
            'fields'               => 'required|array|min:1',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type'  => 'required|in:text,image,gps',
            'fields.*.is_required' => 'required|in:0,1',
        ]);

        $company_id = Auth::user()->company_id ?? Auth::user()->user_id; // ปรับให้ตรงกับฟิลด์ที่เก็บ ID บริษัทของคุณ

        DB::beginTransaction();

        try {
            // 2. Insert ลงตารางหลัก (Templates)
            $templateId = DB::table('pre_inspection_templates')->insertGetId([
                'company_id'    => $company_id,
                'template_name' => $request->template_name,
                'is_active'     => 1,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);

            // 3. เตรียมข้อมูล Batch Insert สำหรับตารางย่อย (Fields)
            $insertFields = [];
            $sortOrder = 1;

            foreach ($request->fields as $field) {
                $insertFields[] = [
                    'template_id'  => $templateId,
                    'field_label'  => $field['field_label'],
                    'field_type'   => $field['field_type'],
                    'is_required'  => $field['is_required'],
                    'sort_order'   => $sortOrder,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ];
                $sortOrder++;
            }

            // 4. บันทึกข้อมูลฟิลด์ทั้งหมด
            DB::table('pre_inspection_fields')->insert($insertFields);

            DB::commit();

            // เปลี่ยน Route ปลายทางกลับไปยังหน้ารายการที่คุณต้องการ
            return redirect()->back()->with('success', 'สร้างแม่แบบก่อนตรวจรถเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

}
