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
        if (!in_array(auth()->user()->role, [Role::User, Role::Manager, Role::Agency, Role::Admin, Role::Manager])) {
            abort(403);
        }

        $user = Auth::user();
        $role = $user->role;

        $layout = match ($role) {
            Role::Admin => 'layout.LayoutAdmin',
            Role::Manager => 'layout.app',
            Role::Agency => 'layout.app',
            Role::User => 'layout.app',
        };

        $title = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'หน้าหลักหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
        };

        $description = match ($role) {
            Role::Admin => 'ผู้ดูแลระบบ',
            Role::Manager => 'แดชบอร์ดผู้จัดการ',
            Role::Agency => 'สำหรับหน่วยงาน',
            Role::User => 'แดชบอร์ดผู้ใช้งานทั่วไป',
        };

        if ($role === Role::User) {
            $vehicles = DB::table('vehicles')
                ->join('vehicle_types', 'vehicles.veh_type', '=', 'vehicle_types.id')
                ->select('vehicles.*', 'vehicle_types.vehicle_type as veh_type_name')
                ->where('vehicles.user_id', '=', Auth::id())
                ->orderBy('vehicles.updated_at', 'DESC')
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
        else {
            return view('pages.local.home', compact('layout', 'title', 'description'));
        }
    }
    public function coming_soon()
    {
        return view('pages.local.ComingSoon');
    }
}
