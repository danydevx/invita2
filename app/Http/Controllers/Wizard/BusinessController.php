<?php

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Listings\Enums\ListingType;
use Modules\Listings\Models\Listing;

class BusinessController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->listings()->exists()) {
            return redirect()->route('member.dashboard');
        }

        $listingTypes = ListingType::cases();
        $userEmail = $user->email;
        $userName = $user->name;

        return view('wizard.business', [
            'title' => 'Configura tu negocio',
            'listingTypes' => $listingTypes,
            'userEmail' => $userEmail,
            'userName' => $userName,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->listings()->exists()) {
            return redirect()->route('member.dashboard');
        }

        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'],
            'listing_type' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $validTypes = array_column(ListingType::cases(), 'value');
        if (!in_array($data['listing_type'], $validTypes)) {
            return back()->withErrors([
                'listing_type' => 'El tipo de negocio no es válido.',
            ]);
        }

        $listing = Listing::create([
            'user_id' => $user->id,
            'name' => trim($data['business_name']),
            'listing_type' => $data['listing_type'],
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
            'is_published' => false,
        ]);

        return redirect()->route('member.dashboard')
            ->with('warning', 'Tu negocio está pendiente. Verifica tu email para activarlo.');
    }
}
