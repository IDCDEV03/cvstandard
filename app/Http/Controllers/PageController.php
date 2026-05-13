<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\Role;

class PageController extends Controller
{
    public function home(Request $request)
    {
        if (!in_array(auth()->user()->role, [Role::User, Role::Manager, Role::Agency, Role::Admin, Role::Manager, Role::Staff, Role::Company, Role::Supply, Role::Inspector])) {
            abort(403);
        }

        $user = Auth::user();
        $role = $user->role;

        $layout = match ($role) {
            Role::Admin => 'layout.LayoutAdmin',
            Role::Manager => 'layout.app',
            Role::Agency => 'layout.app',
            Role::Company => 'layout.app',
            Role::User => 'layout.app',
            Role::Staff => 'layout.app',
            Role::Supply => 'layout.app',
            Role::Inspector => 'layout.app',
        };

        $title = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'หน้าหลักหน่วยงาน',
            Role::Company => 'หน้าหลักหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
            Role::Staff => 'หน้าหลักเจ้าหน้าที่',
            Role::Supply => 'หน้าหลักเจ้าหน้าที่',
            Role::Inspector => 'หน้าหลักผู้ตรวจ',
        };

        $description = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'สำหรับหน่วยงาน',
            Role::Company => 'สำหรับหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
            Role::Staff => 'หน้าหลักเจ้าหน้าที่',
            Role::Supply => 'หน้าหลักเจ้าหน้าที่',
            Role::Inspector => 'ผู้ใช้งานทั่วไป',
        };

        if ($role === Role::User) {

            $user_main_id = Auth::user()->user_id;

            $user_sup = DB::table('inspector_datas')
                ->where('ins_id', $user_main_id)
                ->first();

            $vehicles = DB::table('vehicles_detail')
                ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
                ->leftJoin('chk_records', function ($join) {
                    $join->on('vehicles_detail.car_id', '=', 'chk_records.veh_id')
                        ->whereRaw('chk_records.id IN (select MAX(id) from chk_records GROUP BY veh_id)');
                })
                ->select(
                    'vehicles_detail.*',
                    'vehicle_types.vehicle_type',
                    'chk_records.chk_status',
                    'chk_records.record_id as chk_primary_id'
                )
                ->where('vehicles_detail.supply_id', '=', $user_sup->sup_id)
                ->get();;

            return view('pages.user.MainPage', compact('vehicles'));
        } elseif ($role === Role::Inspector) {

            $user_main_id = Auth::user()->user_id;
            $filter = $request->get('filter', 'all'); // รับค่า filter จาก URL (ค่าเริ่มต้นคือ all)

            // 1. ดึงข้อมูล Profile และสิทธิ์ (เหมือนเดิม)
            $user_sup = DB::table('inspector_datas')->where('ins_id', $user_main_id)->first();
            $allowedSupplyIds = [];
            if ($user_sup->inspector_type == '3') {
                $allowedSupplyIds = DB::table('inspector_supply_access')->where('ins_id', $user_main_id)->pluck('supply_id')->toArray();
            }

            // 2. ดึงข้อมูลรถทั้งหมดมาก่อน (เพื่อเอาไปคำนวณ Widget)
            $allVehicles = DB::table('vehicles_detail')
                ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
                ->select('vehicles_detail.*', 'vehicle_types.vehicle_type')
                ->when($user_sup->inspector_type == '1', function ($query) use ($user_sup) {
                    return $query->where('vehicles_detail.company_code', $user_sup->company_code);
                })
                ->when($user_sup->inspector_type == '2', function ($query) use ($user_sup) {
                    return $query->where('vehicles_detail.supply_id', $user_sup->sup_id);
                })
                ->when($user_sup->inspector_type == '3', function ($query) use ($allowedSupplyIds) {
                    return $query->whereIn('vehicles_detail.supply_id', $allowedSupplyIds);
                })
                ->get();

            // ==========================================
            // 💡 การจัดกลุ่มประวัติ และคำนวณสถิติ
            // ==========================================
            $passedInspections = 0;
            $waitingInspections = 0;
            $failedInspections = 0;
            $totalInspections = 0;

            $filteredVehicles = []; // รถที่จะเอาไปแสดงในตาราง

            foreach ($allVehicles as $car) {
                $history = DB::table('chk_records')
                    ->where('veh_id', $car->car_id)
                    ->where('user_id', $user_main_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $car->history = $history;
                $car->inspect_count = $history->count();
                $car->latest_record = $history->first();

                // ตรวจสอบว่ามีช่างคนอื่นกำลังตรวจอยู่หรือไม่
                $car->in_progress_by_other = DB::table('chk_records as cr')
                    ->join('inspector_datas as ins', 'cr.user_id', '=', 'ins.ins_id')
                    ->where('cr.veh_id', $car->car_id)
                    ->whereIn('cr.chk_status', ['0', '2'])
                    ->where('cr.user_id', '!=', $user_main_id)
                    ->selectRaw("cr.record_id, cr.user_id, CONCAT(ins.ins_prefix, ins.ins_name, ' ', ins.ins_lastname) as inspector_name")
                    ->orderBy('cr.created_at', 'desc')
                    ->first();

                // 📌 นับสถิติจาก "สถานะล่าสุด" ของรถแต่ละคัน (ต้องตรวจเสร็จแล้ว chk_status = 1 ถึงจะนับผลประเมิน)
                if ($car->latest_record && $car->latest_record->chk_status == '1') {
                    $totalInspections++; // นับรวมทุกคันที่เคยตรวจโดย User คนนี้

                    if ($car->latest_record->evaluate_status == 1) {
                        $passedInspections++;
                    } elseif ($car->latest_record->evaluate_status == 2) {
                        $waitingInspections++;
                    } elseif ($car->latest_record->evaluate_status == 3) {
                        $failedInspections++;
                    }
                }

                // 📌 กรองรถเข้าตาราง ตามปุ่มที่กด (Filter)
                $shouldInclude = false;
                if ($filter == 'all') {
                    // แสดงเฉพาะรถที่มีประวัติการตรวจ (ตนเองหรือช่างคนอื่นกำลังตรวจ)
                    $shouldInclude = $car->inspect_count > 0 || $car->in_progress_by_other;
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

            $vehicles = $filteredVehicles; // เปลี่ยนตัวแปรให้ตรงกับที่ Blade เรียกใช้

            return view('pages.inspector.dashboard', compact(
                'vehicles',
                'passedInspections',
                'waitingInspections',
                'failedInspections',
                'totalInspections',
                'filter' // ส่งค่ากลับไปเพื่อให้รู้ว่าตอนนี้กดปุ่มไหนอยู่
            ));
        } elseif ($role === Role::Agency) {
            $id = Auth::id();
            $agency = DB::table('users')->where('id', Auth::id())->first();

            $managers = DB::table('users')
                ->where('agency_id', $id)
                ->where('role', 'manager')
                ->get();

            $users = DB::table('users')
                ->where('agency_id', $id)
                ->where('role', 'user')
                ->get();
            return view('pages.agency.index', compact('agency', 'managers', 'users'));
        } elseif ($role === Role::Manager) {
            $id = Auth::id();
            $manager = DB::table('users')->where('id', Auth::id())->first();

            return view('pages.manager.index', compact('manager'));
        } elseif ($role === Role::Company) {

            $user = Auth::user();
            $companyCode = $user->company_code;
            $companyDetails = DB::table('company_details')
                ->where('company_id', $companyCode)
                ->first();

            $formCount = DB::table('forms')->where('user_id', $companyCode)->count();

            $supplyCount = DB::table('users')
                ->where('company_code', $companyCode)
                ->where('role', 'supply')
                ->count();

            $driverCount = DB::table('drivers_detail')->where('company_id', $companyCode)->count();

            $InspectorCount = DB::table('inspector_datas')->where('company_code', $companyCode)->count();

            $totalVehicleLimit = DB::table('supply_datas')
                ->where('company_code', $companyCode)
                ->sum('vehicle_limit');

            $totalVehicles = DB::table('vehicles_detail')
                ->where('company_code', $companyCode)
                ->where('status', '1')
                ->count();


            $inspections = DB::table('chk_records')
                ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
                ->where('vehicles_detail.company_code', $companyCode)
                ->where('chk_records.chk_status', '1') // 1=ครบ
                ->select('chk_records.evaluate_status')
                ->get();

            $totalInspected = $inspections->count();

            // 3. แยกผ่าน/ไม่ผ่าน (อิงจาก evaluate_status: 1=อนุญาตให้ใช้, 2=มีเงื่อนไข, 3=ไม่อนุญาต)
            // 1 คือผ่าน และ 2,3 คือไม่ผ่าน 
            $passCount = $inspections->where('evaluate_status', 1)->count();
            $failCount = $inspections->whereIn('evaluate_status', [2, 3])->count();

            // 4. คำนวณเปอร์เซ็นต์ (ป้องกันหารด้วย 0)
            $passPercent = $totalInspected > 0 ? round(($passCount / $totalInspected) * 100) : 0;
            $failPercent = $totalInspected > 0 ? round(($failCount / $totalInspected) * 100) : 0;

            // นับพนักงานทั้งหมดใน company (status = 1 และยังไม่ถูกลบ)
            $totalDrivers = DB::table('drivers_detail')
                ->where('company_id', $companyCode)
                ->where('driver_status', 1)
                ->whereNull('deleted_at')
                ->count();

            // หา driver_id ที่มี doc_type = cert (is_active = 1)
            $certDriverIds = DB::table('drivers_document')
                ->join('drivers_detail as d', 'd.driver_id', '=', 'drivers_document.driver_id')
                ->where('d.company_id', $companyCode)
                ->where('d.driver_status', 1)
                ->whereNull('d.deleted_at')
                ->where('drivers_document.doc_type', 'cert')
                ->where('drivers_document.is_active', 1)
                ->distinct()
                ->count('drivers_document.driver_id');

            $noCertDrivers = $totalDrivers - $certDriverIds;
            $certPercent   = $totalDrivers > 0 ? round(($certDriverIds / $totalDrivers) * 100) : 0;
            $noPercent     = $totalDrivers > 0 ? 100 - $certPercent : 0;

            // ── ข้อมูลการตรวจรถ 7 วันย้อนหลัง ──
            $dailyRaw = DB::table('chk_records')
                ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
                ->where('vehicles_detail.company_code', $companyCode)
                ->where('chk_records.chk_status', '1')
                ->where('chk_records.created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->selectRaw('DATE(chk_records.created_at) as chk_date,
                 COUNT(*) as total,
                 SUM(CASE WHEN chk_records.evaluate_status = 1 THEN 1 ELSE 0 END) as pass_count,
                 SUM(CASE WHEN chk_records.evaluate_status IN (2,3) THEN 1 ELSE 0 END) as fail_count')
                ->groupByRaw('DATE(chk_records.created_at)')
                ->orderBy('chk_date', 'asc')
                ->get()
                ->keyBy('chk_date');

            // สร้าง array ครบ 7 วัน (เติม 0 วันที่ไม่มีข้อมูล)
            $dailyLabels = [];
            $dailyTotal  = [];
            $dailyPass   = [];
            $dailyFail   = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->toDateString();
                $dailyLabels[] = Carbon::now()->subDays($i)->locale('th')->isoFormat('D MMM');
                $row = $dailyRaw->get($date);
                $dailyTotal[]  = $row ? (int) $row->total      : 0;
                $dailyPass[]   = $row ? (int) $row->pass_count : 0;
                $dailyFail[]   = $row ? (int) $row->fail_count : 0;
            }

            // สถิติวันนี้
            $todayKey    = Carbon::today()->toDateString();
            $todayRow    = $dailyRaw->get($todayKey);
            $todayTotal  = $todayRow ? (int) $todayRow->total      : 0;
            $todayPass   = $todayRow ? (int) $todayRow->pass_count : 0;
            $todayFail   = $todayRow ? (int) $todayRow->fail_count : 0;

            return view('pages.company.dashboard', compact(
                'user',
                'companyDetails',
                'supplyCount',
                'formCount',
                'totalVehicleLimit',
                'driverCount',
                'InspectorCount',
                'totalVehicles',
                'totalInspected',
                'passCount',
                'failCount',
                'passPercent',
                'failPercent',
                'totalDrivers',
                'certDriverIds',
                'noCertDrivers',
                'certPercent',
                'noPercent',
                'dailyLabels',
                'dailyTotal',
                'dailyPass',
                'dailyFail',
                'todayTotal',
                'todayPass',
                'todayFail'
            ));
        } elseif ($role === Role::Staff) {
            $id = Auth::id();
            $staff = DB::table('users')->where('id', Auth::id())->first();


            return view('pages.staff.index', compact('staff'));
        } elseif ($role === Role::Supply) {
            $id = Auth::id();
            $user_id = Auth::user()->user_id;
            $supply = DB::table('users')->where('id', Auth::id())->first();

            $chk_list = DB::table('chk_records')
                ->select('chk_records.created_at as date_check', 'chk_records.form_id', 'chk_records.record_id', 'vehicles_detail.car_plate', 'vehicles_detail.car_number_record', 'chk_records.veh_id')
                ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
                ->where('chk_records.supply_id', $user_id)
                ->groupBy('chk_records.record_id')
                ->get();
            return view('pages.supply.home', compact('supply', 'chk_list', 'user_id'));
        } else {
            return view('pages.local.home', compact('layout', 'title', 'description'));
        }
    }
    public function coming_soon()
    {
        return view('pages.local.ComingSoon');
    }
}
