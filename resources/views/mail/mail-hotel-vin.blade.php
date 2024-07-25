<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>email</title>
    </head>

    <body>
        <div class="container">
            <p style="font-size: 15px;">Dear <b>quý đối tác,</b></p>
            <p style="font-size: 15px;"><b>Plan To Travel</b> có yêu cầu đặt phòng với thông tin cụ thể như sau:</p>
            <table class="table table-bordered" width="700" style="color: #1f497d;">
                <tr>
                    <td colspan="3" style="text-align: left;">
                        <h4>YÊU CẦU ĐẶT PHÒNG</h4>
                    </td>
                </tr>
            </table>

            <h4>Vinpearl: Phú Quốc</h4>
            <h4>Khu: {{ $detailHotel->name }}</h4>
            <p>
                <strong>Ngày ở:</strong>{!! date('d/m/Y', strtotime($detail->checkin)) !!} - {!! date('d/m/Y', strtotime($detail->checkout)) !!}<br />
                <strong>SL: </strong>@foreach($detail->rooms as $room) {{ $room->room_amount }} phòng  {{ $room->room->name }}<br />
                @endforeach
                <strong>Số ĐT: </strong> {{ $detail->phone }}<br />
                <strong>Danh sách:</strong><br />
                <span style="text-transform: uppercase;">{!! nl2br($detail->danh_sach) !!}</span>
                <br />
            </p>
            <p>
                <strong>Phụ thu:</strong> {{ number_format($detail->extra_fee) }} (<i>{{ $detail->extra_fee_notes }}</i>)
                
            </p>
            <p>
                <strong>Thông tin đón tiễn:</strong><br />
                &nbsp;&nbsp;&nbsp;- Đón: <strong>{{ $detail->don_bay }}</strong><br />
                &nbsp;&nbsp;&nbsp;- Tiễn: <strong>{{ $detail->tien_bay }}</strong>
            </p>

            <p>
                <strong>Ghi chú:</strong> <br />
                {!! nl2br($detail->notes_hotel) !!}
            </p>
            <p>
                <strong>Code nội bộ:</strong> <br />
                PTH{{ $detail->id }}
            </p>
            <br />
            <p style="font-size: 15px;">Vui lòng kiểm tra thông tin và phản hồi email. Trân trọng cảm ơn.</p>
            <br />
            <br />
            <br />

            <div>
                <div dir="ltr" data-smartmail="gmail_signature">
                    <div dir="ltr">
                        <div>
                            <div dir="ltr">
                                <div dir="ltr">
                                    <h4 style="line-height: 1.1; margin-top: 10px; margin-bottom: 10px;">
                                        <font face="Helvetica Neue, Helvetica, Arial, sans-serif">Thanks and best regards</font><i style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 18px;">,</i>
                                        <font color="#0b5394" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 500;"><br /></font>
                                    </h4>
                                    <h4 style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.1; margin-top: 10px; margin-bottom: 10px;">
                                        <font color="#0b5394">CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ VÀ DU LỊCH PLAN TO TRAVEL</font>
                                    </h4>
                                    <div>
                                        <b>Chuyên:</b> Tổ chức tour đảo chụp ảnh, flycam chuyên nghiệp, book phòng khách sạn Phú Quốc, cho thuê xe 4C,7C,16C, vé tham quan Vinpearl, cáp treo, hỗ trợ <b>lên lịch tham quan chuyên nghiệp miễn phí</b> giúp tiết kiệm chi phí tại Phú Quốc.
                                    </div>
                                    <span style="color: rgb(0, 0, 0); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;"><b>Mã số thuế</b>:&nbsp;</span>0315788585
                                    <br style="color: rgb(0, 0, 0); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;" />
                                    <span style="color: rgb(0, 0, 0); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;"><b>Địa chỉ:</b> 08 Trần Hưng Đạo, Dương Đông, Phú Quốc</span>
                                    <span style="color: rgb(0, 0, 0); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;">
                                        <br />
                                        <b>Hotline:</b>
                                    </span>
                                    <span style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;">
                                        <font color="#ff0000"><b>0911 380 111</b></font>
                                    </span>
                                    <div class="yj6qo"></div>
                                    <div class="adL"><br /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
