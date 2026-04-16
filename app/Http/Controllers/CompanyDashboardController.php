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
        $companyCode = $user->company_code; // อ้างอิงจากตาราง users ที่คุณเก็บค่า company_id ไว้ที่นี่

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
        // $formCount = DB::table('forms')->where('company_id', $companyCode)->count();
        $formCount = 0; // ใส่ 0 ไว้ก่อนรอทำระบบฟอร์ม

        // 4. ส่งข้อมูลไปที่หน้า View
        return view('pages.company.dashboard', compact('user', 'companyDetails', 'supplyCount', 'formCount'));
    }

    public function company_form()
    {
        return view('pages.company.form_index');
    }

}
