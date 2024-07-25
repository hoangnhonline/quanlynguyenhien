<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{ asset('admin/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{ Auth::user()->display_name }}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">      
    
      
      
      <li class="treeview {{ (in_array($routeName, ['booking-car.index', 'booking-car.create', 'booking-car.edit']) || $routeName == 'report.car') ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-cab"></i> 
          <span>ĐẶT XE</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        
        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-car.index', 'booking-car.edit']) ? "class=active" : "" }}><a href="{{ route('booking-car.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking.create']) && isset($type) && $type == 4 ? "class=active" : "" }}><a href="{{ route('booking-car.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li>
        </ul>        
      </li> 
      <li class="treeview {{ (in_array($routeName, ['booking-tu-lai.index', 'booking-tu-lai.create', 'booking-tu-lai.edit'])) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-car"></i> 
          <span>XE TỰ LÁI</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        
        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-tu-lai.index', 'booking-tu-lai.edit']) ? "class=active" : "" }}><a href="{{ route('booking-tu-lai.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking-tu-lai.create']) ? "class=active" : "" }}><a href="{{ route('booking-tu-lai.create') }}"><i class="fa fa-circle-o"></i> Tạo booking</a></li>
        </ul>        
      </li>
      <li class="treeview {{ (in_array($routeName, ['booking-xe-may.index', 'booking-xe-may.create', 'booking-xe-may.edit'])) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-bicycle"></i> 
          <span>THUÊ XE MÁY</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        
        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-xe-may.index', 'booking-xe-may.edit']) ? "class=active" : "" }}><a href="{{ route('booking-xe-may.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking-xe-may.create']) ? "class=active" : "" }}><a href="{{ route('booking-xe-may.create') }}"><i class="fa fa-circle-o"></i> Tạo booking</a></li>
        </ul>        
      </li>            
      <li {{ in_array($routeName, ['booking-xe-free.index', 'booking-xe-free.edit', 'booking-xe-free.create', 'booking-xe-free.create-tx']) ? "class=active" : "" }}>
        <a href="{{ route('booking-xe-free.index') }}">
          <i class="fa fa-car" aria-hidden="true"></i>
          <span>XE MIỄN PHÍ</span>          
        </a>       
      </li> 
      <li {{ in_array($routeName, ['drivers.index', 'drivers.edit', 'drivers.create', 'drivers.create-tx']) ? "class=active" : "" }}>
        <a href="{{ route('drivers.index') }}">
          <i class="fa fa-user" aria-hidden="true"></i>
          <span>TÀI XẾ</span>          
        </a>       
      </li>  
      <li {{ in_array($routeName, ['payment-request.index', 'payment-request.edit', 'payment-request.create']) ? "class=active" : "" }}>
        <a href="{{ route('payment-request.index') }}">
          <img src="{{ asset('admin/dist/img/payment-request.png') }}" alt="Yêu cầu thanh toán" width="20px">
          <span>YÊU CẦU THANH TOÁN</span>          
        </a>       
      </li>
      <li {{ in_array($routeName, ['coupon-code.index', 'coupon-code.create', 'coupon-code.edit']) ? "class=active" : "" }}>
        <a href="{{ route('coupon-code.index') }}">
          <i class="  fa fa-gift"></i> <span>MÃ GIẢM GIÁ</span>
        </a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
<style type="text/css">
  .skin-blue .sidebar-menu>li>.treeview-menu{
    padding-left: 15px !important;
  }
</style>