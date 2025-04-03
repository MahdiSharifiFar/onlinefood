@extends('.layouts.master')

@section('pageTitle' , 'فروشگاه آنلاین غذا')

@section('headLink')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
          integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
            integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
            crossorigin=""></script>

@endsection

@section('content')

    <!-- Features section -->
    @include('home.features')

    @include(('home.products'))

    @include('home.about')
    @include('home.contact')

@endsection

@section('mapScript')

    <script>
        var map = L.map('map').setView([27.439902, 57.190527], 14);
        var tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);
        var marker = L.marker([27.439902, 57.190527]).addTo(map)
            .bindPopup('<b>آنلاین فود</b>').openPopup();
    </script>

@endsection
