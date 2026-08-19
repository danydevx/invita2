@extends('layouts.directory')

@section('content')
    @include('sections.business-hero', ['business' => $business])

    @include('sections.business-info', ['business' => $business])

    @include('sections.business-services', ['services' => $services])

    @include('sections.business-gallery', ['images' => $galleryImages])

    @include('sections.business-location', [
        'business' => $business,
        'locations' => $locations,
        'mapLocations' => $mapLocations,
    ])

    @include('sections.business-reviews', [
        'reviews' => $reviews,
        'avgRating' => $avgRating,
        'business' => $business,
    ])

    @include('sections.cta')
@endsection
