<form method="POST" action="{{ route('customer.save-hen') }}" id="henForm">
    {{ csrf_field() }}
    <input type="hidden" name="customer_id" value="{{ $detail->id }}">
    <div class="modal-body text-left" id="henContent">
        @for($i = 0 ; $i <10; $i++)
            @php
                if(isset($appointments[$i])){
                    $schedule = $appointments[$i];
                    $id = $schedule['id'];
                    $schedule_date = date('d/m/Y', strtotime($schedule['datetime']));
                    $schedule_hour = date('H', strtotime($schedule['datetime']));
                    $schedule_minute = date('i', strtotime($schedule['datetime']));
                    $schedule_notes = $schedule['notes'];
                }else{
                    $schedule_date = '';
                    $schedule_hour = '';
                    $schedule_minute = '';
                    $schedule_notes = '';
                    $id = '';
                }
            @endphp
            <fieldset class="scheduler-border-2 {{$i >= 2 && $i >= count($appointments) ? 'dia-diem-hidden' : ''}}" >
                <legend class="scheduler-border-2">Hẹn lần {{$i + 1}}</legend>
                <div class="row">
                    <div class="form-group col-xs-12">
                        <label for="facebook">Ngày giờ</label>
                        <br>
                        <input type="hidden" name="id[]" value="{{$id}}">
                        <input type="text" class="form-control-2 datepicker" name="schedule_date[]"
                               value="{{ $schedule_date }}" style="width: 120px" autocomplete="off">

                        <select class="form-control-2 select2" name="schedule_hour[]" style="width: 90px">
                            <option value="">Giờ</option>
                            @for($g = 7; $g < 23; $g++)
                                <option
                                    value="{{ str_pad($g,2,"0", STR_PAD_LEFT) }}" {{ $g == $schedule_hour ? "selected" : "" }}>{{ str_pad($g,2,"0", STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select class="form-control-2 select2" name="schedule_minute[]" style="width: 90px">
                            <option value="">Phút</option>
                            <option value="00" {{ 0 == $schedule_minute ? "selected" : "" }}>00</option>
                            <option value="15" {{ 15 == $schedule_minute ? "selected" : "" }}>15</option>
                            <option value="30" {{ 30 == $schedule_minute ? "selected" : "" }}>30</option>
                            <option value="45" {{ 45 == $schedule_minute ? "selected" : "" }}>45</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-12">
                        <label for="address">Ghi chú</label>
                        <textarea class="form-control ckeditor" rows="6" name="schedule_notes[]">{{ $schedule_notes }}</textarea>
                    </div>
                </div>
            </fieldset>
        @endfor
        <button type="button" class="btn btn-warning" id="btnAddAppointment">Thêm lịch hẹn</button>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-info" id="btnSaveHen">Lưu lịch hẹn</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        // $('#btnSaveHen').click(function(){
        //     $.ajax({
        //         url : "{{ route('customer.save-hen') }}",
        //         type : "POST",
        //         data : $('#henForm').serialize(),
        //         success : function(data){
        //             alert(data);
        //         }

        //     })
        // });

        $('#btnAddAppointment').click(function(e){
            e.preventDefault();
            $('.dia-diem-hidden:first').removeClass('dia-diem-hidden');
        })
    });
</script>
