<div id="comment" style="margin-top: 20px">
    @if(session('comment_success'))
        <div class="alert alert-success">
            {{session('comment_success')}}
        </div>

        <script>
            setTimeout(function(){
                $('html, body').animate({scrollTop: $('#comment').offset().top}, 500);
            }, 500);
        </script>
    @endif
    @if(count($detail->comments) > 0)
        @foreach($detail->comments as $comment)
            @if($comment->notes)
                <p><strong style="color: #06b7a4">{{$comment->user_name}}:</strong> <span style="color: #0bb2d4">{!! preg_replace('/^<br>/', '', $comment->notes) !!}</span> <span style="color: #777">({{$comment->created_at->format('d/m H:i')}})</span></p>
            @endif
        @endforeach
    @endif
    <form method="post" action="{{route('booking.create-note')}}">
        <input type="hidden" name="booking_id" value="{{$detail->id}}"/>
        @csrf
        <div class="form-group">
            <textarea class="form-control col-md-8" name="comments" placeholder="Nhập ghi chú mới" required></textarea>
            <button class="btn btn-primary mt-2" type="submit" style="margin-top: 5px">Gửi ghi chú</button>
        </div>
    </form>
</div>
