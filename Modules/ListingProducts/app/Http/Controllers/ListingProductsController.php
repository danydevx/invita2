<?php

namespace Modules\ListingProducts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingProductsController extends Controller
{
    public function index()
    {
        return view('listingproducts::index');
    }

    public function create()
    {
        return view('listingproducts::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('listingproducts::show');
    }

    public function edit($id)
    {
        return view('listingproducts::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
