@if($strNameMaxi)
<div class="alert alert-danger alert-dismissible" style="padding: 5px;padding-right: 35px;">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
  <i class="icon fa fa-ban"></i> Váy ngày mai sẽ dùng: <span style="font-size: 15px">{{ $strNameMaxi }}</span>
</div>
@endif
@if((Auth::user()->id == 60 || Auth::user()->id == 245 || Auth::user()->id == 1 ) && $count > 0)
<!-- <div class="alert alert-danger alert-dismissible" style="padding: 5px;padding-right: 35px;">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
  <i class="icon fa fa-ban"></i> Hệ thống đang có <span style="font-size: 15px">{{ $count }}</span> yêu cầu thanh toán GẤP. &nbsp;&nbsp;&nbsp;<a class="" href="{{ route('payment-request.index', ['status' => 1, 'urgent' => 1])}}" style="font-style: italic;text-decoration: underline; color: black">Chi tiết</a>
</div> -->
@endif
@if($countTask > 0)
<div class="alert alert-danger alert-dismissible" style="padding: 5px;padding-right: 35px;">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
  <i class="icon fa fa-ban"></i> Bạn đang có <span style="font-size: 15px">{{ $countTask }}</span> công việc mới cần làm. &nbsp;&nbsp;&nbsp;<a class="" href="{{ route('task.index', ['status' => 1])}}" style="font-style: italic;text-decoration: underline;color: black">Chi tiết</a>
</div>
@endif
