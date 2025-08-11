 @php
     use App\Enums\Role;
     use Illuminate\Support\Facades\Auth;
     use Illuminate\Support\Facades\DB;

     $role = Auth::user()->role;
     $agencyLogo = null;

     $user = Auth::user();

     if ($user->role === Role::Agency->value) {
         $agencyLogo = $user->logo_agency;
     } elseif ($user->agency_id) {
         $agency = DB::table('users')->where('id', $user->agency_id)->first();
         $agencyLogo = $agency?->logo_agency;
     }
 @endphp
 <nav class="navbar navbar-light">
     <div class="navbar-left">
         <div class="logo-area">

             @if ($agencyLogo)
                 <a class="navbar-brand" href="#">
                     <img class="dark" src="{{ asset($agencyLogo) }}" alt="logo"
                         style="height: 40px; object-fit: contain;">
                     <img class="light" src="{{ asset($agencyLogo) }}" alt="logo"
                         style="height: 40px; object-fit: contain;">
                 </a>
             @else
                 <a class="navbar-brand" href="#">
                     <img class="dark" src="{{ asset('assets/img/logo-1.png') }}"
                         style="height: 40px; object-fit: contain;" alt="img">
                     <img class="light" src="{{ asset('assets/img/logo-1.png') }}"
                         style="height: 40px; object-fit: contain;" alt="img">
                 </a>
             @endif
             <a href="#" class="sidebar-toggle">
                 <img class="svg" src="{{ asset('assets/img/svg/align-center-alt.svg') }}" alt="img"></a>
         </div>

         <div class="top-menu">
             <div class="hexadash-top-menu position-relative">
                 <ul>
                     <li>
                         <a href="#">
                             <span class="nav-icon uil uil-circle"></span>
                             <span class="menu-text">0000</span>
                         </a>
                     </li>

                     <li class="has-subMenu">
                         <a href="#">Dashboard</a>
                         <ul class="subMenu">
                             <li><a href="#">11</a></li>

                         </ul>
                     </li>
                 </ul>
             </div>
         </div>
     </div>
     <div class="navbar-right">
         <ul class="navbar-right__menu">

             <li class="nav-author">
                 <div class="dropdown-custom">
                     <a href="javascript:;" class="nav-item-toggle">
                         <img src="{{ asset('settings.png') }}" alt="" class="rounded-circle">


                         @if (Auth::check())
                             <label class="nav-item__title">{{ Auth::user()->name }} {{ Auth::user()->lastname }}<i
                                     class="las la-angle-down nav-item__arrow"></i></label>
                         @endif
                     </a>
                     <div class="dropdown-wrapper">
                         <div class="nav-author__info">
                             <div>
                                 @if (Auth::check())
                                     <span class="fs-14 fw-bold text-capitalize">{{ Auth::user()->name }}
                                         {{ Auth::user()->lastname }}</span>
                                 @endif

                                 @if ($role === Role::User)
                                     <br> <span>เจ้าหน้าที่</span>
                                 @elseif($role === Role::Agency)
                                     <br> <span>หน่วยงาน</span>
                                 @elseif($role === Role::Staff)
                                     <br> <span>ผู้ดูแล</span>
                                 @endif

                             </div>
                         </div>
                         <div class="nav-author__options">

                             @if ($role === Role::User)
                                 <ul>
                                     <li>
                                         <a href="{{ route('user.profile') }}">
                                             <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user"
                                                 class="svg"> บัญชีผู้ใช้</a>
                                     </li>
                                     <li>
                                         <a href="#">
                                             <img src="{{ asset('assets/img/svg/settings.svg') }}" alt="settings"
                                                 class="svg"> ตั้งค่า</a>
                                     </li>


                                 </ul>
                             @elseif ($role === Role::Agency)
                                 <ul>
                                     <li>
                                         <a href="">
                                             <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user"
                                                 class="svg"> Profile</a>
                                     </li>
                                     <li>
                                         <a href="">
                                             <img src="{{ asset('assets/img/svg/settings.svg') }}" alt="settings"
                                                 class="svg"> Settings</a>
                                     </li>
                                 </ul>
                                
                                 @elseif ($role === Role::Staff)
                                 <ul>
                                     <li>
                                         <a href="">
                                             <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user"
                                                 class="svg"> Profile</a>
                                     </li>
                                     <li>
                                         <a href="">
                                             <img src="{{ asset('assets/img/svg/settings.svg') }}" alt="settings"
                                                 class="svg"> Settings</a>
                                     </li>
                                 </ul>
                             @endif

                             <a href="#" class="nav-author__signout"
                                 onclick="event.preventDefault(); document.getElementById('logout').submit();">
                                 <img src="{{ asset('assets/img/svg/log-out.svg') }}" alt="log-out" class="svg">
                                 ออกจากระบบ
                             </a>

                             <form id="logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                                 @csrf
                             </form>
                         </div>
                     </div>
                 </div>
             </li>
         </ul>
         <div class="navbar-right__mobileAction d-md-none">
             <a href="#" class="btn-search">
                 <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg feather-search">
                 <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg feather-x">
             </a>
             <a href="#" class="btn-author-action">
                 <img src="{{ asset('assets/img/svg/more-vertical.svg') }}" alt="more-vertical" class="svg">
             </a>
         </div>
     </div>
 </nav>
