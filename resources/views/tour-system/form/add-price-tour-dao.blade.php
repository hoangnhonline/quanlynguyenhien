<form role="form" method="POST" action="{{ route('tour-system.store-price') }}" id="dataForm" class="productForm">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Thêm mới</h3>
      </div>
      <!-- /.box-header -->               
        {!! csrf_field() !!}          
        <div class="box-body">
          <input type="hidden" name="city_id" value="{{ $detailTour->city_id }}">
          <input type="hidden" name="tour_id" value="{{ $detailTour->id }}">
          <input type="hidden" name="id" value="">
            @if (count($errors) > 0)
              <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
              </div>
            @endif
            <p style="font-weight: bold; color: red;font-size: 15px;text-transform: uppercase;">Ghi chú : Nếu giá 1 ngày thì không cần nhập "Đến ngày" </p>
            <p>
            
            </p>
            <div class="row" style="margin-bottom: 5px">
              <div class="form-group col-xs-4">               
                  <label>Đối tác <span class="red-star">*</span></label>
                  <select name="partner_id" class="form-control select2" id="partner_id">
                    <option value="">--Chọn--</option>
                    @foreach($partnerList as $partner)
                    <option value="{{ $partner->id }}" {{ old('partner_id', $partner_id) == $partner->id ? "selected" : "" }}>{{ $partner->name }}</option>
                    @endforeach
                  </select>                    
              </div>
              <div class="form-group col-xs-4">                  
                <label>Hình thức <span class="red-star">*</span></label>
                <select class="form-control select2" id="tour_type" name="tour_type">                      
                    <option value="1" {{ old('tour_type') == 1 ? "selected" : "" }}>Tour ghép</option>
                    <option value="2" {{ old('tour_type') == 2 ? "selected" : "" }}>Tour VIP</option>
                    <option value="3" {{ old('tour_type') == 3 ? "selected" : "" }}>Thuê cano</option>
                </select>
              </div>
              <div class="form-group col-xs-4">                  
                <label>Loại cano<span class="red-star">*</span></label>
                <select class="form-control select2" id="cano_type" name="cano_type">                      
                    <option value="1" {{ old('cano_type') == 1 ? "selected" : "" }}>Cano SB</option>
                    <option value="2" {{ old('cano_type') == 2 ? "selected" : "" }}>Cano thường</option>
                </select>
              </div>
            </div>
            <div class="row">
            <div class="form-group col-md-4">
              <label>Phân loại sales<span class="red-star">*</span></label>
              <select class="form-control select2" name="level" id="level">      
                <option value="" >--Chọn--</option>
                <option value="1" {{ $level == 1 ? "selected" : "" }}>CTV Group</option>  
                <option value="2" {{ $level == 2 ? "selected" : "" }}>ĐỐI TÁC</option>      
                <option value="6" {{ $level == 6 ? "selected" : "" }}>NV SALES</option>
                <option value="7" {{ $level == 7 ? "selected" : "" }}>GỬI BẾN</option>
              </select>
            </div>
            <div class=" col-md-4 form-group" >                  
              <label>Từ ngày</label>
              <input type="text" class="form-control req datepicker" autocomplete="off" name="from_date" id="from_date" value="{{ old('from_date') }}">
            </div> 
            <div class=" col-md-4 form-group" >                  
              <label>Đến ngày</label>
              <input type="text" class="form-control req datepicker" autocomplete="off" name="to_date" id="to_date" value="{{ old('to_date') }}" >
            </div>
            </div>
            <div class="row" >
              <div class="form-group col-xs-6">               
                  <label>Số lượng từ <span class="red-star">*</span></label>
                  <select name="from_adult" class="form-control select2" id="from_adult">
                    @for($i = 1; $i < 1001; $i++)
                    <option value="{{ $i }}" {{ old('from_adult') == $i ? "selected" : "" }}>{{ $i }}</option>
                    @endfor
                  </select>                    
              </div>
              <div class="form-group col-xs-6">                  
                <label>Số lượng đến  <span class="red-star">*</span></label>
                <select class="form-control select2" id="to_adult" name="to_adult">     
                    @for($i = 1; $i < 1001; $i++)
                    <option value="{{ $i }}" {{ old('to_adult') == $i ? "selected" : "" }}>{{ $i }}</option>
                    @endfor
                </select>
              </div>
            </div>                 
                    
                 <div class="row">
                   <div class=" col-md-4 form-group" >                  
                    <label>Giá NL</label>
                    <input type="text" class="form-control req number" name="price" id="price" value="{{ old('price') }}" autocomplete="off">
                  </div>
                  <div class="col-md-4 form-group" >                  
                    <label>Giá TE</label>
                    <input type="text" class="form-control req number" name="price_child" id="price_child" value="{{ old('price_child') }}" autocomplete="off">
                  </div>
                  <div class="col-md-4 form-group" >                  
                    <label>Giá TE KO CÁP</label>
                    <input type="text" class="form-control req number" name="price_child_no_cable" id="price_child_no_cable" value="{{ old('price_child_no_cable') }}" autocomplete="off">
                  </div>                
                   
                 </div> 
                 <div class="row">
                  <div class=" col-md-3 form-group" >                  
                    <label>Cáp treo NL</label>
                    <input type="text" class="form-control req number" name="cap_nl" id="cap_nl" value="{{ old('cap_nl') }}" autocomplete="off">
                  </div>                       
                  <div class="col-md-3 form-group" >                  
                    <label>Cáp treo TE</label>
                    <input type="text" class="form-control req number" name="cap_te" id="cap_te" value="{{ old('cap_te') }}" autocomplete="off">
                  </div>
                  <div class="col-md-3 form-group" >                  
                    <label>Phần ăn NL</label>
                    <input type="text" class="form-control req number" name="meals" id="meals" value="{{ old('meals') }}" autocomplete="off">
                  </div> 
                  <div class="col-md-3 form-group" >                  
                    <label>Phần ăn TE</label>
                    <input type="text" class="form-control req number" name="meals_te" id="meals_te" value="{{ old('meals_te') }}" autocomplete="off">
                  </div>
                  <div class="col-md-12 form-group" >                  
                    <label>Phụ thu (nếu có - vd: Chụp ảnh đối với Thuê cano)</label>
                    <input type="text" class="form-control req number" name="extra_fee" id="extra_fee" value="{{ old('extra_fee') }}" autocomplete="off">
                  </div>
                 </div> 
                  
                  
         
            
        </div>
        <div class="box-footer">              
          <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
          <input type="hidden" name="is_new" id="is_new" value="0">
          <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
          
          <a class="btn btn-default" class="btn btn-primary" href="{{ route('tour-system.index')}}">Hủy</a>
        </div>
        
    </div>
    <!-- /.box -->     

  </div>      
</form>