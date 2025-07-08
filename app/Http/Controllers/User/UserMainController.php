<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class UserMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('pages.user.UserProfile', compact('user'));
    }

       public function announce()
    {
        $list_post = DB::table('announcements')
            ->join('users', 'announcements.user_id', '=', 'users.id')
            ->select('users.name', 'users.role', 'users.id', 'announcements.id as post_id', 'announcements.title', 'announcements.description', 'announcements.file_upload', 'announcements.updated_at', 'announcements.created_at')
            ->orderBy('announcements.updated_at', 'DESC')
            ->get();

        return view('pages.local.announce', compact('list_post'));
    }


    public function veh_regis()
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
            ->orderBy('brand_name','ASC')
            ->get();

        return view('pages.user.VehiclesRegister', compact('car_type', 'province','car_brand'));
    }

    public function veh_insert(Request $request)
    {

        $agent = DB::table('users')->where('id', Auth::id())->first();

        if (empty($request->province)) {
            return redirect()->back()->with('error', 'กรุณาเลือกทะเบียนจังหวัด');
        }

        if (empty($request->veh_brand)) {
            return redirect()->back()->with('error', 'กรุณาเลือกยี่ห้อรถ');
        }


        $veh_id = 'VEH-' . Str::upper(Str::random(9));

        $rawInput = $request->input('plate');
        $cleanPlate = str_replace(' ', '', $rawInput); //ตัดช่องว่างออก

        // upload image
        $upload_location = 'upload/';
        $file = $request->file('vehicle_image');
        $extension = $file->getClientOriginalExtension();
        $newName = $cleanPlate . '_' . Carbon::now()->format('Ymd_His') . '.' . $extension;
        $file->move($upload_location, $newName);
        $fileName = $upload_location . $newName;

        DB::table('vehicles')->insert([
            'veh_id' => $veh_id,
            'veh_brand' => $request->veh_brand,
            'plate' => $cleanPlate,
            'province' => $request->province,
            'user_id' => Auth::id(),
            'veh_status' => '1',
            'veh_type' => $request->vehicle_type,
            'veh_image' => $fileName,
            'agency_id' => $agent->agency_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('local.home')->with('success', 'บันทึกสำเร็จ');
    }

    public function chk_list()
    {
        $user_id = Auth::user()->id;
        $record = DB::table('chk_records')
            ->join('vehicles', 'chk_records.veh_id', '=', 'vehicles.veh_id')
            ->join('vehicle_types', 'vehicles.veh_type', '=', 'vehicle_types.id')
            ->select(
                'vehicles.*',
                'vehicle_types.vehicle_type as veh_type_name',
                'chk_records.created_at as date_check',
                'chk_records.form_id',
                'chk_records.record_id',
                'chk_records.user_id as chk_user',
                'chk_records.agency_id as chk_agent'
            )
            ->where('chk_records.user_id', '=', $user_id)
            ->orderBy('chk_records.created_at', 'DESC')
            ->get();

        return view('pages.user.ChkList', compact('record'));
    }

    public function start_check($id)
    {
        $forms = DB::table('forms')
            ->where('form_status', '=', '1')
            ->orderBy('form_name', 'ASC')
            ->get();

        $veh_detail = DB::table('vehicles')
            ->select('plate', 'province')
            ->where('veh_id', $id)
            ->first();

        return view('pages.user.ChkStart', compact('forms', 'veh_detail'));
    }


    public function insert_step1(Request $request, $id)
    {

        $agent = DB::table('users')->where('id', Auth::id())->first();

        if (empty($request->form_id)) {
            return redirect()->back()->with('error', 'กรุณาเลือกฟอร์มที่ต้องการใช้');
        }

        $record_id = 'REC-' . Str::upper(Str::random(10));


        DB::table('chk_records')->insert([
            'user_id' => Auth::id(),
            'veh_id' => $id,
            'record_id' => $record_id,
            'form_id' => $request->form_id,
            'agency_id' => $agent->agency_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $firstCategory = DB::table('check_categories')
            ->where('form_id', $request->form_id)
            ->orderBy('cates_no')
            ->first();

        return redirect()->route('user.chk_step2', ['rec' => $record_id, 'cats' => $firstCategory->category_id]);
    }

    public function chk_step2($rec, $cats)
    {
        $forms = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name')
            ->where('check_categories.category_id', '=', $cats)
            ->first();

        $record = DB::table('chk_records')->where('record_id', $rec)->first();
        $category = DB::table('check_categories')->where('category_id', $cats)->first();
        $items = DB::table('check_items')
            ->where('category_id', $category->category_id)->get();

        return view('pages.user.ChkStep2', compact('record', 'category', 'items', 'forms'));
    }

    public function chk_insert_step2(Request $request, $record_id, $category_id)
    {

        foreach ($request->item_result as $item_id => $value) {
            DB::table('check_records_result')->insert([
                'user_id' => Auth::id(),
                'record_id' => $record_id,
                'item_id' => $item_id,
                'result_value' => $value,
                'user_comment' => $request->user_comment[$item_id] ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if ($request->hasFile("item_images.$item_id")) {
                foreach ($request->file("item_images.$item_id") as $image) {
                    $imagePath = 'upload/';
                    $newname = $item_id . '_' . $image->getClientOriginalName();
                    $image->move($imagePath, $newname);
                    $fileName = $imagePath . $newname;

                    DB::table('check_result_images')->insert([
                        'record_id' => $record_id,
                        'item_id' => $item_id,
                        'image_path' => $fileName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }

        //หมวดถัดไป
        $current = DB::table('check_categories')->where('category_id', $category_id)->first();
        $next = DB::table('check_categories')
            ->where('form_id', $current->form_id)
            ->where('cates_no', '>', $current->cates_no)
            ->orderBy('cates_no')
            ->first();

        if ($next) {
            return redirect()->route('user.chk_step2', ['rec' => $record_id, 'cats' => $next->category_id]);
        }
        return redirect()->route('user.chk_result', ['record_id' => $record_id])->with('success', 'บันทึกสำเร็จ');
    }

    public function chk_result($record_id)
    {
        $record = DB::table('chk_records')
            ->join('vehicles', 'chk_records.veh_id', '=', 'vehicles.veh_id')
            ->join('vehicle_types', 'vehicles.veh_type', '=', 'vehicle_types.id')
            ->select('vehicles.*', 'vehicle_types.vehicle_type as veh_type_name', 'chk_records.created_at as date_check', 'chk_records.form_id', 'chk_records.record_id', 'chk_records.user_id as chk_user', 'chk_records.agency_id as chk_agent')
            ->where('chk_records.record_id', $record_id)->first();

        $agent_name = DB::table('users')
            ->where('id', $record->chk_agent)
            ->first();

        $forms = DB::table('forms')
            ->select('forms.form_name')
            ->where('form_id', '=', $record->form_id)
            ->first();

        $categories = DB::table('check_categories')
            ->where('form_id', $record->form_id)
            ->orderBy('cates_no')
            ->get();

        $results = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
            ->where('record_id', $record_id)
            ->select('check_items.category_id', 'check_items.item_name', 'check_records_result.item_id', 'result_value', 'user_comment')
            ->get()
            ->groupBy('category_id');

        $images = DB::table('check_result_images')
            ->where('record_id', $record_id)
            ->get()
            ->groupBy('item_id');

            $item_chk = DB::table('check_records_result')
            ->select('record_id', 'item_id', DB::raw('COUNT(result_value) as count'))
            ->where('record_id', $record_id)
            ->whereIn('result_value', [0, 2])
            ->groupBy('record_id', 'item_id')
            ->get();

        return view('pages.user.ChkResult', compact('agent_name', 'record', 'results', 'forms', 'categories', 'images','item_chk'));
    }
}
