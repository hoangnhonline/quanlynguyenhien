<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Xác nhận đặt tour</title>

        <!-- Bootstrap core CSS -->
        <link href="../../dist/css/bootstrap.min.css" rel="stylesheet" />

        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script type="text/javascript"></script>
        <style type="text/css">
            body {
                font-family: DejaVu Sans;
            }
        </style>

    </head>

    <body>
        <div class="container" style="margin-top: 10px;">
            <table border="1" cellspacing="0" cellpadding="10" class="table table-bordered" width="100%" style="color: #1f497d; margin: 0 auto;">
                <tr>
                    <td colspan="2">
                        <img src="{{ asset('images/logo-plan-to-travel.png?ver=1.0') }}" alt="logo" width="130px" align="left" />
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
                <tr style="clear: both;">
                    <td colspan="2" style="text-align: center;">
                        <h3>XÁC NHẬN ĐẶT @if($detail->type == 1) TOUR @elseif($detail->type == 4) XE @elseif($detail->type == 5) LỊCH CHỤP ẢNH  @endif</h3>
                    </td>
                </tr>

                <tr>
                    <td width="170">
                        Mã booking
                    </td>
                    <td>
                        <span style="font-weight: bold; font-size: 20px; color: red;">@if($detail->type == 1) PTT{{$detail->id}} @elseif($detail->type == 4) PTX{{$detail->id}}  @elseif($detail->type == 5) PTC{{$detail->id}} @endif</span>
                    </td>
                </tr>
                @if ( $detail->type == 1 || $detail->type == 4)
                <tr>
                    <td width="170">
                        Loại tour
                    </td>
                    <td>
                        @if($detail->source == 'website')
                            {{$detail->tour->name}}
                        @else
                                
                            {{ $tourSystem[$detail->tour_id] }} /
                            <?php
                            if($detail->tour_type == 1){ $tour_type = 'Tour ghép'; }elseif($detail->tour_type == 2){ $tour_type = 'Tour VIP'; }else{ $tour_type = 'Thuê cano'; } ?> {{ $tour_type }}
                        @endif
                    </td>
                </tr>
                @endif
                <tr>
                    <td>
                        @if( $detail->type == 1 || $detail->type == 4) Ngày đi: @elseif($detail->type == 5) Ngày chụp @endif
                    </td>
                    <td>
                        {{ date('d/m/Y', strtotime($detail->use_date)) }} @if($detail->type == 4 || $detail->type == 5) - {{ $detail->time_pickup }} @endif
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
                        Nơi đón
                    </td>
                    <td>
                        @if($detail->location_id) {{ $detail->location->name }} @else {{ $detail->address }} @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        Người lớn/Trẻ em/Em bé
                    </td>
                    <td>
                        {{ $detail->adults }}/{{ $detail->childs }}/{{ $detail->infants }}
                    </td>
                </tr>
                @if($detail->type == 1)
                <tr>
                    <td>
                        Số phần ăn
                    </td>
                    <td style="font-weight: bold;">
                        <?php
              $meals = $detail->meals; if($meals > 0){ $meals+= $detail->meals_te/2; } ?> {{ $meals }}
                    </td>
                </tr>
                @endif
                @if($detail->tour_id == 1)
                <tr>
                  <td>
                    Vé cáp NL/TE
                  </td>
                  <td>
                    {{ $detail->cap_nl }}/{{ $detail->cap_te }}
                  </td>
                </tr>
                @endif
                @if($detail->extra_fee)
                <tr>
                    <td>Phụ thu</td>
                    <td>
                        {{ number_format($detail->extra_fee) }}
                    </td>
                </tr>
                @endif @if($detail->discount)
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
                @if($detail->tien_coc) @if($detail->tien_coc == $detail->total_price)
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
                    <td style="color: red; font-weight: bold;">
                        {{ number_format($detail->tien_coc) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Còn lại
                    </td>
                    <td style="color: red; font-weight: bold;">
                        {{ number_format($detail->con_lai)}}
                    </td>
                </tr>
                @endif @endif

                <tr>
                    <td>
                        Ghi chú
                    </td>
                    <td>

                        @if($detail->ko_cap_treo && $detail->tour_id == 1) KHÔNG ĐI CÁP TREO <br />
                        @endif @if($detail->notes) {!! nl2br($detail->notes ) !!} @endif
                    </td>
                </tr>

                <tr>
                    <td>
                        Sales
                    </td>
                    <td style="font-weight: bold;">
                        {{ $sales }} @if($detail->user_id == 39) ❤ @endif - {{ $sales_phone }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Hotline
                    </td>
                    <td>
                        <span style="font-weight: bold; font-size: 16px; color: red;">0911.380.111</span>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
