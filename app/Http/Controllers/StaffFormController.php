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
use Illuminate\Support\Facades\File;

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
                'form_status'   => '1',
                'form_open'     => 'public',
                'created_at'    =>  Carbon::now(),
                'updated_at'    =>  Carbon::now()
            ]);

            // loop เก็บความสัมพันธ์กับหลายหน่วยงาน
            foreach ($request->supply_ids as $supId) {
                DB::table('form_permission')->insert([
                    'form_id' => $form_id,
                    'sup_id' => $supId,
                    'permission_status' => '1',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return redirect()->route('staff.form_new2', ['id' => $form_id,'order'=>'1'])->with('success', 'บันทึกข้อมูลสำเร็จ');
    }

    public function FormNew_Step2($id,$order)
    {
         $chk_cates = DB::table('forms')
            ->where('form_id', '=', $id)
            ->first();       

        return view('pages.staff.FormCreate2', ['id' => $id,'order'=>$order], compact('chk_cates'));
    }

      public function FormStep2_Insert(Request $request, $id)
    {
        $uid = Auth::id();

        $user_id = DB::table('users')
        ->select('users.user_id')
        ->where('id','=',$uid)
        ->first();

        foreach ($request->chk_cats_name as $index => $name) {
            $cats_id = Str::upper(Str::random(8));
            $list = $index + 1;
            DB::table('check_categories')->insert([
                'user_id' => $user_id->user_id,
                'form_id' => $id,
                'cates_no' => $request->order_no[$index] ?? ($index + 1),
                'category_id' => 'CAT-' . $list . '-' . $cats_id,
                'chk_cats_name' => $name,
                'chk_detail' => $request->chk_detail[$index] ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return redirect()->route('staff.form_step3', ['id' => $id])->with('success', 'บันทึกหมวดหมู่เรียบร้อยแล้ว');
    }

     public function FormNew_Step3($id)
    {
        
        $data = DB::table('check_categories')
            ->where('form_id', '=', $id)
            ->orderBy('cates_no', 'ASC')
            ->get();

        $form_name = DB::table('forms')
            ->where('form_id', '=', $id)
            ->first();

        return view('pages.staff.FormCreate3', ['id' => $id], compact('data','form_name'));
    }

     public function categories_detail($cates_id)
    {
        $cates_data = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'forms.form_id', 'check_categories.category_id', 'check_categories.chk_cats_name')
            ->where('check_categories.category_id', '=', $cates_id)
            ->first();

        $item_data = DB::table('check_items')
            ->where('category_id', '=', $cates_id)
            ->get();

        return view('pages.staff.Categories_Detail', ['cates_id' => $cates_id], compact('cates_data', 'item_data'));
    }

     public function item_create($id)
    {
        $cates_data = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'check_categories.category_id', 'check_categories.chk_cats_name')
            ->where('check_categories.category_id', '=', $id)
            ->first();

        return view('pages.staff.ItemCreate', ['id' => $id], compact('cates_data'));
    }

       public function item_insert(Request $request)
    {
        $uid = Auth::id();

        $user_id = DB::table('users')
        ->select('users.user_id')
        ->where('id','=',$uid)
        ->first();

        foreach ($request->item_name as $index => $name) {
            $fileName = null;

            $list = $index + 1;
            $item_id = 'CH_' . $list . '_' . Str::upper(Str::random(8));

            if ($request->hasFile('item_image.' . $index)) {
                $imagePath = 'upload/';
                $file = $request->file('item_image.' . $index);
                $extension = $file->getClientOriginalExtension();
                $newName = 'item_' . $item_id . '_' . '.' . $extension;
                $file->move($imagePath, $newName);
                $fileName = $imagePath . $newName;
            }


            DB::table('check_items')->insert([
                'user_id' => $user_id->user_id,
                'category_id' => $request->cate_id,
                'item_id' => $item_id,
                'item_no' => $index + 1,
                'item_name' => $name,
                'item_description' => $request->item_description[$index] ?? null,
                'item_type' => $request->item_type[$index],
                'item_image' => $fileName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        return redirect()->route('staff.categories_detail', ['cates_id' => $request->cate_id])->with('success', 'บันทึกข้อตรวจเรียบร้อยแล้ว');
    }

     public function item_edit($id)
    {

        $item_data = DB::table('check_items')
            ->join('check_categories', 'check_items.category_id', '=', 'check_categories.category_id')
            ->select('check_categories.chk_cats_name', 'check_items.item_no', 'check_items.item_name', 'item_description', 'check_items.item_type', 'check_items.item_image')
            ->where('check_items.item_id', '=', $id)
            ->first();

        return view('pages.staff.ItemEdit', compact('item_data'));
    }

    
    public function item_delete_image($id)
    {
        $post = DB::table('check_items')->where('item_id', $id)->first();
        if (!$post || !$post->item_image) {
            return redirect()->back();
        }
        $file = public_path('upload/' . $post->item_image);

        if (File::exists($file)) {
            File::delete($file);
        }

        DB::table('check_items')->where('item_id', $id)->update(['item_image' => null]);

        return redirect()->back()->with('success', 'ลบไฟล์เรียบร้อยแล้ว');
    }

      public function item_update(Request $request)
    {

        $id = $request->item_id;

        $item = DB::table('check_items')->where('item_id', $id)->first();
        if (!$item) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลข้อตรวจ');
        }

        $imagePath = $item->item_image;
        $item_random_id = Str::upper(Str::random(8));
        $fileName = null;

        if ($request->hasFile('item_image')) {

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $imagePath_1 = 'upload/';
            $file = $request->file('item_image');

            $extension = $file->getClientOriginalExtension();
            $newName = 'item_' . $item->item_no . '_' . $item_random_id . '.' . $extension;
            $file->move($imagePath_1, $newName);
            $fileName = $imagePath_1 . $newName;
        }

        DB::table('check_items')->where('item_id', $id)->update([
            'item_name' => $request->item_name,
            'item_description' => $request->item_description,
            'item_type' => $request->item_type,
            'item_image' => $fileName ?? null,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('staff.categories_detail', ['cates_id' => $item->category_id])->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }
}
