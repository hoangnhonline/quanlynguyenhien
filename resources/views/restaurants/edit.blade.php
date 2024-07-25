@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Nhà hàng
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('restaurants.index') }}">Nhà hàng</a></li>
                <li class="active">Cập nhật</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <a class="btn btn-default btn-sm" href="{{ route('restaurants.index') }}" style="margin-bottom:5px">Quay lại</a>
            <form role="form" method="POST" action="{{ route('restaurants.update') }}" id="dataForm" class="productForm">
                <input type="hidden" name="id" value="{{ $detail->id }}">
                <div class="row">
                    <!-- left column -->

                    <div class="col-md-12">
                        <div id="content_alert"></div>
                        @if(Session::has('message'))
                            <p class="alert alert-info">{{ Session::get('message') }}</p>
                        @endif
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Cập nhật</h3>
                            </div>
                            <!-- /.box-header -->
                            {!! csrf_field() !!}
                            <div class="box-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div>

                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Thông tin chi tiết</a></li>
                                        <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Hình ảnh</a></li>                                 
                                        <li role="presentation"><a href="#meta" aria-controls="settings" role="tab" data-toggle="tab">SEO</a></li>                                        
                                        <li role="presentation"><a href="#lienhe" aria-controls="settings" role="tab" data-toggle="tab">Thông tin liên hệ</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">

                                        <div role="tabpanel" class="tab-pane active" id="home">
                                            <input type="hidden" name="type" value="1">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="city_id">Tỉnh/Thành</label>
                                                    <select class="form-control select2" name="city_id" id="city_id">
                                                        <option value="">--Chọn--</option>
                                                        @foreach($cityList as $city)
                                                            <option value="{{ $city->id }}" {{ old('city_id', $detail->city_id) == $city->id ? "selected" : "" }}>{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div> 
                                                <div class="form-group col-md-6">
                                                    <label for="city_id">Khu vực</label>
                                                    <select class="form-control select2" name="area_id" id="area_id">
                                                        <option value="">--Chọn--</option>
                                                        @foreach($areaList as $area)
                                                            <option value="{{ $area->id }}" {{ old('area_id', $detail->area_id) == $area->id ? "selected" : "" }}>{{ $area->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>       
                                            </div>                                           
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label>Tên nhà hàng <span class="red-star">*</span></label>
                                                    <input type="text" class="form-control req" name="name" id="name" value="{{ old('name', $detail->name) }}">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Slug <span class="red-star">*</span></label>
                                                    <input type="text" class="form-control req" readonly="readonly" name="slug" id="slug" value="{{ old('slug', $detail->slug) }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                
                                                <div class="form-group col-md-6">
                                                    <label>Địa chỉ</label>
                                                    <input type="input" id="address" class="form-control" name="address" value="{{ old('address', $detail->address) }}">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Video Youtube ID<span class="red-star">*</span></label>
                                                    <input type="text" class="form-control req" name="video_id" id="video_id" value="{{ old('video_id', $detail->video_id) }}">
                                                </div>

                                            </div>
                                            <div class="row">                                               
                                                <div class="form-group col-md-6">
                                                    <label>Latitude</label>
                                                    <input type="input" id="latitude" class="form-control" name="latitude" value="{{ old('latitude', $detail->latitude) }}">
                                                </div>                                                                                          
                                                <div class="col-md-6 form-group">
                                                    <label>Longitude<span class="red-star">*</span></label>
                                                    <input type="text" class="form-control req" name="longitude" id="longitude" value="{{ old('longitude', $detail->longitude) }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group  col-md-4">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="is_hot" value="1" {{ old('is_hot', $detail->is_hot) == 1 ? "checked" : "" }}>
                                                            <span style="color:red">NỔI BẬT</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <div class="checkbox">
                                                        <label for="is_home">
                                                            <input type="checkbox" name="is_home" value="1" {{ old('is_home', $detail->is_home) == 1 ? "checked" : "" }}>
                                                            <span style="color:red">HIỆN TRANG CHỦ</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <div class="checkbox">
                                                        <label for="is_show">
                                                            <input type="checkbox" name="is_show" value="1" {{ old('is_show', $detail->is_show) == 1 ? "checked" : "" }}>
                                                            <span style="color:red">HIỆN LÊN WEBSITE</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group" style="margin-top:10px;margin-bottom:10px">
                                                <label class="col-md-3 row">Banner ( 1350x500 px)</label>
                                                <div class="col-md-9">
                                                    <img id="thumbnail_banner lazy" data-original="{{ $detail->banner_url ? Helper::showImage($detail->banner_url ) : asset('admin/dist/img/img.png') }}" class="img-thumbnail" width="300">
                                                    <button class="btn btn-default btn-sm btnSingleUpload" data-set="banner_url" data-image="thumbnail_banner" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button>
                                                    <input type="hidden" name="banner_url" id="banner_url" value="{{ old('banner_url', $detail->banner_url) }}"/>
                                                </div>
                                                <div style="clear:both"></div>
                                            </div>
                                            <div class="form-group" style="margin-top: 15px !important;">
                                                <label>Giới thiệu</label>
                                                <button class="btnUploadEditor btn btn-info" type="button" style="float:right;margin-bottom: 3px !important;" data-editor="description">Chèn ảnh</button>
                                                <div class="clearfix"></div>
                                                <textarea class="form-control" rows="4" name="description" id="description">{{ old('description', $detail->description) }}</textarea>
                                            </div>

                                            <div style="margin-bottom:10px;clear:both"></div>
                                            <div class="clearfix"></div>
                                        </div><!--end thong tin co ban-->
                                        <input type="hidden" id="editor" value="">
                                        <div role="tabpanel" class="tab-pane" id="settings">
                                            <div class="form-group" style="margin-top:10px;margin-bottom:10px">

                                                <div class="col-md-12">

                                                    <input type="file" id="file-image" style="display:none" multiple/>

                                                    <button class="btn btn-success btnMultiUpload" type="button"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Chọn hình ảnh (1200 x 800px)</button>
                                                    
                                                    @include('partials.div-image-edit')
                                                    
                                                </div>
                                                <div style="clear:both"></div>
                                            </div>

                                        </div><!--end hinh anh-->
                                       
                                        <div role="tabpanel" class="tab-pane" id="meta">
                                            <div class="form-group">
                                                <label>Meta title </label>
                                                <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ old('meta_title', $detail->meta_title) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Meta desciption</label>
                                                <textarea class="form-control" rows="6" name="meta_desc" id="meta_desc">{{ old('meta_desc', $detail->meta_desc) }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Meta keywords</label>
                                                <textarea class="form-control" rows="6" name="meta_keywords" id="meta_keywords">{{ old('meta_keywords', $detail->meta_keywords) }}</textarea>
                                            </div>
                                        </div>                                        
                                        <div role="tabpanel" class="tab-pane" id="lienhe">
                                            <div class="form-group">
                                                <label>Email nhà hàng </label>
                                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $detail->email) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Website nhà hàng </label>
                                                <input type="text" class="form-control" name="website" id="website" value="{{ old('website', $detail->website) }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Số điện thoại </label>
                                                <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $detail->phone) }}">
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label for="co_chi">
                                                        <input type="checkbox" id="co_chi" name="co_chi" value="1" {{ old('co_chi', $detail->co_chi) == 1 ? "checked" : "" }}>
                                                        <span style="color:red">Có chi %</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Số phần trăm chi</label>
                                                <select class="form-control select2" name="phan_tram_chi" id="phan_tram_chi">
                                                    <option value="">--Chọn--</option>
                                                    <option value="30" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 30 ? "selected" : "" }}>30%</option>
                                                    <option value="25" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 25 ? "selected" : "" }}>25%</option>
                                                    <option value="20" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 20 ? "selected" : "" }}>20%</option>
                                                    <option value="15" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 15 ? "selected" : "" }}>15%</option>
                                                    <option value="10" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 10 ? "selected" : "" }}>10%</option>
                                                    <option value="5" {{ old('phan_tram_chi', $detail->phan_tram_chi) == 5 ? "selected" : "" }}>5%</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Quy định chi</label>
                                                <input type="text" class="form-control" name="quy_dinh_chi" id="quy_dinh_chi" value="{{ old('quy_dinh_chi', $detail->quy_dinh_chi) }}" placeholder="VD: Chỉ giảm tiền thức ăn, không giảm tiền nước...">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn btn-default" id="btnLoading" style="display:none"><i class="fa fa-spin fa-spinner"></i></button>
                                <button type="submit" class="btn btn-primary" id="btnSave">Lưu</button>
                                <a class="btn btn-default" class="btn btn-primary" href="{{ route('restaurants.index')}}">Hủy</a>
                            </div>

                        </div>
                        <!-- /.box -->

                    </div>
                </div>
            </form>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <input type="hidden" id="route_upload_tmp_image_multiple" value="{{ route('image.tmp-upload-multiple') }}">
    <input type="hidden" id="route_upload_tmp_image" value="{{ route('image.tmp-upload') }}">   
@stop
@section('js')
    <script type="text/javascript">

        $(document).ready(function () {
            $(".select2").select2();
            $('#parent_id').change(function () {
                location.href = "{{ route('restaurants.create') }}?parent_id=" + $(this).val();
            })

            $('#dataForm').submit(function () {
                $('#btnSave').hide();
                $('#btnLoading').show();
            });            
        });

    </script>
@stop
