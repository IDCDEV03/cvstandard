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
            'agency_id' => '5',
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
            'user_status' => '1',
            'email' => $request->company_email,
            'password' => Hash::make($request->password),
            'user_phone' => $request->company_phone,
            'role' => 'company',
            'company_code' => $comp_id,
            'agency_id' => '5',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
    }

    public function UpdateStatus($id, $status)
    {
        if ($status == '2') {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'user_status'      => '2',
                    'updated_at' =>  Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
        } elseif ($status == '1') {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'user_status'      => '1',
                    'updated_at' =>  Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
        }
    }

    public function CompanyUpdate(Request $request, $id, $tab)
    {
        $company_id = DB::table('users')->where('id', $id)->first();

        if ($tab == 'part1') {

            DB::table('company_details')
                ->where('company_id', $company_id->company_code)
                ->update([
                    'company_address' => $request->company_address,
                    'company_province' => $request->province,
                    'updated_at' => Carbon::now(),
                ]);

            DB::table('users')
                ->where('company_code', $company_id->company_code)
                ->update([
                    'name' => $request->company_name,
                    'email' => $request->company_email,
                    'user_phone' => $request->company_phone,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part2') {

            $usernameExists = DB::table('users')->where('username', $request->company_user)->exists();

            if ($usernameExists) {
                return back()
                    ->withInput()
                    ->withErrors(['company_username' => 'Username นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น']);
            }

            DB::table('users')
                ->where('company_code', $company_id->company_code)
                ->update([
                    'username' => $request->company_user,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part3') {
            DB::table('users')
                ->where('company_code', $company_id->company_code)
                ->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        }
    }

    public function SupList($id)
    {
       $company_name = DB::table('users')
        ->where('company_code','=',$id)
        ->first();

    return view('pages.admin.SupplyList', compact('company_name'));
    }

    public function SupCreate($id)
    {
         $company_name = DB::table('users')
        ->where('company_code','=',$id)
        ->first();
         return view('pages.admin.SupplyCreate', compact('company_name'));
    }
}
