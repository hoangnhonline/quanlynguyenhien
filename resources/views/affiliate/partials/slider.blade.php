<!--SLIDER SECTION-->
@php
    $sliders = [
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
        'images/affiliate/slider_1.png',
    ];
@endphp
<section>
    <div class="main-slider">
        @foreach($sliders as $slide)
            <img src="{{$slide}}"/>
        @endforeach
    </div>
    <div class="container">
        <div class="main-slider-nav">
            @foreach($sliders as $slide)
                <img src="{{$slide}}"/>
            @endforeach
        </div>
    </div>
</section>
<!-- .END SLIDER SECTION-->
