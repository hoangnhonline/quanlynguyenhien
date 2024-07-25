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
            <li class="treeview {{ in_array($routeName, ['customer.index', 'customer.create', 'customer.edit']) ? "active" : "" }}">
                <a href="{{ route('customer.index') }}">
                    <img src="{{ asset('admin/dist/img/cskh.png') }}" alt="Chăm sóc khách hàng" width="20px">
                    <span>Khách hàng</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>

                <ul class="treeview-menu">
                    <li {{ in_array($routeName, ['customer.index', 'customer.edit', 'customer.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('customer.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
                    <li {{ in_array($routeName, ['user-balance-withdraw.index', 'user-balance-withdraw.edit', 'user-balance-withdraw.create']) ? "class=active" : "" }}>
                        <a href="{{ route('user-balance-withdraw.index') }}"><i class="fa fa-circle-o"></i> Yêu cầu rút
                            tiền</a></li>
                </ul>
            </li>
            <li class="treeview {{ in_array($routeName, ['booking.index', 'booking.create', 'booking.edit']) && (isset($type) && $type == 1) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-superpowers"></i>
                    <span>QUẢN LÝ BOOKING</span>
                    <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                </a>

                <ul class="treeview-menu">
                    <li {{ in_array($routeName, ['booking.index', 'booking.edit']) ? "class=active" : "" }}><a
                            href="{{ route('booking.index') }}"><i class="fa fa-circle-o"></i> Tour</a></li>
                    <li {{ in_array($routeName, ['booking-hotel.index', 'booking-hotel.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('booking-hotel.index') }}"><i class="fa fa-circle-o"></i> Khách sạn</a></li>
                    <li {{ in_array($routeName, ['booking-vmb.index', 'booking-vmb.edit']) ? "class=active" : "" }}><a
                            href="{{ route('booking-vmb.index') }}"><i class="fa fa-circle-o"></i> Vé máy bay</a></li>
                    <li {{ in_array($routeName, ['booking-ticket.index', 'booking-ticket.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('booking-ticket.index') }}"><i class="fa fa-circle-o"></i> Vé vui chơi</a>
                    </li>
                    <li {{ in_array($routeName, ['booking-combo.index', 'booking-combo.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('booking-combo.index') }}"><i class="fa fa-circle-o"></i> Combo</a></li>
                </ul>
            </li>

            <li class="treeview {{ in_array($routeName, ['maxi.index', 'maxi.create', 'maxi.edit']) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-umbrella" aria-hidden="true"></i>
                    <span>THUÊ MAXI</span>
                    <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                </a>

                <ul class="treeview-menu">
                    <li {{ in_array($routeName, ['maxi.index', 'maxi.edit',]) ? "class=active" : "" }}><a
                            href="{{ route('maxi.index') }}"><i class="fa fa-circle-o"></i> Danh sách</a></li>
                    <li {{ in_array($routeName, ['maxi.create']) ? "class=active" : "" }}><a href="{{ route('maxi.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li>
                </ul>
            </li>

            @if(Auth::user()->is_limit == 0)

                <li class="treeview {{ (in_array($routeName, ['booking-car.calendar', 'booking-car.index', 'booking-car.create', 'booking-car.edit']) || $routeName == 'report.car') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-cab"></i>
                        <span>ĐẶT XE</span>
                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                    </a>

                    <ul class="treeview-menu">
                        <li {{ in_array($routeName, ['booking-xe-free.index', 'booking-xe-free.edit', 'booking-xe-free.create']) ? "class=active" : "" }}>
                            <a href="{{ route('booking-xe-free.index') }}"><i class="fa fa-circle-o"></i> Xe miễn
                                phí</a></li>
                        <li {{ in_array($routeName, ['booking-car.index', 'booking-car.edit']) ? "class=active" : "" }}>
                            <a
                                href="{{ route('booking-car.index') }}"><i class="fa fa-circle-o"></i> Xe có tài</a>
                        </li>
                        @if(!Auth::user()->view_only)
                            <li {{ in_array($routeName, ['booking.create']) && isset($type) && $type == 4 ? "class=active" : "" }}>
                                <a href="{{ route('booking-car.create') }}"><i class="fa fa-circle-o"></i> Tạo
                                    booking</a></li>
                        @endif
                        <li {{ in_array($routeName, ['booking-tu-lai.index', 'booking-tu-lai.edit', 'booking-tu-lai.create']) ? "class=active" : "" }}>
                            <a href="{{ route('booking-tu-lai.index') }}"><i class="fa fa-circle-o"></i> Xe tự lái</a>
                        </li>
                        <li {{ in_array($routeName, ['booking-xe-may.index', 'booking-xe-may.edit', 'booking-xe-may.create']) ? "class=active" : "" }}>
                            <a href="{{ route('booking-xe-may.index') }}"><i class="fa fa-circle-o"></i> Xe máy</a></li>
                        <li {{ in_array($routeName, ['booking-car.calendar']) ? "class=active" : "" }}>
                            <a href="{{ route('booking-car.calendar') }}"><i class="fa fa-circle-o"></i>Lịch trình</a></li>

                    </ul>
                </li>

                <li class="treeview {{ (in_array($routeName, ['booking-camera.index', 'booking-camera.create', 'booking-camera.edit'])  || in_array($routeName, ['media.index', 'media.diem-danh'])) ? 'active' : '' }}"
                " >
                <a href="#">
                    <i class=" fa-camera fa"></i>
                    <span>CHỤP ẢNH</span>
                    <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                </a>

                <ul class="treeview-menu {{ (in_array($routeName, ['booking-camera.index', 'booking-camera.create', 'booking-camera.edit']) || in_array($routeName, ['media.index', 'media.diem-danh']))  ? 'active' : '' }}">
                    <li {{ (in_array($routeName, ['booking-camera.index', 'booking-camera.create', 'booking-camera.edit'])) ? "class=active" : "" }}>
                        <a href="{{ route('booking-camera.create') }}"><i class="fa fa-circle-o"></i> Book chụp ảnh</a>
                    </li>
                    <li {{ in_array($routeName, ['media.index']) ? "class=active" : "" }}><a
                            href="{{ route('media.index') }}"><i class="fa fa-circle-o"></i> Link ảnh</a></li>
                    @if(Auth::user()->role == 1)
                        <li {{ in_array($routeName, ['media.diem-danh']) ? "class=active" : "" }}><a
                                href="{{ route('media.diem-danh') }}"><i class="fa fa-circle-o"></i> Điểm danh</a></li>

                    @endif
                    <li {{ in_array($routeName, ['media-rating.index']) ? "class=active" : "" }}><a
                            href="{{ route('media-rating.index') }}"><i class="fa fa-circle-o"></i> Đánh giá</a></li>
                </ul>
                </li>
                @if(Auth::user()->role == 1)
                    <li class="treeview {{ in_array($routeName, ['cost.index', 'cost.create', 'cost.edit', 'revenue.index', 'revenue.create', 'revenue.edit', 'debt.index', 'debt.create', 'debt.edit']) ? 'active' : '' }}">
                        <a href="#">
                            <i class="glyphicon glyphicon-usd"></i>
                            <span>THU CHI</span>
                            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                        </a>

                        <ul class="treeview-menu">
                            <li {{ in_array($routeName, ['cost.index', 'cost.create', 'cost.edit']) ? "class=active" : "" }}>
                                <a href="{{ route('cost.index') }}"><i class="fa fa-circle-o"></i> Chi phí</a></li>
                            <li {{ in_array($routeName, ['payment-request.index', 'payment-request.create', 'payment-request.edit']) ? "class=active" : "" }}>
                                <a href="{{ route('payment-request.index') }}"><i class="fa fa-circle-o"></i> Yêu cầu
                                    thanh toán</a></li>
                            <li {{ in_array($routeName, ['revenue.index', 'revenue.create', 'revenue.edit']) ? "class=active" : "" }}>
                                <a href="{{ route('revenue.index') }}"><i class="fa fa-circle-o"></i> Khoản thu khác</a>
                            </li>
                            <li {{ in_array($routeName, ['debt.index', 'debt.create', 'debt.edit']) ? "class=active" : "" }}>
                                <a href="{{ route('debt.index') }}"><i class="fa fa-circle-o"></i> Công nợ</a></li>

                        </ul>

                    </li>

                    <li {{ in_array($routeName, ['signature']) ? "class=active" : "" }}>
                        <a href="{{ route('signature') }}">
                            <i class="  glyphicon glyphicon-pencil"></i> <span>KÝ TÊN</span>
                        </a>
                    </li>
                @else
                    <li {{ in_array($routeName, ['payment-request.index', 'payment-request.create', 'payment-request.edit']) ? "class=active" : "" }}>
                        <a href="{{ route('payment-request.index') }}">
                            <i class="glyphicon glyphicon-usd"></i> <span>Yêu cầu thanh toán</span>
                        </a>
                    </li>
                @endif


                @if(Auth::user()->role == 1 || Auth::user()->is_staff == 1)
                    <li class="treeview {{ (Route::is('task.*') || Route::is('task-detail.*')) ? "active" : "" }}">
                        <a href="#">
                            <i class="fa fa-tasks"></i>
                            <span>QUẢN LÝ CÔNG VIỆC</span>
                            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                        </a>

                        <ul class="treeview-menu">
                            <li {{ in_array($routeName, ['task.index', 'task.edit', 'task.create']) ? "class=active" : "" }}>
                                <a href="{{ route('task.index') }}"><i class="fa fa-circle-o"></i> Công việc</a></li>
                            <li {{ in_array($routeName, ['task.calendar']) ? "class=active" : "" }}><a
                                    href="{{ route('task.calendar') }}"><i class="fa fa-circle-o"></i> Lịch công việc</a></li>
                            <li {{ in_array($routeName, ['task.reports']) ? "class=active" : "" }}><a
                                    href="{{ route('task.reports') }}"><i class="fa fa-circle-o"></i> Thống kế</a></li>
                            {{-- <li {{ in_array($routeName, ['task-detail.create']) ? "class=active" : "" }}><a href="{{ route('task-detail.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li> --}}
                            <li {{ in_array($routeName, ['plan.index', 'plan.edit', 'plan.create']) ? "class=active" : "" }}>
                                <a href="{{ route('plan.index') }}"><i class="fa fa-circle-o"></i> Kế hoạch</a></li>

                        </ul>

                    </li>
                @endif
                <li {{ in_array($routeName, ['coupon-code.index', 'coupon-code.create', 'coupon-code.edit']) ? "class=active" : "" }}>
                    <a href="{{ route('coupon-code.index') }}">
                        <i class="  fa fa-gift"></i> <span>MÃ GIẢM GIÁ</span>
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
                        <li {{ in_array($routeName, ['report.detail-by-type'])? "class=active" : "" }}><a
                                href="{{ route('report.detail-by-type') }}"><i class="fa fa-circle-o"></i> Chi tiết</a>
                        </li>
                        <li {{ in_array($routeName, ['report.customer'])? "class=active" : "" }}><a
                                href="{{ route('report.customer') }}"><i class="fa fa-circle-o"></i> Khách hàng</a></li>
                        <li {{ in_array($routeName, ['report.customer-by-level'])? "class=active" : "" }}><a
                                href="{{ route('report.customer-by-level') }}"><i class="fa fa-circle-o"></i> Khách theo
                                level</a></li>
                        <li {{ in_array($routeName, ['report.average-guest-by-level'])? "class=active" : "" }}><a
                                href="{{ route('report.average-guest-by-level') }}"><i class="fa fa-circle-o"></i> SL
                                khách</a></li>
                        @if(in_array(Auth::user()->id, [1,7,21,33]))
                            <li {{ in_array($routeName, ['report.doanh-thu-thang'])? "class=active" : "" }}><a
                                    href="{{ route('report.doanh-thu-thang') }}"><i class="fa fa-circle-o"></i> Doanh
                                    thu</a></li>
                            <li {{ in_array($routeName, ['report.loi-nhuan-thang'])? "class=active" : "" }}><a
                                    href="{{ route('report.loi-nhuan-thang') }}"><i class="fa fa-circle-o"></i> Lợi
                                    nhuận</a></li>
                        @endif
                        <li {{ in_array($routeName, ['report.hotel-recent'])? "class=active" : "" }}><a
                                href="{{ route('report.hotel-recent') }}"><i class="fa fa-circle-o"></i> Khách sạn</a>
                        </li>
                        <li {{ in_array($routeName, ['report.hotel-by-user'])? "class=active" : "" }}><a
                                href="{{ route('report.hotel-by-user') }}"><i class="fa fa-circle-o"></i> Khách sạn theo
                                user</a></li>
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}>
                            <a href="{{ route('report.ve-cap-treo') }}"><i class="fa fa-circle-o"></i> Vé cáp treo</a>
                        </li>
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}>
                            <a href="{{ route('report.phan-an') }}"><i class="fa fa-circle-o"></i> Phần ăn</a>
                        </li>
                        <li {{ in_array($routeName, ['report.index'])? "class=active" : "" }}><a
                                href="{{ route('report.cano') }}"><i class="fa fa-circle-o"></i> Cano</a></li>
                        <li {{ in_array($routeName, ['report.car'])? "class=active" : "" }}><a
                                href="{{ route('report.car') }}"><i class="fa fa-circle-o"></i> Xe</a></li>
                    </ul>
                    </li>
                    <li class="treeview {{ in_array($routeName, ['daily-report.index', 'daily-report.hotel']) ? 'active' : '' }}"
                    " >
                    <a href="#">
                        <i class="fa fa-pie-chart" aria-hidden="true"></i>
                        <span>BÁO CÁO</span>
                        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
                    </a>

                    <ul class="treeview-menu">
                        <li {{ in_array($routeName, ['report.general'])? "class=active" : "" }}><a
                                href="{{ route('daily-report.index') }}"><i class="fa fa-circle-o"></i> Tour</a></li>
                        <li {{ in_array($routeName, ['report.detail-by-type'])? "class=active" : "" }}><a
                                href="{{ route('daily-report.hotel') }}"><i class="fa fa-circle-o"></i> Khách sạn</a>
                        </li>
                        <li {{ in_array($routeName, ['report.weekly'])? "class=active" : "" }}><a
                                href="{{ route('report.weekly') }}"><i class="fa fa-circle-o"></i> Báo cáo tuần</a></li>
                    </ul>
                    </li>

                    @if(!Auth::user()->view_only)
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
                            <li {{ in_array($routeName, ['hotel.index', 'hotel.edit', 'hotel.create'])? "class=active" : "" }}>
                                <a href="{{ route('hotel.index') }}"><i class="fa fa-circle-o"></i> Khách sạn</a></li>
                            <li {{ in_array($routeName, ['ticket-type-system.index', 'ticket-type-system.edit', 'ticket-type-system.create'])? "class=active" : "" }}>
                                <a href="{{ route('tour-system.index', ['city_id' => $city_id_default]) }}"><i
                                        class="fa fa-circle-o"></i> Giá tour</a></li>
                            <li {{ in_array($routeName, ['ticket-type-system.index', 'ticket-type-system.edit', 'ticket-type-system.create'])? "class=active" : "" }}>
                                <a href="{{ route('ticket-type-system.index') }}"><i class="fa fa-circle-o"></i> Giá vé</a>
                            </li>
                            <li {{ in_array($routeName, ['combo.index', 'combo.edit', 'combo.create'])? "class=active" : "" }}>
                                <a href="{{ route('combo.index') }}"><i class="fa fa-circle-o"></i> Combo</a></li>
                            <li {{ in_array($routeName, ['partner.index', 'partner.edit', 'partner.create'])? "class=active" : "" }}>
                                <a href="{{ route('partner.index') }}"><i class="fa fa-circle-o"></i> Đại lí phòng/chi
                                    phí</a></li>
                            <li {{ in_array($routeName, ['drivers.index', 'drivers.edit', 'drivers.create'])? "class=active" : "" }}>
                                <a href="{{ route('drivers.index') }}"><i class="fa fa-circle-o"></i> Tài xế</a></li>
                            <li {{ in_array($routeName, ['location.index', 'location.edit', 'location.create'])? "class=active" : "" }}>
                                <a href="{{ route('location.index') }}"><i class="fa fa-circle-o"></i> Điểm đón</a></li>
                            <li {{ in_array($routeName, ['report-setting.index', 'report-setting.edit', 'report-setting.create'])? "class=active" : "" }}>
                                <a href="{{ route('report-setting.index') }}"><i class="fa fa-circle-o"></i> Báo cáo</a>
                            </li>
                            <li {{ in_array($routeName, ['ads-campaign.index', 'ads-campaign.edit', 'ads-campaign.create'])? "class=active" : "" }}>
                                <a href="{{ route('ads-campaign.index') }}"><i class="fa fa-circle-o"></i> Chiến dịch
                                    quảng cáo</a></li>
                            <li {{ in_array($routeName, ['cano.index', 'cano.create', 'cano.edit', 'steersman.index']) ? "class=active" : "" }}>
                                <a
                                    href="{{ route('cano.index') }}"><i class="fa fa-circle-o"></i>Cano</a></li>
                            <li {{ in_array($routeName, ['restaurants.index', 'restaurants.create', 'restaurants.edit']) ? "class=active" : "" }}>
                                <a
                                    href="{{ route('restaurants.index') }}"><i class="fa fa-circle-o"></i>Nhà hàng</a>
                            </li>

                        </ul>
                        </li>
                    @endif
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
    .skin-blue .sidebar-menu > li > .treeview-menu {
        padding-left: 15px !important;
    }
</style>
