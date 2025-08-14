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

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function VehiclesList()
    {
        $veh_list = DB::table('vehicles_detail')
        ->select('vehicles_detail.*', 'users.company_code','users.name','vehicle_types.vehicle_type')
        ->join('users','users.company_code','=','vehicles_detail.company_code')
        ->join('vehicle_types','vehicles_detail.car_type','vehicle_types.id')
        ->orderBy('vehicles_detail.created_at','DESC')
        ->get();

        return view('pages.staff.VehiclesList',compact('veh_list'));
    }

    public function VehiclesRegister()
    {

        $car_type = DB::table('vehicle_types')
            ->select('id', 'vehicle_type')
            ->orderBy('id', 'ASC')
            ->get();

        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $car_brand = DB::table('car_brands')
            ->orderBy('brand_name', 'ASC')
            ->get();

        $company_list = DB::table('users')
            ->where('role', '=', 'company')
            ->where('user_status', '=', '1')
            ->get();

        return view('pages.staff.VehiclesRegister', compact('car_type', 'province', 'car_brand', 'company_list'));
    }

    public function VehiclesInsert(Request $request)
    {
        $staff_id = Auth::id();

        if (empty($request->province)) {
            return redirect()->back()->with('error', 'กรุณาเลือกทะเบียนจังหวัด');
        }

        if (empty($request->car_brand)) {
            return redirect()->back()->with('error', 'กรุณาเลือกยี่ห้อรถ');
        }

        $rawInput = $request->input('plate');
        $cleanPlate = str_replace(' ', '', $rawInput); //ตัดช่องว่างออก

        $car_plate = $cleanPlate . " " . $request->province;
        $veh_id = 'VEH-' . Str::upper(Str::random(9));

        DB::table('vehicles_detail')
            ->insert([
                'user_id' => $staff_id,
                'company_code' => $request->company_code,
                'car_id' => $veh_id,
                'car_plate' => $car_plate,
                'car_brand' => $request->car_brand,
                'car_model' => $request->car_model,
                'car_number_record' => $request->car_number_record,
                'car_age' => $request->car_age,
                'car_mileage' => $request->car_mileage,
                'car_tax' => $request->car_tax,
                'car_insure' => $request->car_insure,
                'car_type' => $request->car_type,
                'status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

             return redirect()->route('staff.veh_list')->with('success', 'บันทึกสำเร็จ');
    }
}
