<div class="sidebar__menu-group">
    <ul class="sidebar_nav">

        <li class="has-child {{ Request::is(app()->getLocale().'/dashboards/*') ? 'open':'' }}">
            <a href="#" class="{{ Request::is(app()->getLocale().'/dashboards/*') ? 'active':'' }}">
                <span class="nav-icon uil uil-create-dashboard"></span>
                <span class="menu-text">หน้าหลัก</span>
                <span class="toggle-icon"></span>
            </a>
            <ul>
                <li class="{{ Request::is(app()->getLocale().'/dashboard') ? 'active':'' }}"><a href="{{ route('admin.dashboard',app()->getLocale()) }}">#</a></li>
               
            </ul>
        </li>

          <li>
                     <a href="{{route('admin.announce')}}" class="">
                        <span class="nav-icon uil uil-megaphone"></span>
                        <span class="menu-text">ประกาศ</span>
                        <span class="badge badge-success menuItem rounded-circle">3</span>
                     </a>
                  </li>

                  <li>
                     <a href="{{route('admin.agency_list')}}" class="">
                        <span class="nav-icon uil uil-building"></span>
                        <span class="menu-text">หน่วยงาน</span>
                       
                     </a>
                  </li>


    </ul>
</div>