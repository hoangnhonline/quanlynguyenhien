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
        <div class="container" style="margin: 0 auto; max-width: 700px;">
            @if($detail->user_id_manage != 333)
            <a target="_blank" href="{{ route('pdf-tour',['id' => $detail->id]) }}" style="padding: 5px 10px; background-color: red; color: #fff; text-decoration: none; margin-bottom: 20px; display: block; text-align: center;">Xuất PDF</a>
            @endif
            <table cellspacing="0" cellpadding="10" class="table table-bordered" width="100%" style="@if($detail->user_id_manage==333) color:#333; @else color:#1f497d; @endif; margin: 20px auto;">
                @if($detail->user_id_manage != 333)
                <tr>
                    <td colspan="2">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo" width="90px" align="left" />
                        <div style="text-align: right; font-size: 12px; color: #5f6368;">
                            <p style="font-weight: bold; margin-top: 5px; margin-bottom: 5px;">CTY TNHH TMDV & DU LỊCH PLAN TO TRAVEL</p>
                            @if($detail->city_id == 1)
                            <i class="fa fa-map-marker" aria-hidden="true"></i> <b>08 Trần Hưng Đạo, Dương Đông, Phú Quốc</b>
                            @else
                            <i class="fa fa-map-marker" aria-hidden="true"></i> <b>386 Dũng Sĩ Thanh Khê, Thanh Khê, Đà Nẵng</b>
                            @endif
                            <br />
                            <i class="fa fa-id-card-o" aria-hidden="true"></i> <b>0315788585</b>

                        </div>
                         <h4 class="text-center" style="color: #f39c12;clear: both;">XÁC NHẬN ĐẶT @if($detail->type == 1) TOUR @elseif($detail->type == 4) XE @elseif($detail->type == 5) LỊCH CHỤP ẢNH  @endif</h4>
                    </td>
                </tr>
                @else
                <tr>
                    <td colspan="2" class="text-center">
                        <img src="{{ asset('images/logo-group.jpg') }}" alt="logo" width="120px" />
                         <h4 class="text-center" style="color: #f39c12;clear: both;">XÁC NHẬN ĐẶT TOUR</h4>
                    </td>
                </tr>
                @endif
                <tr>
                    <td width="170">
                        Mã booking
                    </td>
                    <td>
                        @if($detail->user_id_manage == 333)
                        <span style="font-weight: bold;font-size: 20px;color: red">GT{{$detail->id}}</span>
                        @else
                        <span style="font-weight: bold; font-size: 16px; color: red;">
                            @if($detail->type == 1) PTT{{$detail->id}} @elseif($detail->type == 4) PTX{{$detail->id}}  @elseif($detail->type == 5) PTC{{$detail->id}} @endif
                        </span>
                        @endif

                    </td>
                </tr>
                @if ( $detail->type == 1 || $detail->type == 4)

                    <tr>
                        <td width="170">
                            Loại @if($detail->type == 1) tour @elseif($detail->type == 4) xe @endif
                        </td>
                        <td>
                            @if($detail->type == 4) {{ $detail->carCate->name }}
                            @elseif($detail->type == 1)
                                <span style="text-transform: uppercase;">
                                    @if($detail->source == 'website')
                                        {{$detail->tour->name}}
                                    @else
                                    {{ @$tourSystem[$detail->tour_id] }} /
                                      <?php
                                        if($detail->tour_type == 1){ $tour_type = 'Tour ghép'; }elseif($detail->tour_type == 2){ $tour_type = 'Tour VIP'; }else{ $tour_type = 'Thuê cano'; } ?>
                                    {{ $tour_type }}
                                    @endif
                            @endif
                            </span>
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
                        Điểm đón/trả
                    </td>
                    <td>
                        @if($detail->location_id) {{ $detail->location->name }} @if($detail->location_id_2) => {{ $detail->location2->name }} @endif @else {{ $detail->address }} @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        NL/TE/EB
                    </td>
                    <td>
                        {{ $detail->adults }}/{{ $detail->childs }}/{{ $detail->infants }}
                    </td>
                </tr>
                @if($detail->type == 1 && $detail->city_id == 1)
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
                        {{ $sales }} - {{ $sales_phone }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Hotline
                    </td>
                    <td>
                        <span style="font-weight: bold; font-size: 16px; color: red;">
                            @if($detail->user_id_manage == 333)
                            08.1800.24.55
                            @else
                            0911.380.111
                            @endif
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
