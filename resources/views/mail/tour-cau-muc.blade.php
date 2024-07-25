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
            <p style="font-size: 15px;"><b>Plan To Travel</b> có yêu cầu đặt tour với thông tin cụ thể như sau:</p>
            <table class="table table-bordered" width="700" style="color: #1f497d;">
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <h3>YÊU CẦU ĐẶT TOUR</h3>
                    </td>
                </tr>
            </table>
            <table width="700" border="1" cellspacing="0" cellpadding="10" style="color: #1f497d;">
                <tr>
                    <td>
                        CODE
                    </td>
                    <td>
                        PTT{{ $detail->id }}
                    </td>
                </tr>
                <tr>
                    <td width="170">
                        TOUR
                    </td>
                    <td>
                        TOUR CÂU MỰC
                    </td>
                </tr>
                <tr>
                    <td width="170">
                        Tên KH
                    </td>
                    <td>
                        {!! $detail->name !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        Số điện thoại
                    </td>
                    <td>
                        {!! $detail->phone !!}
                    </td>
                </tr>
                
                <tr>
                    <td>
                        Ngày đi
                    </td>
                    <td>
                        {!! date('d/m/Y', strtotime($detail->use_date)) !!}
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
                
                <tr>
                    <td>Danh sách khách</td>
                    <td>
                        <span style="text-transform: uppercase;">{!! nl2br($detail->danh_sach) !!}</span>
                    </td>
                </tr>                
                <tr>
                    <td>
                        Ghi chú
                    </td>
                    <td style="color: red;">
                        {!! nl2br($detail->notes_hotel) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        Sales
                    </td>
                    <td>
                        {{ $userDetail->name }} - <a href="tel:{{ $userDetail->phone }}">{{ $userDetail->phone }}</a>
                    </td>
                </tr>
            </table>
            <p style="font-size: 15px;"><span style="font-weight: bold;">Quý đối tác</span> vui lòng kiểm tra thông tin và phản hồi email. Trân trọng cảm ơn.</p>
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
