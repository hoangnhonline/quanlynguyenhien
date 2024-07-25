@if( $planList->count() > 0)
	@foreach( $planList as $value )
	<option value="{{ $value->id }}" {{ $value->id == $id_selected ? "selected" : "" }}>{{ $value->name }}</option>
	@endforeach
@endif
