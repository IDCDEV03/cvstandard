 @php
     use App\Enums\Role;
     $role = Auth::user()->role;
     
 @endphp
 <div class="sidebar__menu-group">
     <ul class="sidebar_nav">

         <li class="menu-title mt-30">
             @if ($role === Role::Agency)
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <div class="border-top my-3"></div>
                   <span>เมนูสำหรับหน่วยงาน</span>
             @elseif ($role === Role::User)
             @php               
                $agent_id = Auth::user()->agency_id;
                $agent = DB::table('users')
                ->select('name')
                ->where('id','=',$agent_id)
                ->first();             
             @endphp
                 <span>ระบบตรวจมาตรฐานรถ</span>
                 <span><i class="far fa-building"></i> {{$agent->name}} </span>
                 <div class="border-top my-3"></div>
                 <span><i class="fas fa-bars"></i> เมนูสำหรับผู้ใช้งาน</span>
             @endif
         </li>

   
         @if ($role === Role::Agency)
            <li>
             <a href="{{route('agency.index')}}" class="">
                 <span class="nav-icon uil uil-create-dashboard"></span>
                 <span class="menu-text">หน้าหลัก</span>
                
             </a>
           
         </li>

          <li>
                 <a href="{{ route('agency.main') }}" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>
                     <span class="badge badge-success menuItem rounded-circle">3</span>
                 </a>
             </li>


          <li>
             <a href="{{route('agency.manager_list')}}" class="">
                 <span class="nav-icon uil uil-users-alt"></span>
                 <span class="menu-text">รายชื่อหัวหน้า</span>                
             </a>           
         </li>

          <li>
             <a href="{{route('agency.user_list')}}" class="">
                 <span class="nav-icon uil uil-users-alt"></span>
                 <span class="menu-text">รายชื่อเจ้าหน้าที่</span>                
             </a>           
         </li>

           <li>
             <a href="{{route('agency.veh_list',['id'=>Auth::id()])}}" class="">
                 <span class="nav-icon uil uil-truck"></span>
                 <span class="menu-text">รายการทะเบียนรถ</span>                
             </a>           
         </li>



            
                 <li class="has-child">
             <a href="#" class="">
                 <span class="nav-icon far fa-list-alt"></span>
                 <span class="menu-text">แบบฟอร์ม</span>
                 <span class="toggle-icon"></span>
             </a>
             <ul>
                 <li>
                    <a href="{{route('agency.form_list')}}">รายการฟอร์ม</a>
                 </li>
                 <li>
                    <a href="{{route('agency.create_form')}}">สร้างฟอร์ม</a>
                 </li>

             </ul>
         </li>
         @elseif ($role === Role::User)
               <li>
             <a href="{{route('local.home')}}" class="">
                 <span class="nav-icon uil uil-create-dashboard"></span>
                 <span class="menu-text">หน้าหลัก</span>
               
             </a>
               </li>
        

             <li>
                 <a href="{{route('user.announce')}}" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>
                  
                 </a>
             </li>
                <li>
                 <a href="{{route('user.chk_list')}}" class="">
                     <span class="nav-icon uil uil-file-copy-alt"></span>
                     <span class="menu-text">รายการตรวจรถ</span> 
                      <span class="toggle-icon"></span>                  
                 </a>                 

               <li>
                 <a href="{{route('coming_soon')}}" class="">
                     <span class="nav-icon uil-file-edit-alt"></span>
                     <span class="menu-text">ประวัติแจ้งซ่อม</span>
                 </a>
             </li>

               <li>
                 <a href="{{route('coming_soon')}}" class="">
                     <span class="nav-icon uil uil-file-plus-alt"></span>
                     <span class="menu-text">ประวัติการขอเบิก</span>
                 </a>
             </li>

                 <li>
                 <a href="{{route('user.doc_list')}}" class="">
                     <span class="nav-icon uil-file-edit-alt"></span>
                     <span class="menu-text">บันทึกข้อความ</span>
                 </a>
             </li>


             </li>
                <li>
                 <a href="{{route('user.profile')}}" class="">
                     <span class="nav-icon uil uil-user"></span>
                     <span class="menu-text">บัญชีผู้ใช้</span>                   
                 </a>
             </li>
        @elseif ($role === Role::Manager)
             <li>
                 <a href="#" class="">
                     <span class="nav-icon uil uil-megaphone"></span>
                     <span class="menu-text">ประกาศ</span>
                     <span class="badge badge-info menuItem rounded-circle">2</span>
                 </a>
             </li>
         @endif



     </ul>
 </div>
