<table class="table table-borderd table-striped">
	<tr>
		<th class="text-center">STT</th>
		<th class="text-right">COST ID</th>
		<th class="text-right">SỐ TIỀN</th>
	</tr>
	<?php $i = $total = 0; ?>
	@foreach($arrCost as $cost_id => $so_tien)
	<?php $i++; 
	$total += $so_tien;
	?>
	<tr>
		<td class="text-center" width="1%">{{ $i }}</td>
		<td class="text-right">
			{{ $cost_id }}
		</td>
		<td class="text-right">
			{{ number_format($so_tien) }}
		</td>
	</tr>
	@endforeach
	<tr>
		<td colspan="2" class="text-right">Tổng tiền</td>
		<td class="text-right" style="color: red">{{ number_format($total) }}</td>
	</tr>
</table>
<p style="color: red; font-size: 20px; font-weight: bold; margin: 15px;" id="noi_dung_chi"></p>