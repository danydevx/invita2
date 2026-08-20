<?php

namespace Modules\ListingModules\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingModulesController extends Controller
{
    public function index()
    {
        return view('listingmodules::index');
    }

    public function create()
    {
        return view('listingmodules::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('listingmodules::show');
    }

    public function edit($id)
    {
        return view('listingmodules::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
