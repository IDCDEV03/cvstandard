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

   //บันทึกข้อมูล supply
    public function store(Request $request)
    {
        
        $user = Auth::user();
        $companyCode = $user->company_code; 
        $agencyId = $user->agency_user_id ?? $user->agency_id; 

        $request->validate([
            'supply_name' => 'required|string|max:200',
            'supply_address' => 'required|string',
            'vehicle_limit' => 'nullable|integer|min:0', 
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'supply_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supply_user' => 'required|string|unique:users,username', 
            'supply_password' => 'required|string|min:6',
        ]);

       do {
            $supId = 'SUP-' . strtoupper(Str::random(6));
            $exists = DB::table('supply_datas')->where('sup_id', $supId)->exists();
        } while ($exists);
   
        $logoPath = null;
        if ($request->hasFile('supply_logo')) {
            $file = $request->file('supply_logo');
            $filename = $supId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo/supply'), $filename); // เก็บแยกโฟลเดอร์ให้เป็นระเบียบ
            $logoPath = 'logo/supply/' . $filename;
        }

     
        DB::beginTransaction();

        try {
            DB::table('supply_datas')->insert([
                'company_code' => $companyCode,
                'agency_user_id' => $agencyId,
                'sup_id' => $supId,
                'supply_name' => $request->supply_name,
                'supply_logo' => $logoPath,
                'supply_address' => $request->supply_address,
                'supply_phone' => $request->supply_phone,
                'supply_email' => $request->supply_email,
                'supply_status' => '1', 
                'vehicle_limit' => $request->vehicle_limit ?? 0,
                'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
                'start_date' => $request->start_date,
                'expire_date' => $request->expire_date,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

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
                ->with('success', 'สร้างบริษัทในเครือ(Supply) เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function SupIndex()
    {
        $companyCode = Auth::user()->company_code;

      $supplies = DB::table('supply_datas')
        ->where('company_code', $companyCode)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('pages.company.supplies_index', compact('supplies'));
    }

}
