<div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h5 class="mb-0" style="padding-top: 10px; padding-bottom: 5px">

        <button class="btn btn-warning btn-sm" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"  style="margin-top: 5px;">
           <span style="font-weight: bold"> >> XEM GIÁ GỐC</span>
        </button>
        
      </h5>
    </div>

    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
        <table class="table table-bordered">
			<tr>
				<th width="30%">Ngày</th>
				@foreach($firstPriceDate as $partner_id => $rowPrice)
				<?php 
				$tong_goc[$partner_id] = 0;
				?>
				<th class="text-right" width="160">{{ $partnerArrName[$partner_id] }}</th>		
				@endforeach
			</tr>
			
			@foreach($dataArr as $date => $priceArr)
			
			<tr>
				<td>
					{{ $date }}
				</td>
				@foreach($priceArr as $partner_id => $rowPrice)
				<?php $tong_goc[$partner_id]+= $rowPrice; ?>
				<td class="text-right">				
					{{ number_format($rowPrice) }}
				</td>
				@endforeach		
			</tr>
			@endforeach
			<tr>
				<th>Tổng giá gốc</th>
				@foreach($firstPriceDate as $partner_id => $rowPrice)
				<td class="text-right" style="font-weight: bold;">{{ number_format($tong_goc[$partner_id]) }}</td>	
				@endforeach	
			</tr>
		</table>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
	.accordion {
	  background-color: #f0e2ce;
	  color: #444;
	  cursor: pointer;		 
	  width: 100%;
	  text-align: left;
	  border: none;
	  outline: none;
	  transition: 0.4s;
	  padding-left: 10px;		  
	}

	/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
	.accordion:hover {
	  background-color: #f0e2ce;
	}
</style>
<div class="clearfix"></div>