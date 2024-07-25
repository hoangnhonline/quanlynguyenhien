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
      <li class="treeview {{ in_array($routeName, ['booking.index', 'booking.create', 'booking.edit']) && (isset($type) && $type == 1) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-superpowers"></i>
          <span>ĐẶT TOUR PQ</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking.index', 'booking.edit']) ? "class=active" : "" }}><a href="{{ route('booking.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking.create']) ? "class=active" : "" }}><a href="{{ route('booking.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li>

        </ul>
      </li>
      <li class="treeview {{ in_array($routeName, ['booking.index', 'booking.create', 'booking.edit']) && (isset($type) && $type == 1) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-snowflake-o" aria-hidden="true"></i>
          <span>ĐẶT TOUR ĐN</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-dn.index', 'booking-dn.edit']) ? "class=active" : "" }}><a href="{{ route('booking-dn.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking-dn.create']) ? "class=active" : "" }}><a href="{{ route('booking-dn.create') }}"><i class="fa fa-circle-o"></i> Tạo booking</a></li>

        </ul>
      </li>
      @if(Auth::user()->id == 333)
      <li {{ in_array($routeName, ['payment-request.index', 'payment-request.edit', 'payment-request.create']) ? "class=active" : "" }}>
        <a href="{{ route('payment-request.index') }}">
          <img src="{{ asset('admin/dist/img/payment-request.png') }}" alt="Yêu cầu thanh toán" width="20px">
          <span>YÊU CẦU THANH TOÁN</span>
        </a>
      </li>
      @endif
      @if(Auth::user()->is_limit == 0)
      <li class="treeview {{ in_array($routeName, ['booking-hotel.index', 'booking-hotel.create', 'booking-hotel.edit']) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-building-o"></i>
          <span>ĐẶT KHÁCH SẠN</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-hotel.index', 'booking-hotel.edit']) ? "class=active" : "" }}><a href="{{ route('booking-hotel.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking-hotel.create']) ? "class=active" : "" }}>
            <a href="{{ route('booking-hotel.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a>
          </li>
          <li {{ in_array($routeName, ['hotel.index', 'hotel.edit', 'hotel.create']) ? "class=active" : "" }}><a href="{{ route('hotel.index') }}"><i class="fa fa-circle-o"></i> Khách sạn</a></li>
        </ul>
      </li>
      <li class="treeview {{ (in_array($routeName, ['booking-car.index', 'booking-car.create', 'booking-car.edit']) || $routeName == 'report.car') ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-cab"></i>
          <span>ĐẶT XE</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">

          <li {{ in_array($routeName, ['booking-car.index', 'booking-car.edit']) ? "class=active" : "" }}><a href="{{ route('booking-car.index') }}"><i class="fa fa-circle-o"></i> Xe có tài</a></li>
          <li {{ in_array($routeName, ['booking.create']) && isset($type) && $type == 4 ? "class=active" : "" }}><a href="{{ route('booking-car.create') }}"><i class="fa fa-circle-o"></i> Tạo booking</a></li>
          <li {{ in_array($routeName, ['booking-tu-lai.index', 'booking-tu-lai.edit', 'booking-tu-lai.create']) ? "class=active" : "" }}><a href="{{ route('booking-tu-lai.index') }}"><i class="fa fa-circle-o"></i> Xe tự lái</a></li>
          <li {{ in_array($routeName, ['booking-xe-may.index', 'booking-xe-may.edit', 'booking-xe-may.create']) ? "class=active" : "" }}><a href="{{ route('booking-xe-may.index') }}"><i class="fa fa-circle-o"></i> Xe máy</a></li>

        </ul>
      </li>
      <li class="treeview {{ in_array($routeName, ['booking.index', 'booking.create', 'booking.edit']) && (isset($type) && $type == 3) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-ticket"></i>
          <span>ĐẶT VÉ THAM QUAN</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['booking-ticket.index', 'booking-ticket.edit']) ? "class=active" : "" }}><a href="{{ route('booking-ticket.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
          <li {{ in_array($routeName, ['booking.create']) ? "class=active" : "" }}><a href="{{ route('booking-ticket.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li>
        </ul>
      </li>


      <li {{ in_array($routeName, ['customer.index', 'customer.create', 'customer.edit']) ? "class=active" : "" }}>
        <a href="{{ route('customer.index') }}">
          <img src="{{ asset('admin/dist/img/cskh.png') }}" alt="Chăm sóc khách hàng" width="20px">
          <span>Khách hàng</span>
        </a>
      </li>
      @if(Auth::user()->role == 1 && !Auth::user()->view_only)
      <li {{ in_array($routeName, ['cost.index', 'cost.create', 'cost.edit']) ? "class=active" : "" }}>
        <a href="{{ route('cost.index') }}">
          <i class="glyphicon glyphicon-usd"></i> <span>CHI PHÍ</span>
        </a>
      </li>
      <li {{ in_array($routeName, ['revenue.index', 'revenue.create', 'revenue.edit']) ? "class=active" : "" }}>
        <a href="{{ route('revenue.index') }}">
          <i class="glyphicon glyphicon-gift"></i> <span>KHOẢN THU KHÁC</span>
        </a>
      </li>
      <li {{ in_array($routeName, ['debt.index', 'debt.create', 'debt.edit']) ? "class=active" : "" }}>
        <a href="{{ route('debt.index') }}">
          <i class="  glyphicon glyphicon-remove-sign"></i> <span>CÔNG NỢ</span>
        </a>
      </li>
      @endif
        @if(Auth::user()->role == 1 && !Auth::user()->view_only && Auth::user()->id != 549)

      <li class="treeview {{ in_array($routeName, ['hotel.index', 'hotel.create', 'hotel.edit', 'partner.index', 'partner.create', 'partner.edit', 'drivers.index', 'drivers.create', 'drivers.edit']) ? 'active' : '' }}"" >
        <a href="#">
          <i class="fa fa-cogs"></i>
          <span>HỆ THỐNG</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li {{ in_array($routeName, ['account.index', 'account.edit', 'account.create'])? "class=active" : "" }}><a href="{{ route('account.index') }}"><i class="fa fa-circle-o"></i> Tài khoản</a></li>
          <li {{ in_array($routeName, ['ticket-type-system.index', 'ticket-type-system.edit', 'ticket-type-system.create'])? "class=active" : "" }}><a href="{{ route('ticket-type-system.index') }}"><i class="fa fa-circle-o"></i> Giá vé</a></li>
          <li {{ in_array($routeName, ['partner.index', 'partner.edit', 'partner.create'])? "class=active" : "" }}><a href="{{ route('partner.index') }}"><i class="fa fa-circle-o"></i> Đại lí phòng/chi phí</a></li>
          <li {{ in_array($routeName, ['drivers.index', 'drivers.edit', 'drivers.create'])? "class=active" : "" }}><a href="{{ route('drivers.index') }}"><i class="fa fa-circle-o"></i> Tài xế</a></li>
          <li {{ in_array($routeName, ['location.index', 'location.edit', 'location.create'])? "class=active" : "" }}><a href="{{ route('location.index') }}"><i class="fa fa-circle-o"></i> Điểm đón</a></li>
        </ul>
      </li>
      @endif

      @endif
       @if(Auth::user()->id == 333)
      <li {{ in_array($routeName, ['report.ben']) ? "class=active" : "" }}>
        <a href="{{ route('report.ben') }}">
          <i class=" fa-bar-chart fa"></i> <span>THỐNG KÊ</span>
        </a>
      </li>
      <li {{ in_array($routeName, ['account.index', 'account.edit', 'account.create', 'account.create-tx']) ? "class=active" : "" }}>
        <a href="{{ route('account.index') }}">
          <i class="fa fa-user" aria-hidden="true"></i>
          <span>ĐỐI TÁC</span>
        </a>
      </li>
      @endif
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
<style type="text/css">
  .skin-blue .sidebar-menu>li>.treeview-menu{
    padding-left: 15px !important;
  }
</style>
