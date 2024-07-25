@php
    $bookings = \App\Models\Booking::where('tour_id', 1)->where('tour_type', 1)->whereIn('status', [1,2])->where('use_date', request()->query('date', date('Y-m-d')))->get();
    $data = [];
    $classes = ['blue', 'red'];
    foreach ($bookings as $booking){
        if($booking->location){
            $data[] = [
                'lat' => $booking->location->latitude,
                'lng' => $booking->location->longitude,
                'name' => $booking->name,
                'class' => $classes[array_rand($classes)],
                'code' => 'PTT'.$booking->id,
                'adults' => $booking->adults,
                'childs' => $booking->childs,
                'infants' => $booking->infants,
                'address' => $booking->location->name,
            ];
        }
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bản đồ booking</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.js'></script>
    <link href='https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.css' rel='stylesheet'/>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Styles -->
    

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        @media screen and (max-width: 767px) {
            #map{
                width: 100%;
                height: calc(100vh - 48px) !important;
            }

            #main{
                flex-direction: column !important;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="container" style="padding-top: 20px;">
        <div style="padding: 10px;">
            <form action="{{ route('booking.maps') }}" class="form-inline">
                <div class="form-group">
                    <label>Ngày</label>
                    <input class="form-control" type="date" name="date" value="{{date('Y-m-d')}}"/>
                </div>
                <button class="form-control btn-info">Chọn</button>
            </form>
        </div>
        <div class="clearfix"></div>

    <div id="main">
    @if (Route::has('login'))
        <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            @auth
                <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                @endif
            @endauth
        </div>
    @endif


    <div id='map' style='width: 100%; height: 100vh;'></div>
    <script>
        goongjs.accessToken = 'ECtkx4dsZc4RnkZfLC0zmZurYFB3NthMNmbFTrcl';
        var map = new goongjs.Map({
            container: 'map',
            style: 'https://tiles.goong.io/assets/goong_map_web.json', // stylesheet location
            center: [103.966850, 10.200809], // starting position [lng, lat]
            zoom: 12, // starting zoom
            maxZoom: 12,
            minZoom: 10
        });

        var bookings = @json($data);
        var markers = [];
        var bounds = new goongjs.LngLatBounds();
        var places = {
            'type': 'FeatureCollection',
            'features': bookings.map(function (booking) {
                console.log(booking);
                return {
                    'type': 'Feature',
                    'properties': {
                        'description': '<span style="color:red; font-weight:bold">' + booking.code + '</span><br><span style="color:#D2691E; font-weight:bold; text-transform:uppercase">' + booking.address
                        + '</span><br><span style="font-size:15px">' + booking.adults + 'NL, ' + booking.childs + 'TE, ' + booking.infants + 'EB</span>'
                        ,
                        'icon': booking.class == 'blue' ? 'custom-marker' : 'custom-marker-red'
                    },
                    'geometry': {
                        'type': 'Point',
                        'coordinates': [booking.lng, booking.lat]
                    }
                }
            })
        };
        map.on('load', function () {
            map.loadImage(
                'http://maps.google.com/mapfiles/ms/icons/blue.png',
                function (error, image) {
                    if (error) throw error;
                    map.addImage('custom-marker', image);

                    map.loadImage('http://maps.google.com/mapfiles/ms/icons/red.png', function (err, image) {
                        map.addImage('custom-marker-red', image);
                    })

                    // Add a GeoJSON source containing place coordinates and information.
                    map.addSource('places', {
                        'type': 'geojson',
                        'data': places
                    });

                    map.addLayer({ 
                        'id': 'places',
                        'type': 'symbol',
                        'source': 'places',
                        'layout': {
                            'icon-image': '{icon}',
                            'icon-allow-overlap': true
                        }
                    });
                    bookings.forEach(function (booking) {
                        if(booking && booking.lat && booking.lng){
                            // var marker = new goongjs.Marker()
                            //     .setLngLat([booking.lng, booking.lat])
                            //     .addTo(map);
                            bounds.extend(new goongjs.LngLat(booking.lng, booking.lat));

                            // var popup = new goongjs.Popup({ offset: 25 }).setText(
                            //     'The President Ho Chi Minh Mausoleum is a mausoleum which serves as the resting place of Vietnamese Revolutionary leader & President Ho Chi Minh in Hanoi, Vietnam'
                            // );
                            // marker.setPopup(popup).addTo(map);
                        }
                    })
                    map.fitBounds(bounds, 60);

                    // When a click event occurs on a feature in the places layer, open a popup at the
                    // location of the feature, with description HTML from its properties.
                    map.on('click', 'places', function (e) {
                        var coordinates = e.features[0].geometry.coordinates.slice();
                        var description = e.features[0].properties.description;

                        map.flyTo({
                            center: e.features[0].geometry.coordinates
                        });
                        // Ensure that if the map is zoomed out such that multiple
                        // copies of the feature are visible, the popup appears
                        // over the copy being pointed to.
                        while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                            coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                        }

                        new goongjs.Popup()
                            .setLngLat(coordinates)
                            .setHTML(description)
                            .addTo(map);
                    });


                    // Change the cursor to a pointer when the mouse is over the places layer.
                    map.on('mouseenter', 'places', function () {
                        map.getCanvas().style.cursor = 'pointer';
                    });

                    // Change it back to a pointer when it leaves.
                    map.on('mouseleave', 'places', function () {
                        map.getCanvas().style.cursor = '';
                    });
                })
        });
    </script>

    
</div>
    </div>

</body>
</html>
