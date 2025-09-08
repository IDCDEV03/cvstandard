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
    public function home()
    {
        if (!in_array(auth()->user()->role, [Role::User, Role::Manager, Role::Agency, Role::Admin, Role::Manager,Role::Staff,Role::Company,Role::Supply])) {
            abort(403);
        }

        $user = Auth::user();
        $role = $user->role;

        $layout = match ($role) {
            Role::Admin => 'layout.LayoutAdmin',
            Role::Manager => 'layout.app',
            Role::Agency => 'layout.app',
            Role::User => 'layout.app',
            Role::Staff => 'layout.app',
             Role::Supply => 'layout.app',
        };

        $title = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'หน้าหลักหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
            Role::Staff => 'หน้าหลักเจ้าหน้าที่',
             Role::Supply => 'หน้าหลักเจ้าหน้าที่',
        };

        $description = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'สำหรับหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
            Role::Staff => 'หน้าหลักเจ้าหน้าที่',
            Role::Supply => 'หน้าหลักเจ้าหน้าที่',
        };

        if ($role === Role::User) {

            $user_main_id = Auth::id();
            $user_gen_id = DB::table('users')
            ->where('id','=',$user_main_id)
            ->first();

            $user_gen = $user_gen_id->user_id;

            $user_sup = DB::table('inspector_datas')
            ->where('ins_id',$user_gen)
            ->first();

            $vehicles = DB::table('vehicles_detail')
            ->select('vehicles_detail.*','vehicle_types.vehicle_type')
            ->join('vehicle_types','vehicle_types.id','=','vehicles_detail.car_type')
            ->where('vehicles_detail.supply_id',$user_sup->sup_id)                
             ->get();

            return view('pages.user.MainPage', compact('vehicles'));
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
        }
        elseif ($role === Role::Manager) {
            $id = Auth::id();
            $manager = DB::table('users')->where('id', Auth::id())->first();

           
            return view('pages.manager.index', compact('manager'));
        }
          elseif ($role === Role::Staff) {
            $id = Auth::id();
            $staff = DB::table('users')->where('id', Auth::id())->first();

           
            return view('pages.staff.index', compact('staff'));
        }
        elseif ($role === Role::Supply) {
            $id = Auth::id();
            $supply = DB::table('users')->where('id', Auth::id())->first();

            return view('pages.supply.home', compact('supply'));
        }
        else {
            return view('pages.local.home', compact('layout', 'title', 'description'));
        }
    }
    public function coming_soon()
    {
        return view('pages.local.ComingSoon');
    }
}
