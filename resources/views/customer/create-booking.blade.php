<div class="modal-body text-center">
    <p>Vui lòng chọn loại booking:</p>
    <a href="{{ route('booking.create', ['customer_id' => $id]) }}" target="_blank" class="btn btn-lg btn-primary btn-block">TOUR</a>
    <a href="{{ route('booking-hotel.create', ['customer_id' => $id]) }}" target="_blank" class="btn btn-lg btn-primary btn-block">KHÁCH SẠN</a>
    <a href="{{ route('booking-ticket.create', ['customer_id' => $id]) }}" target="_blank" class="btn btn-lg btn-primary btn-block">VÉ VUI CHƠI</a>
    <a href="{{ route('booking-car.create', ['customer_id' => $id]) }}" target="_blank" class="btn btn-lg btn-primary btn-block">XE</a>
</div>
