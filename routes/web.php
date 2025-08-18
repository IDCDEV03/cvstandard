<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\AgencyMainController;
use App\Http\Controllers\User\UserMainController;
use App\Http\Controllers\User\RepairController;
use App\Http\Controllers\User\VehiclesController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Admin\ManageCompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\User\ManageAccountController;
use Database\Seeders\VehicleTypeSeeder;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\User\ManagerController;
use App\Http\Controllers\StaffController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RedirectController::class, 'handleRoot'])->name('root');
Route::get('/comingsoon', [PageController::class, 'coming_soon'])->name('coming_soon');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    //CRUD บริษัทฯว่าจ้าง
    Route::get('/cp_list', [AdminDashboardController::class, 'CompanyList'])->name('admin.cp_list');
    Route::get('/cp_new', [AdminDashboardController::class, 'CompanyCreate'])->name('admin.cp_create');
    Route::get('/cp_edit/{id}', [AdminDashboardController::class, 'CompanyEdit'])->name('admin.cp_edit');

    Route::POST('/cp_update/{id}/{tab}', [ManageCompanyController::class, 'CompanyUpdate'])->name('admin.cp_update');

    Route::get('/cp_status/{id}/{status}', [ManageCompanyController::class, 'UpdateStatus'])->name('admin.cp_updatestatus');

    //CRUD Supply
    Route::get('/sup_list/{id}', [ManageCompanyController::class, 'SupList'])->name('admin.sup_list');
     Route::get('/sup-create/{id}', [ManageCompanyController::class, 'SupCreate'])->name('admin.sup_create');
      Route::post('/supply/insert', [ManageCompanyController::class, 'SupInsert'])->name('admin.sup_insert');

    // CRUD หน่วยงาน
    Route::get('/agencies/create', [ManageUserController::class, 'createAgency'])->name('admin.agency.create');
    Route::post('/agencies/insert', [ManageUserController::class, 'insert_agency'])->name('admin.agency.insert');
    Route::get('/agency', [ManageUserController::class, 'Agency_list'])->name('admin.agency_list');

    Route::get('/agencies/{id}/edit', [ManageUserController::class, 'EditAgency'])->name('admin.agency.edit');
    Route::put('/agencies/{id}', [ManageUserController::class, 'UpdateAgency'])->name('admin.agency.update');
    Route::GET('/agencies/{id}', [ManageUserController::class, 'DestroyAgency'])->name('admin.agency.destroy');
    Route::get('/show-agencies/{id}', [ManageUserController::class, 'AgencyDetail'])->name('admin.agency.show');

    //CRUD User-Manager
    Route::get('/members/create/{role}/{id}', [ManageUserController::class, 'createMember'])->name('admin.member.create');
    Route::post('/members/insert', [ManageUserController::class, 'insertMember'])->name('admin.member.insert');
    Route::get('/members/{id}/edit', [ManageUserController::class, 'editMember'])->name('admin.member.edit');
    Route::put('/members/{id}', [ManageUserController::class, 'updateMember'])->name('admin.member.update');
    Route::delete('/members/{id}', [ManageUserController::class, 'destroyMember'])->name('admin.member.destroy');


    //module ประกาศ
    Route::get('/announcement', [AdminDashboardController::class, 'AnnouncementPage'])->name('admin.announce');
    Route::get('/create_post', [AdminDashboardController::class, 'create_announce'])->name('admin.create_post');
    Route::post('/insert_post', [AdminDashboardController::class, 'insert_post'])->name('admin.insert_post');
    //edit-update-delete_post
    Route::get('/announce/{id}/edit', [AdminDashboardController::class, 'edit_post'])->name('admin.edit_post');
    Route::post('/announce-update/{id}', [AdminDashboardController::class, 'update_post'])->name('admin.update_post');
    Route::get('/announce-delete/{id}/file', [AdminDashboardController::class, 'delete_file'])->name('admin.delete_file');
    Route::get('/announce-delete/{id}/post', [AdminDashboardController::class, 'delete_post'])->name('admin.delete_post');
});

Route::prefix('vehicles')->middleware(['auth', 'role:user,manager,admin,agency'])->group(function () {
    Route::get('/page/{id}', [VehiclesController::class, 'veh_detail'])->name('veh.detail');
    Route::get('/result/{rec}', [VehiclesController::class, 'Report_Result'])->name('veh.result');
    Route::get('/repair-notice', [VehiclesController::class, 'repair_notice'])->name('veh.notice');
});

Route::prefix('user')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', [PageController::class, 'home'])->name('local.home');
    Route::get('/announce', [UserMainController::class, 'announce'])->name('user.announce');
    Route::get('/check/all', [UserMainController::class, 'chk_list'])->name('user.chk_list');
    Route::get('/profile', [UserMainController::class, 'profile'])->name('user.profile');

    //ลงทะเบียนรถ
    Route::get('/veh-regis', [UserMainController::class, 'veh_regis'])->name('user.veh_regis');
    Route::POST('/veh-create', [UserMainController::class, 'veh_insert'])->name('user.veh_create');

    //เริ่มตรวจ
    Route::get('/check/start/{id}', [UserMainController::class, 'start_check'])->name('user.chk_start');

    Route::POST('/chk/insert/step1/{id}', [UserMainController::class, 'insert_step1'])->name('user.insert1');

    Route::get('/check/step2/{rec}/{cats}', [UserMainController::class, 'chk_step2'])->name('user.chk_step2');
    Route::POST('/check-store/step2/{record_id}/{category_id}', [UserMainController::class, 'chk_insert_step2'])->name('user.chk_insert_step2');

    Route::get('/check/result/{record_id}', [UserMainController::class, 'chk_result'])->name('user.chk_result');

    //แก้ไขภาพ
    Route::get('/images/edit/{record_id}/{id}', [VehiclesController::class, 'edit_images'])->name('user.edit_images');
    Route::post('/images/update', [VehiclesController::class, 'update_image'])->name('update_image');
    Route::get('/image/delete/{id}', [VehiclesController::class, 'delete_image'])->name('delete_image');

    //แจ้งซ่อม
    Route::get('/create-repair/{record_id}', [RepairController::class, 'create_repair'])->name('user.create_repair');

    //บันทึกข้อความ
    Route::get('/veh-doc', [DocumentController::class, 'doc_list'])->name('user.doc_list');
});

Route::prefix('agency')->middleware(['auth', 'role:agency'])->group(function () {
    Route::get('/index', [PageController::class, 'home'])->name('agency.index');
    Route::get('/main', [AgencyMainController::class, 'main_page'])->name('agency.main');

    //เพิ่มรถ
    Route::get('/veh-regis', [VehiclesController::class, 'veh_regis'])->name('agency.veh_regis');
    Route::get('/veh-list/{id}', [VehiclesController::class, 'veh_list'])->name('agency.veh_list');
    Route::POST('/veh-create', [VehiclesController::class, 'veh_insert'])->name('agency.veh_create');

    //สร้างuserหัวหน้า-เจ้าหน้าที่
    Route::get('/managerlist', [ManageAccountController::class, 'ManagerList'])->name('agency.manager_list');
    Route::get('/userlist', [ManageAccountController::class, 'UserList'])->name('agency.user_list');
    Route::post('/check-username', [ManageAccountController::class, 'checkUsername'])->name('check.username');


    Route::get('/create-account/{role}', [ManageAccountController::class, 'createAccount'])->name('agency.create_account');
    Route::post('/insert_account', [ManageAccountController::class, 'InsertAccount'])->name('agency.insert_account');

    //ฟอร์ม
    Route::get('/form', [AgencyMainController::class, 'form_list'])->name('agency.form_list');
    Route::get('/create-form', [AgencyMainController::class, 'form_create'])->name('agency.create_form');
    Route::post('/insert_form', [AgencyMainController::class, 'form_insert'])->name('agency.insert_form');

    //หมวดหมู่
    Route::get('/chk-categories/{form_id}', [AgencyMainController::class, 'cates_list'])->name('agency.cates_list');
    Route::get('/chk-cates-create/{id}', [AgencyMainController::class, 'create_cates'])->name('agency.create_cates');
    Route::post('/insert_cates/{id}', [AgencyMainController::class, 'insert_cates'])->name('agency.insert_cates');
    Route::get('/categories/{cates_id}', [AgencyMainController::class, 'cates_detail'])->name('agency.cates_detail');

    //ข้อตรวจ
    Route::get('/item-new/{id}', [AgencyMainController::class, 'item_create'])->name('agency.item_create');
    Route::post('/insert-item', [AgencyMainController::class, 'item_insert'])->name('agency.item_insert');
    Route::get('/item-edit/{id}', [AgencyMainController::class, 'item_edit'])->name('agency.item_edit');
    Route::post('/item-update', [AgencyMainController::class, 'item_update'])->name('agency.item_update');
    Route::get('/item-delete/{id}/image', [AgencyMainController::class, 'item_delete_image'])->name('agency.item_delete_image');
});

Route::prefix('manager')->middleware(['auth', 'role:manager'])->group(function () {
    // สำหรับผจก.BU
    Route::get('/index', [PageController::class, 'home'])->name('manager.index');

    //ทะเบียนบริษัท
    Route::get('/company-regis', [ManagerController::class, 'company_register'])->name('manager.company_regis');
    Route::get('/company-list/{id}', [ManagerController::class, 'company_list'])->name('manager.company_list');
    Route::POST('/company-insert', [ManagerController::class, 'company_insert'])->name('manager.company_insert');
});


Route::prefix('company')->middleware(['auth', 'role:company'])->group(function () {
    // สำหรับบริษัทว่าจ้างฯ
});

Route::prefix('sup')->middleware(['auth', 'role:supply'])->group(function () {
    // Route สำหรับ supply
});

Route::prefix('staff')->middleware(['auth', 'role:staff'])->group(function () {
    // Route สำหรับ staff
     Route::get('/index', [PageController::class, 'home'])->name('staff.index');

       //ลงทะเบียนรถ
    Route::get('/veh-list', [StaffController::class, 'VehiclesList'])->name('staff.veh_list');
    Route::get('/veh-regis', [StaffController::class, 'VehiclesRegister'])->name('staff.veh_regis');
    Route::POST('/veh-create', [StaffController::class, 'VehiclesInsert'])->name('staff.veh_create');
    Route::get('/get-supply', [StaffController::class, 'getSupplyByCompany'])->name('get.supply');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/register', [LoginController::class, 'showregisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register_store'])->name('register.store');
    Route::post('/check_username', [LoginController::class, 'check_username'])->name('username.check');
});


Route::get('/check-username', function () {
    $username = request('company_user');
    $exists = \App\Models\User::where('username', $username)->exists();
    return response()->json(['exists' => $exists]);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Route::post('/logout',[AuthController::class,'logout'])->name('logout')->middleware('auth');
//Route::get('/lang/{lang}',[ LanguageController::class,'switchLang'])->name('switch_lang');
//Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');
