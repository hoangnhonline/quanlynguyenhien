<div class="form-group">
  <label for="time_type1">
    <input id="time_type1" type="radio" name="time_type" value="1"  {{ $time_type == 1 ? "checked" : "" }}> Tháng
  </label>
  <label for="time_type2">
    <input id="time_type2" type="radio" name="time_type" value="2"  {{ $time_type == 2 ? "checked" : "" }}> Giai đoạn
  </label>
  <label for="time_type3">
    <input id="time_type3" type="radio" name="time_type" value="3"  {{ $time_type == 3 ? "checked" : "" }}> Ngày
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
    <option value="2022" {{ $arrSearch['year'] == 2022 ? "selected" : "" }}>2022</option>
    <option value="2023" {{ $arrSearch['year'] == 2023 ? "selected" : "" }}>2023</option>
    <option value="2024" {{ $arrSearch['year'] == 2024 ? "selected" : "" }}>2024</option>
    <option value="2025" {{ $arrSearch['year'] == 2025 ? "selected" : "" }}>2025</option>
    <option value="2026" {{ $arrSearch['year'] == 2026 ? "selected" : "" }}>2026</option>
  </select>
</div>
@endif
@if($time_type == 2 || $time_type == 3)            
<div class="form-group chon-ngay">              
  <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_from" placeholder="@if($time_type == 2) Từ ngày @else Ngày @endif " value="{{ $arrSearch['use_date_from'] }}" style="width: 120px">
</div>
@if($time_type == 2)
<div class="form-group chon-ngay den-ngay">              
  <input type="text" class="form-control datepicker" autocomplete="off" name="use_date_to" placeholder="Đến ngày" value="{{ $arrSearch['use_date_to'] }}" style="width: 120px">
</div>
 @endif
@endif