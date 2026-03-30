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

class CompanySupplyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function create()
    {
        return view('pages.company.supplies_create');
    }

    // ฟังก์ชันบันทึกข้อมูล Supply
    public function store(Request $request)
    {
        // 1. ดึงข้อมูลบริษัทแม่ที่ล็อกอินอยู่
        $user = Auth::user();
        $companyCode = $user->company_code; // รหัสบริษัทแม่
        $agencyId = $user->agency_user_id ?? $user->agency_id; // รหัส Agency ต้นสังกัด

        // 2. Validate ข้อมูล
        $request->validate([
            'supply_name' => 'required|string|max:200',
            'supply_address' => 'required|string',
            'vehicle_limit' => 'nullable|integer|min:0', // โควตารถของสาขานี้
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'supply_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supply_user' => 'required|string|unique:users,username', // เช็ค Username ไม่ให้ซ้ำ
            'supply_password' => 'required|string|min:6',
        ]);

        // 3. สุ่มรหัส Supply (sup_id)
        do {
            $supId = 'SUP-' . strtoupper(Str::random(6));
            $exists = DB::table('supply_datas')->where('sup_id', $supId)->exists();
        } while ($exists);

        // 4. จัดการรูปภาพโลโก้
        $logoPath = null;
        if ($request->hasFile('supply_logo')) {
            $file = $request->file('supply_logo');
            $filename = $supId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo/supply'), $filename); // เก็บแยกโฟลเดอร์ให้เป็นระเบียบ
            $logoPath = 'logo/supply/' . $filename;
        }

        // 5. บันทึกลงฐานข้อมูลแบบ Transaction (2 ตาราง)
        DB::beginTransaction();

        try {
            // บันทึกลงตาราง supply_datas
            DB::table('supply_datas')->insert([
                'company_code' => $companyCode,
                'agency_user_id' => $agencyId,
                'sup_id' => $supId,
                'supply_name' => $request->supply_name,
                'supply_logo' => $logoPath,
                'supply_address' => $request->supply_address,
                'supply_phone' => $request->supply_phone,
                'supply_email' => $request->supply_email,
                'supply_status' => 'active', // ค่าเริ่มต้น
                'vehicle_limit' => $request->vehicle_limit ?? 0,
                'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
                'start_date' => $request->start_date,
                'expire_date' => $request->expire_date,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // บันทึกลงตาราง users สำหรับล็อกอิน
            DB::table('users')->insert([
                'user_id' => $supId,
                'username' => $request->supply_user,
                'prefix' => '-',
                'name' => $request->supply_name,
                'lastname' => '-',
                'user_status' => $request->has('require_user_approval') ? 1 : 0,
                'email' => $request->supply_email,
                'password' => Hash::make($request->supply_password),
                'user_phone' => $request->supply_phone,
                'logo_agency' => $logoPath,
                'role' => 'supply', // สำคัญมาก: กำหนด Role เป็น supply
                'company_code' => $companyCode,
                'agency_user_id' => $agencyId,
                'agency_id' => $agencyId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()
                ->route('company.supplies.index')
                ->with('success', 'สร้างสาขา (Supply) เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }
}
