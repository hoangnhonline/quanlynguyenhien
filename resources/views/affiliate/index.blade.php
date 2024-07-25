@extends('affiliate_layout')
@section('content')
    @include('affiliate.partials.header')
    @include('affiliate.partials.slider')
@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.main-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                dots: true,
                fade: true,
                asNavFor: '.main-slider-nav'
            });
            $('.main-slider-nav').slick({
                infinite: false,
                slidesToShow: 8,
                slidesToScroll: 1,
                asNavFor: '.main-slider',
                focusOnSelect: true
            });
        });
    </script>
@stop
