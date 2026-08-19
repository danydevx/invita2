@extends('layouts.directory')

@section('content')
    @include('sections.hero')

    @include('sections.categories', ['businessTypes' => $businessTypes])

    @include('sections.featured-businesses', ['businesses' => $featuredBusinesses])

    @include('sections.recent-businesses', ['businesses' => $recentBusinesses])

    @include('sections.cta')
@endsection
