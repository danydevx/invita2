<?php

namespace Modules\ListingLocations\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingLocationsController extends Controller
{
    public function index()
    {
        return view('listinglocations::index');
    }

    public function create()
    {
        return view('listinglocations::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('listinglocations::show');
    }

    public function edit($id)
    {
        return view('listinglocations::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
