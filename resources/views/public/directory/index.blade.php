@extends('layouts.directory')

@section('content')
    @include('sections.search-hero', [
        'businessTypes' => $businessTypes,
        'filters' => $filters,
    ])

    @include('sections.results-layout', [
        'businesses' => $businesses,
        'mapMarkers' => $mapMarkers,
    ])
@endsection
