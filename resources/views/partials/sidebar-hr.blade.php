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
    
      
      
      <li class="treeview {{ in_array($routeName, ['task.index', 'task.create', 'task.edit', 'task-detail.index', 'task-detail.edit', 'task-detail.create']) ? 'active' : '' }}" >
          <a href="#">
            <i class="fa fa-twitch"></i> 
            <span>QUẢN LÝ CÔNG VIỆC</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          
          <ul class="treeview-menu">
            <li {{ in_array($routeName, ['task.index', 'task.edit', 'task.create']) ? "class=active" : "" }}><a href="{{ route('task.index') }}"><i class="fa fa-circle-o"></i> Công việc</a></li>
            <li {{ in_array($routeName, ['task-detail.index', 'task-detail.edit']) ? "class=active" : "" }}><a href="{{ route('task-detail.index') }}"><i class="fa fa-circle-o"></i> Quản lý công việc</a></li>
            <li {{ in_array($routeName, ['task-detail.create']) ? "class=active" : "" }}><a href="{{ route('task-detail.create') }}"><i class="fa fa-circle-o"></i> Thêm mới</a></li>
            
          </ul>
          
        </li>                  
      
      <li {{ in_array($routeName, ['payment-request.index', 'payment-request.edit', 'payment-request.create']) ? "class=active" : "" }}>
        <a href="{{ route('payment-request.index') }}">
          <img src="{{ asset('admin/dist/img/payment-request.png') }}" alt="Yêu cầu thanh toán" width="20px">
          <span>YÊU CẦU THANH TOÁN</span>          
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