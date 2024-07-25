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
           
            <li class="treeview {{ in_array($routeName, ['booking.index', 'booking.create', 'booking.edit']) && (isset($type) && $type == 1) ? 'active' : '' }}">
                <a href="{{ route('booking.index') }}">
                    <i class="fa fa-superpowers"></i>
                    <span>QUẢN LÝ TOUR</span>
                    
                </a>
            </li>

                    <li {{ in_array($routeName, ['cost.index', 'cost.create', 'cost.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('cost.index') }}">
                            <i class="glyphicon glyphicon-usd"></i></i> <span>CHI PHÍ</span>
                        </a>
                    </li>
                
                @if(Auth::user()->role == 1 && Auth::user()->id != 549)
                    <li class="treeview {{ in_array($routeName, ['report.cano', 'report.cano-detail', 'report.car', 'report.doanh-thu-thang', 'report.general']) ? 'active' : '' }}"
                    " >
                    <a href="#">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        <span>THỐNG KÊ</span>
                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                    </a>

                    <ul class="treeview-menu">
                        <li {{ in_array($routeName, ['report.general'])? "class=active" : "" }}><a
                                href="{{ route('report.general') }}"><i class="fa fa-circle-o"></i> Tổng quan</a></li>
                        
                   
                        <li {{ in_array($routeName, ['report.average-guest-by-level'])? "class=active" : "" }}><a
                                href="{{ route('report.average-guest-by-level') }}"><i class="fa fa-circle-o"></i> SL
                                khách</a></li>                       
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}>
                            <a href="{{ route('report.ve-cap-treo') }}"><i class="fa fa-circle-o"></i> Vé cáp treo</a>
                        </li>
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}>
                            <a href="{{ route('report.phan-an') }}"><i class="fa fa-circle-o"></i> Phần ăn</a>
                        </li>
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}><a
                                href="{{ route('report.cano') }}"><i class="fa fa-circle-o"></i> Cano</a></li>
                        
                    </ul>
                    </li>
                

                   
                        <li class="treeview {{ in_array($routeName, ['hotel.index', 'hotel.create', 'hotel.edit', 'partner.index', 'partner.create', 'partner.edit', 'drivers.index', 'drivers.create', 'drivers.edit']) ? 'active' : '' }}"
                        " >
                        <a href="#">
                            <i class="fa fa-cogs"></i>
                            <span>HỆ THỐNG</span>
                            <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                        </a>

                        <ul class="treeview-menu">
                            <li {{ in_array($routeName, ['bank-info.index', 'bank-info.edit', 'bank-info.create'])? "class=active" : "" }}>
                                <a href="{{ route('bank-info.index') }}"><i class="fa fa-circle-o"></i> TK ngân hàng</a></li>
                            <li {{ in_array($routeName, ['staff.index', 'staff.edit', 'staff.create'])? "class=active" : "" }}>
                                <a href="{{ route('staff.index') }}"><i class="fa fa-circle-o"></i> Nhân viên</a></li>
                            <li {{ in_array($routeName, ['account.index', 'account.edit', 'account.create'])? "class=active" : "" }}>
                                <a href="{{ route('account.index') }}"><i class="fa fa-circle-o"></i> Tài khoản</a></li>
                           
                          
                            <li {{ in_array($routeName, ['location.index', 'location.edit', 'location.create'])? "class=active" : "" }}>
                                <a href="{{ route('location.index') }}"><i class="fa fa-circle-o"></i> Điểm đón</a></li>
                            
                           
                            <li {{ in_array($routeName, ['cano.index', 'cano.create', 'cano.edit', 'steersman.index']) ? "class=active" : "" }}>
                                <a
                                    href="{{ route('cano.index') }}"><i class="fa fa-circle-o"></i>Cano</a></li>
                           

                        </ul>
                        </li>
                  
      

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
    .skin-blue .sidebar-menu > li > .treeview-menu {
        padding-left: 15px !important;
    }
</style>
