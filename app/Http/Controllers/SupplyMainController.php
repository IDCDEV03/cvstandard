<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class SupplyMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supply']);
    }

    public function Vehicles_Create()
    {
         $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $car_brand = DB::table('car_brands')
            ->orderBy('brand_name', 'ASC')
            ->get();

             $car_type = DB::table('vehicle_types')
            ->select('id', 'vehicle_type')
            ->orderBy('vehicle_type', 'ASC')
            ->get();

        return view('pages.supply.CarCreate',compact('province','car_brand','car_type'));
    }

      public function VehiclesInsert(Request $request)
    {
        $supply_id = Auth::user()->user_id;

        $company = DB::table('supply_datas')
        ->where('sup_id',$supply_id)
        ->first();

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
                'user_id' => $supply_id,
                'company_code' => $company->company_code,
                'supply_id' => $supply_id,
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

        return redirect()->route('supply.veh_list')->with('success', 'บันทึกสำเร็จ');
    }

     public function VehiclesList()
    {
        $supply_id = Auth::user()->user_id;
        $veh_list = DB::table('vehicles_detail')
           ->where('supply_id',$supply_id)
            ->get();

        return view('pages.supply.CarList', compact('veh_list'));
    }
}
