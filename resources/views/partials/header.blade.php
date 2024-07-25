<header class="main-header">
  <!-- Logo -->
  <a href="{{ route('dashboard')}}" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini">
      <img src="{{ asset('images/logo-small.jpg')}}" width="52">
    </span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>NGUYỄN HIỀN</b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  @if(!isset($codeUser))
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

     <div class="input-group input-group-sm " style="width: 150px;position: absolute;left: 45px; top: 10px;" id="div_search_fast">
  
          <input type="text" class="form-control" id="keyword" name="keyword" placeholder="ID/Điện thoại" value="{{ isset($keyword) ? $keyword : "" }}">
          <span class="input-group-btn">
              <button type="button" id="btnQuickSearch" class="btn btn-danger btn-flat btn-preview">Tìm</button>
          </span>
      </div>
   
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">                

        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="setting_top_1">            
            <i class="fa fa-gears" id="setting_top_2"></i><span class="hidden-xs">Chào {{ Auth::user()->name }}</span>
          </a>
          <ul class="dropdown-menu">            
            <li class="user-footer">
            <div class="pull-left">
                <a href="{{ route('account.change-pass') }}" class="btn btn-success btn-flat">Đổi mật khẩu</a>
              </div>             
              <div class="pull-right">

                <a href="{{ route('logout') }}" class="btn btn-danger btn-flat" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Thoát</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </div>
            </li>

          </ul>
        </li>               

      </ul>
    </div>
  </nav>
  @else
  <div style="clear:both;"></div>
   @endif

</header>