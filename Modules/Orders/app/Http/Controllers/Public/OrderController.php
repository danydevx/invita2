<?php

namespace Modules\Orders\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Listings\Models\Listing;
use Modules\Orders\Enums\OrderType;
use Modules\Orders\Enums\ProductType;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderDeliveryAddress;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Models\OrderPickupLocation;
use Modules\Orders\Models\OrderSetting;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:businesses,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:delivery,pickup',
            'items' => 'required|array|min:1',
            'items.*.product_type' => 'required|in:menu_product,business_product',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.title' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.options' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'delivery_address' => 'required_if:order_type,delivery',
            'delivery_address.full_address' => 'required_if:order_type,delivery|string',
            'delivery_address.references' => 'nullable|string',
            'delivery_address.latitude' => 'nullable|numeric',
            'delivery_address.longitude' => 'nullable|numeric',
            'delivery_address.distance_km' => 'nullable|numeric|min:0',
            'pickup_location_id' => 'required_if:order_type,pickup|nullable|exists:listing_locations,id',
            'pickup_time' => 'nullable|string',
        ]);

        $business = Listing::find($validated['listing_id']);

        $setting = OrderSetting::getForBusiness($business->id);
        if (!$setting || !$setting->is_active) {
            abort(403, 'El sistema de pedidos no está activo para este negocio.');
        }

        if ($validated['order_type'] === 'delivery' && $setting->order_type === 'pickup') {
            abort(403, 'Este negocio no ofrece servicio de delivery.');
        }

        if ($validated['order_type'] === 'pickup' && $setting->order_type === 'delivery') {
            abort(403, 'Este negocio no ofrece servicio de recolección.');
        }

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        if ($setting->min_order_amount && $subtotal < $setting->min_order_amount) {
            return response()->json([
                'error' => "El pedido mínimo es de {$setting->min_order_amount}",
            ], 422);
        }

        $deliveryFee = 0;
        if ($validated['order_type'] === 'delivery') {
            $distanceKm = $validated['delivery_address']['distance_km'] ?? 0;

            if ($setting->canDeliverTo($distanceKm)) {
                $deliveryFee = $setting->calculateDeliveryFee($distanceKm);
            } else {
                return response()->json([
                    'error' => 'Lo sentimos, no hacemos deliveries a tu zona.',
                ], 422);
            }
        }

        $order = Order::create([
            'listing_id' => $business->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'order_type' => $validated['order_type'],
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
            'distance_km' => $validated['delivery_address']['distance_km'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $itemData) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_type' => $itemData['product_type'],
                'product_id' => $itemData['product_id'],
                'variant_id' => $itemData['variant_id'] ?? null,
                'title' => $itemData['title'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'options' => $itemData['options'] ?? null,
                'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
            ]);
        }

        if ($validated['order_type'] === 'delivery' && isset($validated['delivery_address'])) {
            OrderDeliveryAddress::create([
                'order_id' => $order->id,
                'full_address' => $validated['delivery_address']['full_address'],
                'references' => $validated['delivery_address']['references'] ?? null,
                'latitude' => $validated['delivery_address']['latitude'] ?? null,
                'longitude' => $validated['delivery_address']['longitude'] ?? null,
                'distance_km' => $validated['delivery_address']['distance_km'] ?? null,
            ]);
        }

        if ($validated['order_type'] === 'pickup' && isset($validated['pickup_location_id'])) {
            $location = \Modules\ListingLocations\Models\ListingLocation::find($validated['pickup_location_id']);
            OrderPickupLocation::create([
                'order_id' => $order->id,
                'location_id' => $validated['pickup_location_id'],
                'location_name' => $location?->name,
                'location_address' => $location?->full_address,
                'pickup_time' => $validated['pickup_time'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => $order->load(['items', 'deliveryAddress']),
            'message' => 'Pedido creado correctamente.',
        ]);
    }
}
