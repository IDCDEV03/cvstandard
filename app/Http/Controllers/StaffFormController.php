<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;

class StaffFormController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function FormList()
    {
        $form_list = DB::table('forms')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('pages.staff.FormList', compact('form_list'));
    }

    public function FormNew()
    {
        $car_type = DB::table('vehicle_types')->get();

        $supply_list = DB::table('supply_datas')
            ->where('supply_status', '1')
            ->orderBy('supply_name', 'ASC')
            ->get();

        return view('pages.staff.FormNew', compact('car_type', 'supply_list'));
    }

    public function FormStore(Request $request)
    {

        $form_id = Str::upper(Str::random(8));

        if ($request->form_scope === 'public') {
            DB::table('forms')->insert([
                'user_id'       => Auth::user()->user_id,
                'form_id'       => $form_id,
                'form_code'     => $request->input('form_code'),
                'form_name'     => $request->form_name,
                'form_category' => $request->form_category,
                'form_status'   => '1',
                'form_open'     => 'public',
                'created_at'    =>  Carbon::now(),
                'updated_at'    =>  Carbon::now()
            ]);
        } else {
            // Insert ฟอร์มครั้งเดียว
            DB::table('forms')->insert([
                'user_id'       => Auth::user()->user_id,
                'form_id'       => $form_id,
                'form_code'     => $request->input('form_code'),
                'form_name'     => $request->form_name,
                'form_category' => $request->form_category,
                'form_status'   => '1',
                'form_open'     => 'public',
                'created_at'    =>  Carbon::now(),
                'updated_at'    =>  Carbon::now()
            ]);

            // loop เก็บความสัมพันธ์กับหลายหน่วยงาน
            foreach ($request->agency_ids as $agencyId) {
                DB::table('form_permission')->insert([
                    'form_id' => $formId,
                    'agency_id' => $agencyId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
