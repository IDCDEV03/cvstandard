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

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company,supply,staff']);
    }

    public function index()
    {
        // สมมติว่าใน Auth มี role หรือคุณเช็คจากตาราง user ได้
        $userRole = Auth::user()->role;
        $userCompanyCode = Auth::user()->company_code ?? null;
        $userSupplyId = Auth::user()->supply_id ?? null;

        $query = DB::table('drivers_detail')
            ->leftJoin('supply_datas', 'drivers_detail.supply_id', '=', 'supply_datas.sup_id')
            ->whereNull('drivers_detail.deleted_at')
            ->orderBy('drivers_detail.created_at', 'desc');

        // 🔒 Data Access Logic (สิทธิ์การมองเห็นคนขับ)
        if ($userRole == 'company') {
            // Company: เห็นเฉพาะคนขับในบริษัทตัวเอง (รวม Supply ใต้สังกัด)
            $query->where('drivers_detail.company_id', $userCompanyCode);
        } elseif ($userRole == 'supply') {
            // Supply: เห็นเฉพาะคนขับของ Supply ตัวเองเท่านั้น
            $query->where('drivers_detail.supply_id', $userSupplyId);
        } elseif ($userRole == 'staff') {
            // Staff (Admin): ไม่ต้องใส่ where เงื่อนไข (เห็นทั้งหมด)
        }

        $drivers = $query->get();

        return view('pages.drivers.index', compact('drivers'));
    }

    // ==========================================
    // 2. หน้าฟอร์มเพิ่มข้อมูล (Create)
    // ==========================================
    public function create()
    {
        $userRole = Auth::user()->role;
        $user = Auth::user();

        $companies = [];
        $supplies = [];

        if ($userRole === Role::Staff) {
            $companies = DB::table('company_details')->get();
        } elseif ($userRole === Role::Company) {
            $supplies = DB::table('supply_datas')
                ->where('company_code', $user->company_code)
                ->where('supply_status', '1')
                ->get();
        }

        return view('pages.drivers.create', compact('companies', 'supplies'));
    }

    public function getSupplies(Request $request)
    {
        $company_id = $request->get('company_id');

        // ดึงข้อมูลจากตาราง supply_datas โดยใช้ company_code (หรือ company_id ตามโครงสร้างจริง)
        $supplies = DB::table('supply_datas')
            ->where('company_code', $company_id)
            ->where('supply_status', '1')
            ->get(['sup_id', 'supply_name']);

        return response()->json($supplies);
    }

    public function checkDuplicate(Request $request)
    {
        $column = $request->get('column');
        $value = $request->get('value');
        $ignore_id = $request->get('ignore_id');

      $query = DB::table('drivers_detail')
                    ->where($column, $value)
                    ->whereNull('deleted_at');

        if ($ignore_id) {
            $query->where('id', '!=', $ignore_id);
        }

       return response()->json(['exists' => $query->exists()]);
    }

    // ==========================================
    // 3. บันทึกข้อมูล (Store)
    // ==========================================
    public function store(Request $request)
    {
        // Validate ข้อมูลเบื้องต้น
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'id_card_no' => 'required',
            'company_id' => 'required',
            'supply_id' => 'required',
        ]);

        $generateDriverId = 'DRV-' . Str::upper(Str::random(8));

        DB::table('drivers_detail')->insert([
            'driver_id' => $generateDriverId,
            'company_id' => $request->company_id ?? Auth::user()->company_code,
            'supply_id' => $request->supply_id ?? Auth::user()->supply_user_id,
            'prefix' => $request->prefix,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'id_card_no' => $request->id_card_no,
            'phone' => $request->phone,
            'driver_license_no' => $request->driver_license_no,
            'license_expire_date' => $request->license_expire_date,
            'assigned_car_id' => $request->assigned_car_id,
            'hire_date' => $request->hire_date,
            'remark' => $request->remark,
            'driver_status' => $request->driver_status ?? 1,
            'created_by' => Auth::user()->user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('drivers.index')->with('success', 'เพิ่มข้อมูลพนักงานขับรถสำเร็จ!');
    }

    // ==========================================
    // 4. หน้าฟอร์มแก้ไข (Edit)
    // ==========================================
  public function edit($id)
    {
        $driver = DB::table('drivers_detail')->where('driver_id', $id)->first();
        if (!$driver) {
            return redirect()->route('drivers.index')->with('error', 'ไม่พบข้อมูลพนักงานขับรถ');
        }

        $userRole = Auth::user()->role;
        $userRoleStr = is_object($userRole) ? $userRole->value : $userRole;
        $companies = [];
        $supplies = [];
        $staffSupplies = []; // เอาไว้เก็บ Supply กรณีที่เป็น Staff เพื่อให้ Dropdown มีข้อมูลตอนโหลดหน้าแรก

        if ($userRoleStr === 'staff') {        
            $companies = DB::table('company_details')->get();
            // ดึง Supply ของบริษัทที่พนักงานคนนี้สังกัดอยู่มาแสดงรอไว้เลย
            $staffSupplies = DB::table('supply_datas')
                ->where('company_code', $driver->company_id)
                ->where('supply_status', '1')
                ->get();
        } elseif ($userRoleStr === 'company') {
            $supplies = DB::table('supply_datas')
                ->where('company_code', Auth::user()->company_code)
                ->where('supply_status', '1')
                ->get();
        }

        // ฟังก์ชันแปลง ค.ศ. (DB) เป็น พ.ศ. เพื่อไปแสดงใน Input
        $formatDateBE = function($date) {
            if (!$date) return '';
            $d = Carbon::parse($date);
            return $d->format('d/m/') . ($d->year + 543);
        };

        $driver->license_expire_date_show = $formatDateBE($driver->license_expire_date);
        $driver->hire_date_show = $formatDateBE($driver->hire_date);

        return view('pages.drivers.edit', compact('driver', 'companies', 'supplies', 'staffSupplies'));
    }

     // ==========================================
    // 5. หน้าฟอร์ม Update
    // ==========================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'id_card_no' => 'required',
        ]);

        DB::table('drivers_detail')->where('driver_id', $id)->update([
            'company_id' => $request->company_id ?? Auth::user()->company_code,
            'supply_id' => $request->supply_id ?? Auth::user()->supply_user_id,
            'prefix' => $request->prefix,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'id_card_no' => $request->id_card_no,
            'phone' => $request->phone,
            'driver_license_no' => $request->driver_license_no,
            'license_expire_date' => $request->license_expire_date,
            'assigned_car_id' => $request->assigned_car_id,
            'hire_date' => $request->hire_date,
            'remark' => $request->remark,
            'driver_status' => $request->driver_status, 
            'updated_by' => Auth::user()->user_id,
            'updated_at' => now(),
        ]);

        return redirect()->route('drivers.index')->with('success', 'อัปเดตข้อมูลพนักงานขับรถสำเร็จ!');
    }

    // ==========================================
    // 6. ลบข้อมูล (Delete / Soft Delete)
    // ==========================================
    public function destroy($id)
    {
        DB::table('drivers_detail')->where('driver_id', $id)->update([
            'deleted_at' => now(),
            'updated_by' => Auth::user()->user_id,
        ]);

        return redirect()->route('drivers.index')->with('success', 'ลบข้อมูลสำเร็จ!');
    }
}
