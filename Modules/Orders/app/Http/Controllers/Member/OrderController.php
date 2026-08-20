<?php

namespace Modules\Orders\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Listings\Models\Listing;
use Modules\Orders\Enums\OrderStatus;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderSetting;

class OrderController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $query = Order::where('listing_id', $listing->id)
            ->with(['items', 'deliveryAddress'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        $dataTable = [
            'data' => $orders->items(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            'from' => $orders->firstItem(),
            'to' => $orders->lastItem(),
        ];

        $statuses = OrderStatus::cases();

        $setting = OrderSetting::getForBusiness($listing->id);

        return inertia('Member/Orders/Index', [
            'listing' => $listing,
            'dataTable' => $dataTable,
            'statuses' => array_map(fn($s) => ['value' => $s->value, 'label' => $s->label()], $statuses),
            'filters' => $request->only(['status', 'search']),
            'setting' => $setting,
        ]);
    }

    public function show(Listing $listing, Order $order)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($order->listing_id === $listing->id, 403);

        $order->load(['items', 'deliveryAddress', 'pickupLocation']);

        return inertia('Member/Orders/Show', [
            'listing' => $listing,
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Listing $listing, Order $order)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($order->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_column(OrderStatus::cases(), 'value'))],
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

    public function settings(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $setting = OrderSetting::firstOrCreate(
            ['listing_id' => $listing->id],
            [
                'order_type' => 'both',
                'delivery_radius_km' => 10,
                'delivery_fee_base' => 30,
                'delivery_fee_per_km' => 3,
                'free_delivery_threshold' => null,
                'min_order_amount' => 0,
                'whatsapp_number' => null,
                'is_active' => true,
            ]
        );

        return inertia('Member/Orders/Settings', [
            'listing' => $listing,
            'setting' => $setting,
        ]);
    }

    public function updateSettings(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'order_type' => 'required|in:delivery,pickup,both',
            'delivery_radius_km' => 'nullable|numeric|min:1|max:100',
            'delivery_fee_base' => 'nullable|numeric|min:0',
            'delivery_fee_per_km' => 'nullable|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'whatsapp_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $setting = OrderSetting::updateOrCreate(
            ['listing_id' => $listing->id],
            $validated
        );

        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }

    public function bulkDelete(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);

        Order::where('listing_id', $listing->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return redirect()->back()->with('success', 'Pedidos eliminados correctamente.');
    }
}
