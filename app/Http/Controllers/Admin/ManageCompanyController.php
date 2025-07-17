<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ManageCompanyController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function CompanyStore(Request $request)
    {
          $agent = DB::table('users')->where('id', Auth::id())->first();

        //เช็ค username ซ้ำ 
        $usernameExists = DB::table('users')->where('username', $request->company_user)->exists();

        if ($usernameExists) {
            return back()
                ->withInput()
                ->withErrors(['company_username' => 'Username นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น']);
        }

        $comp_id = 'CP-' . Str::upper(Str::random(9));

        DB::table('company_details')->insert([
            'user_created_id' => Auth::id(),
            'agency_id' => $agent->agency_id,
            'company_id' => $comp_id,
            'company_address' => $request->company_address,
            'company_province' => $request->company_province,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        DB::table('users')->insert([
            'username' => $request->company_user,
            'prefix' => '-',
            'name' => $request->company_name,
            'lastname' => '-',
            'user_status' => '0',
            'email' => $request->company_email,
            'password' => Hash::make($request->password),
            'user_phone' => $request->company_phone,
            'role' => 'company',
            'company_code' => $comp_id,
            'agency_id' => $agent->agency_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
    }
    
}
