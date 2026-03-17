<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AgentCompanyController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->keyword);

        $query = DB::table('company_details')
            ->where('agency_id', Auth::user()->user_id);

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('company_id', 'like', "%{$keyword}%")
                  ->orWhere('company_name', 'like', "%{$keyword}%")
                  ->orWhere('company_province', 'like', "%{$keyword}%");
            });
        }

        $companies = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.agency.Companies_index', compact('companies', 'keyword'));
    }

    public function create()
    {
           $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();


        return view('pages.agency.Companies_create', compact('province'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:200',
            'company_address' => 'nullable|string',
            'company_province' => 'nullable|string|max:30',
            'form_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'company_name.required' => 'กรุณากรอกชื่อบริษัท',
            'company_name.max' => 'ชื่อบริษัทต้องไม่เกิน 200 ตัวอักษร',
            'company_province.max' => 'จังหวัดต้องไม่เกิน 30 ตัวอักษร',
            'form_limit.integer' => 'จำนวนฟอร์มต้องเป็นตัวเลข',
            'form_limit.min' => 'จำนวนฟอร์มต้องไม่น้อยกว่า 0',
            'expire_date.after_or_equal' => 'วันหมดอายุต้องไม่น้อยกว่าวันเริ่มใช้งาน',
        ]);

        // ป้องกัน company_id ซ้ำแบบง่าย
        do {
            $companyId = 'COM' . strtoupper(Str::random(8));
            $exists = DB::table('company_details')->where('company_id', $companyId)->exists();
        } while ($exists);

        DB::table('company_details')->insert([
            'user_created_id' => auth()->id(),
            'agency_id' => null, // ถ้ายังไม่ได้ใช้ตาราง agencies ให้ null ไปก่อน
            'company_id' => $companyId,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_province' => $request->company_province,
            'agency_user_id' => auth()->id(),
            'form_limit' => $request->form_limit ?? 0,
            'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('companies.index')
            ->with('success', 'สร้างบริษัทเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $company = DB::table('company_details')
            ->where('id', $id)
            ->where('agency_user_id', auth()->id())
            ->first();

        if (!$company) {
            abort(404);
        }

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = DB::table('company_details')
            ->where('id', $id)
            ->where('agency_user_id', auth()->id())
            ->first();

        if (!$company) {
            abort(404);
        }

        $request->validate([
            'company_name' => 'required|string|max:200',
            'company_address' => 'nullable|string',
            'company_province' => 'nullable|string|max:30',
            'form_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'company_name.required' => 'กรุณากรอกชื่อบริษัท',
            'company_name.max' => 'ชื่อบริษัทต้องไม่เกิน 200 ตัวอักษร',
            'company_province.max' => 'จังหวัดต้องไม่เกิน 30 ตัวอักษร',
            'form_limit.integer' => 'จำนวนฟอร์มต้องเป็นตัวเลข',
            'form_limit.min' => 'จำนวนฟอร์มต้องไม่น้อยกว่า 0',
            'expire_date.after_or_equal' => 'วันหมดอายุต้องไม่น้อยกว่าวันเริ่มใช้งาน',
        ]);

        DB::table('company_details')
            ->where('id', $id)
            ->where('agency_user_id', auth()->id())
            ->update([
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_province' => $request->company_province,
                'form_limit' => $request->form_limit ?? 0,
                'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
                'start_date' => $request->start_date,
                'expire_date' => $request->expire_date,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('companies.index')
            ->with('success', 'แก้ไขข้อมูลบริษัทเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $company = DB::table('company_details')
            ->where('id', $id)
            ->where('agency_user_id', auth()->id())
            ->first();

        if (!$company) {
            abort(404);
        }

        DB::table('company_details')
            ->where('id', $id)
            ->where('agency_user_id', auth()->id())
            ->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'ลบบริษัทเรียบร้อยแล้ว');
    }
}
