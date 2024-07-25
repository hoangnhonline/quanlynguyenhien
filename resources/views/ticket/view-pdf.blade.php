<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>View PDF</title>
<!-- Bootstrap core CSS -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap" rel="stylesheet" />
        <style>
            body {
                font-family: 'Rubik', sans-serif;
            }
        </style>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script type="text/javascript"></script>
        <script src="https://use.fontawesome.com/92c6fc74a0.js"></script>
    </head>

  <body>
  	<div class="container" style="margin: 0 auto; max-width:700px" >      
  		<table cellspacing="0" cellpadding="10" class="table table-bordered" width="100%" style="@if($detail->user_id_manage==333) color:#333; @else color:#1f497d; @endif; margin: 20px auto;">
          <tr>
              <td colspan="2">
                  <img src="{{ asset('assets/images/logo.png') }}" alt="logo" width="90px" align="left" />
                  <div style="text-align: right; font-size: 12px; color: #5f6368;">
                      <p style="font-weight: bold; margin-top: 5px; margin-bottom: 5px;">CTY TNHH TMDV & DU LỊCH PLAN TO TRAVEL</p>
                      ĐC: <b>08 Trần Hưng Đạo, Dương Đông, Phú Quốc</b>
                      <br />
                      MST: <b>0315788585</b>
                      <br />
                      Hotline: <b>0911 380 111</b>
                  </div>
              </td>
          </tr>
  			<tr style="clear:both;">
  				<td colspan="2" style="text-align: center;">
					   <h4>XÁC NHẬN ĐẶT VÉ</h4>
				  </td>
  			</tr>
        
        <tr>
          <td width="170">
            Mã booking 
          </td>
          <td >
            <span style="font-weight: bold;font-size: 16px;color: red">PTV{{$detail->id}}</span>
          </td>
        </tr>
            
        
        <tr>
          <td>
            Ngày đi:
          </td>
          <td>
            {{ date('d/m/Y', strtotime($detail->use_date)) }}
          </td>
        </tr>
        
        <tr>
          <td>
            Tên KH
          </td>
          <td>
            {{ $detail->name }}
          </td>
        </tr>
        <tr>
          <td>
            Số điện thoại: 
          </td>
          <td>
           {{ $detail->phone }}
          </td>
        </tr>
        <tr>
          <td>
            Nơi giao
          </td>
          <td>
           
           @if($detail->location_id)
          {{ $detail->location->name }}
          @else
          {{ $detail->address }}
          @endif
          </td>
        </tr>
        <tr>
          <td>
            Vé
          </td>
          <td>
           
                 
                 
                  @foreach($detail->tickets as $r)                  
                 
                      @if(isset($ticketTypeArr[$r->ticket_type_id]))
                     {{ $r->amount }} vé  {{ $ticketTypeArr[$r->ticket_type_id] }}                   
                      @else
                     {{ $r->amount }} vé {{ $r->ticket_type_id }} 
                      @endif                      
                    x {{ number_format($r->price_sell) }} VNĐ
                   <br>

                  @endforeach
          </td>
        </tr>
       
        @if($detail->extra_fee)
        <tr>
          <td>Phụ thu</td>
          <td>
            {{ number_format($detail->extra_fee) }}
          </td>
        </tr>
        @endif
        @if($detail->discount)
        <tr>
          <td>Giảm giá</td>
          <td>
            {{ number_format($detail->discount) }}
          </td>
        </tr>
        @endif
        <tr>
          <td>Tổng tiền</td>
          <td>
            {{ number_format($detail->total_price) }}
          </td>
        </tr>
        @if($detail->tien_coc)
            @if($detail->tien_coc == $detail->total_price)
            <tr>
            <td colspan="2">
               ĐÃ THANH TOÁN
              </td>
            </tr>
            @else
            <tr>
              <td>
                Tiền cọc
              </td>
              <td style="color:red;font-weight: bold">
                    {{ number_format($detail->tien_coc) }}
              </td>

            </tr>
            <tr>
              <td>
                Còn lại
              </td>
              <td style="color:red;font-weight: bold">
                    {{ number_format($detail->con_lai)}}
              </td>

            </tr>
          @endif
        @endif
        
        <tr>
          <td>
            Ghi chú
          </td>
          <td>
            @if($detail->notes)
                {!! nl2br($detail->notes ) !!}
            @endif
          </td>

        </tr>
        <tr>
          <td>
            Sales
          </td>
          <td style="font-weight: bold">
                {{ $sales }} - {{ $sales_phone }}
          </td>

        </tr>
        <tr>
          <td>
            Hotline
          </td>
          <td>
                <span style="font-weight: bold;font-size: 16px;color: red">0911.380.111</span>
          </td>

        </tr>
                
        
      </table>
     

  	</div>
   	
  </body>
</html>