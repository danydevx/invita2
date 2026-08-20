<?php

namespace Modules\Locations\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Locations\Models\Country;
use Modules\Locations\Models\State;
use Modules\Locations\Models\Municipality;

class LocationController extends Controller
{
    public function index()
    {
        $stats = [
            'countries' => Country::count(),
            'states' => State::count(),
            'municipalities' => Municipality::count(),
        ];

        return Inertia::render('Admin/Locations/Index', [
            'stats' => $stats,
        ]);
    }

    public function countriesIndex()
    {
        $countries = Country::orderBy('name')->get();

        return Inertia::render('Admin/Locations/Countries/Index', [
            'countries' => $countries,
        ]);
    }

    public function countriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:countries,code',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
        ]);

        Country::create($validated);

        return redirect()->back()->with('success', 'País creado exitosamente.');
    }

    public function countriesUpdate(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:countries,code,' . $country->id,
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country->update($validated);

        return redirect()->back()->with('success', 'País actualizado exitosamente.');
    }

    public function statesIndex(Request $request)
    {
        $query = State::with('country')->withCount('municipalities');

        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        $states = $query->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return Inertia::render('Admin/Locations/States/Index', [
            'states' => $states,
            'countries' => $countries,
            'selectedCountryId' => $request->country_id,
        ]);
    }

    public function statesStore(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'code' => 'required|string|max:10|unique:states,code,NULL,id,country_id,' . $request->country_id,
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        State::create($validated);

        return redirect()->back()->with('success', 'Estado creado exitosamente.');
    }

    public function statesUpdate(Request $request, State $state)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'code' => 'required|string|max:10|unique:states,code,' . $state->id . ',id,country_id,' . $request->country_id,
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $state->update($validated);

        return redirect()->back()->with('success', 'Estado actualizado exitosamente.');
    }

    public function municipalitiesIndex(Request $request)
    {
        $query = Municipality::with('state.country');

        if ($request->has('state_id') && $request->state_id) {
            $query->where('state_id', $request->state_id);
        } elseif ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        $municipalities = $query->orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();

        return Inertia::render('Admin/Locations/Municipalities/Index', [
            'municipalities' => $municipalities,
            'countries' => $countries,
            'states' => $states,
            'selectedCountryId' => $request->country_id,
            'selectedStateId' => $request->state_id,
        ]);
    }

    public function municipalitiesStore(Request $request)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'code' => 'required|string|max:20|unique:municipalities,code,NULL,id,state_id,' . $request->state_id,
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_metropolitan' => 'boolean',
        ]);

        Municipality::create($validated);

        return redirect()->back()->with('success', 'Municipio creado exitosamente.');
    }

    public function municipalitiesUpdate(Request $request, Municipality $municipality)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'code' => 'required|string|max:20|unique:municipalities,code,' . $municipality->id . ',id,state_id,' . $request->state_id,
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_metropolitan' => 'boolean',
        ]);

        $municipality->update($validated);

        return redirect()->back()->with('success', 'Municipio actualizado exitosamente.');
    }

    public function getCountries(): JsonResponse
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'currency', 'currency_symbol']);

        return response()->json(['countries' => $countries]);
    }

    public function getStates(?string $countryCode = null): JsonResponse
    {
        $query = State::with('country')->where('is_active', true);

        if ($countryCode) {
            $country = Country::where('code', $countryCode)->first();
            if (!$country) {
                return response()->json(['error' => 'Country not found'], 404);
            }
            $query->where('country_id', $country->id);
        }

        $states = $query->orderBy('name')->get(['id', 'code', 'name', 'lat', 'lng']);

        return response()->json(['states' => $states]);
    }

    public function getMunicipalities(string $stateCode): JsonResponse
    {
        $state = State::where('code', $stateCode)->first();

        if (!$state) {
            return response()->json(['error' => 'State not found'], 404);
        }

        $municipalities = Municipality::where('state_id', $state->id)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'is_metropolitan', 'lat', 'lng']);

        return response()->json(['municipalities' => $municipalities]);
    }
}
