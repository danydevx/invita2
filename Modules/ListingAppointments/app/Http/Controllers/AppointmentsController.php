<?php

namespace Modules\ListingAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    public function index()
    {
        return view('listingappointments::index');
    }

    public function create()
    {
        return view('listingappointments::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('listingappointments::show');
    }

    public function edit($id)
    {
        return view('listingappointments::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
