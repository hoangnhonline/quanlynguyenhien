<div class="form-group">
  <label for="time_type1">
    <input id="time_type1" type="radio" name="time_type" value="1"  {{ $time_type == 1 ? "checked" : "" }}> Theo tháng
  </label>
  <label for="time_type2">
    <input id="time_type2" type="radio" name="time_type" value="2"  {{ $time_type == 2 ? "checked" : "" }}> Khoảng ngày
  </label>
  <label for="time_type3">
    <input id="time_type3" type="radio" name="time_type" value="3"  {{ $time_type == 3 ? "checked" : "" }}> Theo ngày
  </label>
</div> 
@if($time_type == 1)
<div class="form-group  chon-thang">                
  <select class="form-control select2" id="month_change" name="month">
    <option value="">--THÁNG--</option>
    @for($i = 1; $i <=12; $i++)
    <option value="{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}" {{ $arrSearch['month'] == $i ? "selected" : "" }}>{{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
    @endfor
  </select>
</div>
<div class="form-group  chon-thang">                
  <select class="form-control select2" id="year_change" name="year">
    <option value="">--NĂM--</option>
    <option value="2020" {{ $arrSearch['year'] == 2020 ? "selected" : "" }}>2020</option>
    <option value="2021" {{ $arrSearch['year'] == 2021 ? "selected" : "" }}>2021</option>
    <option value="2022" {{ $arrSearch['year'] == 2022 ? "selected" : "" }}>2022</option>
    <option value="2023" {{ $arrSearch['year'] == 2023 ? "selected" : "" }}>2023</option>
    <option value="2024" {{ $arrSearch['year'] == 2024 ? "selected" : "" }}>2024</option>
  </select>
</div>
@endif
@if($time_type == 2 || $time_type == 3)            
<div class="form-group chon-ngay">              
  <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['checkin_from'] }}"  style="width: 120px">
</div>
@if($time_type == 2)
<div class="form-group chon-ngay den-ngay">              
  <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['checkin_to'] }}" style="width: 120px">
</div>
 @endif
@endif