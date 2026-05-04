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

class CompanyDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function index()
    {
        // 1. ดึงข้อมูล User ที่ล็อกอินอยู่
        $user = Auth::user();
        $companyCode = $user->company_code;

        // 2. ดึงข้อมูลรายละเอียดของ Company ตัวเอง
        $companyDetails = DB::table('company_details')
            ->where('company_id', $companyCode)
            ->first();

        // 3. ดึงข้อมูลสถิติต่างๆ (จำลองการ Query ไว้ก่อน เผื่อตาราง Supply คุณยังไม่เสร็จ)
        // จำนวน Supply ที่บริษัทนี้สร้างไว้
        $supplyCount = DB::table('users') // สมมติว่าเก็บ Supply ในตาราง users โดยมี role = 'supply'
            ->where('company_code', $companyCode)
            ->where('role', 'supply')
            ->count();

        // จำนวนฟอร์มที่สร้างไปแล้ว (สมมติว่าคุณมีตาราง forms)
        $formCount = DB::table('forms')->where('user_id', '=', $companyCode)->count();
        //$formCount = 0; // ใส่ 0 ไว้ก่อนรอทำระบบฟอร์ม

        // 4. ส่งข้อมูลไปที่หน้า View
        return view('pages.company.dashboard', compact('user', 'companyDetails', 'supplyCount', 'formCount'));
    }

    public function company_form()
    {
        $user = Auth::user()->user_id;

        $form_list = DB::table('forms')
            ->leftJoin('vehicle_types', 'forms.car_type', '=', 'vehicle_types.id')
            ->select('forms.*', 'vehicle_types.vehicle_type')
            ->where('forms.user_id', $user)
            ->orderBy('forms.updated_at', 'DESC')
            ->get();

        return view('pages.company.form_index', compact('form_list'));
    }

    public function vehicles_information(Request $request)
    {
        $companyCode = Auth::user()->user_id;
        $filter = $request->get('filter', 'all'); // รับค่า filter จาก URL (ค่าเริ่มต้นคือ all)

        // 1. ดึงข้อมูลรถทั้งหมดของบริษัทนี้ พร้อมชื่อ Supply และประเภทรถ
        $allVehicles = DB::table('vehicles_detail')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->leftJoin('supply_datas', 'vehicles_detail.supply_id', '=', 'supply_datas.sup_id')
            ->where('vehicles_detail.company_code', $companyCode)
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type', 'supply_datas.supply_name')
            ->get();

        // ==========================================
        // 💡 การจัดกลุ่มประวัติ และคำนวณสถิติ
        // ==========================================
        $passedInspections = 0;
        $waitingInspections = 0;
        $failedInspections = 0;
        $totalInspections = 0;

        $filteredVehicles = [];

        foreach ($allVehicles as $car) {
            // ดึงประวัติการตรวจของรถคันนี้ **(ดึงทุกรายการ ไม่สนว่าใครตรวจ)**
            $history = DB::table('chk_records')
                ->where('veh_id', $car->car_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $car->history = $history;
            $car->inspect_count = $history->count();
            $car->latest_record = $history->first();

            // 📌 นับสถิติจาก "สถานะล่าสุด" ของรถแต่ละคัน (ต้องตรวจเสร็จแล้ว)
            if ($car->latest_record && $car->latest_record->chk_status == '1') {
                $totalInspections++;

                if ($car->latest_record->evaluate_status == 1) {
                    $passedInspections++;
                } elseif ($car->latest_record->evaluate_status == 2) {
                    $waitingInspections++;
                } elseif ($car->latest_record->evaluate_status == 3) {
                    $failedInspections++;
                }
            }

            // 📌 กรองรถเข้าตาราง ตามปุ่มที่กด
            $shouldInclude = false;
            if ($filter == 'all') {
                $shouldInclude = true;
            } elseif ($car->latest_record && $car->latest_record->chk_status == '1') {
                if ($filter == 'passed' && $car->latest_record->evaluate_status == 1) {
                    $shouldInclude = true;
                } elseif ($filter == 'waiting' && $car->latest_record->evaluate_status == 2) {
                    $shouldInclude = true;
                } elseif ($filter == 'failed' && $car->latest_record->evaluate_status == 3) {
                    $shouldInclude = true;
                }
            }

            // ถ้ารถคันนี้ตรงกับ Filter ค่อยเอาใส่ Array ไปแสดง
            if ($shouldInclude) {
                $filteredVehicles[] = $car;
            }
        }

        $vehicles = $filteredVehicles;

        return view('pages.company.vehicles_information', compact(
            'vehicles',
            'passedInspections',
            'waitingInspections',
            'failedInspections',
            'totalInspections',
            'filter'
        ));
    }
}
