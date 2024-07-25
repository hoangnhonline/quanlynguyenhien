<option value="">--Loại phòng--</option>
@foreach($rooms as $room)
<option value="{{ $room->id }}">{{ $room->name }}</option>
@endforeach