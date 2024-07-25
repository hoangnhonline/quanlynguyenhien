<div class="clearfix"></div>
<div id="div-image" style="margin-top:10px">
    @if( $detail->images->count() > 0 )
    <?php $i = 0; ?>
    <div class="row">
        @foreach( $detail->images as $k => $hinh)
            <?php $i++; ?>
            <div class="col-md-3">
                <div class="img-select">    
	                <img class="img-thumbnail lazy" data-original="{{ Helper::showImage($hinh->image_url) }}" style="width:100%">
	                <div class="checkbox">
	                    <label>
	                    	<input type="radio" name="thumbnail_img" class="thumb" value="{{ $hinh->image_url }}" {{ $detail->thumbnail_id == $hinh->id ? "checked" : "" }}> Ảnh đại diện 
	                    </label>
	                    <input type="text" name="image_tmp_url[]"  value="{{$hinh->image_url}}" hidden>
	                    <button class="btn btn-danger btn-sm remove-image" type="button" data-value="{{  $hinh->image_url }}" data-id="{{ $hinh->id }}">Xóa</button>
	                </div>
                	<input type="hidden" name="image_id[]" value="{{ $hinh->id }}">
            	</div><!--img-select-->
            </div><!--col-md-3-->
            @if($i%4 == 0)
            </div><!--row--><div class="row">
                @endif
        @endforeach
        </div><!--row-->
    @endif
	
</div>