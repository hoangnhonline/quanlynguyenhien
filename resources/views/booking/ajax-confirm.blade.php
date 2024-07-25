<table class="table table-borderd table-striped">
	<tr>
		<th class="text-center">STT</th>
		<th class="text-right">BOOKING ID</th>
		<th class="text-right">SỐ TIỀN</th>
	</tr>
	<?php $i = $total = 0; ?>
	@foreach($arrCost as $booking_id => $arr)
	<?php $i++; 
	$total += $arr['tien_thuc_thu'];
	?>
	<tr>
		<td class="text-center" width="1%">{{ $i }}</td>
		<td class="text-right">
			{{ $arr['code'] }}
		</td>
		<td class="text-right">
			{{ number_format($arr['tien_thuc_thu']) }}
		</td>
	</tr>
	@endforeach
	<tr>
		<td colspan="2" class="text-right">Tổng tiền</td>
		<td class="text-right" style="color: red">{{ number_format($total) }}</td>
	</tr>
</table>
<div class="form-group">
	<textarea class="form-control" id="content_nop" rows="3" placeholder="Ghi chú nội dung nộp"></textarea>
</div>
<input type="hidden" id="dt" value="{{ $dt }}">
<p style="color: red; font-size: 20px; font-weight: bold; margin: 15px;" id="noi_dung_nop"></p>