@if($notiList->count() > 0)
    <div style="margin-bottom: 10px; text-align: right">
        <a href="{{ route('noti.noti-read-all') }}" class="btn btn-sm btn-danger btnReadAll">Đã xem tất cả ({{$notiList->count()}})</a>
    </div>
@endif

@foreach($notiList as $noti)
<div class="alert" style="background-color: #f2dede !important;border-color: #ebccd1; color: #a94442; margin-bottom: 5px;">
	{{ $noti->title }}
	<span class="btn btn-sm btn-warning btnReadNoti" data-link="{{ route('noti.read', ['id' => $noti->id]) }}" style="float: right;">Đã xem</span>
</div>
@endforeach
<script type="text/javascript">
	$(document).ready(function(){
		$('.btnReadNoti').click(function(){
			if(confirm('Bạn chắc chắn đã xem thông báo này?')){
				$.ajax({
					url : $(this).data('link'),
					type : "GET",
					success: function(){
						location.reload();
					}
				});
			}
		});

        $('.btnReadAll').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            if(confirm('Bạn chắc chắn đã xem tất cả thông báo?')){
                $.ajax({
                    url : link,
                    type : "POST",
                    success: function(){
                        location.reload();
                    }
                });
            }
        });
	});
</script>
