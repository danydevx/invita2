<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\Appointments\Enums\AppointmentStatus;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Listings\Models\Listing;
use Modules\Faqs\Models\BusinessFaq;
use Modules\Faqs\Models\BusinessFaqCategory;
use Modules\Gallery\Models\BusinessGallery;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Locations\Models\BusinessLocation;
use Modules\Products\Models\BusinessProduct;
use Modules\Services\Models\BusinessService;

class BusinessContentController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;

    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function locationsIndex(Request $request, Listing $business)
    {
        $locations = $business->locations()
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/LocationsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'locations' => $locations,
        ]);
    }

    public function locationsCreate(Request $request, Listing $business)
    {
        return Inertia::render('Admin/BusinessContent/LocationsCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function locationsStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $location = $business->locations()->create($data);

        $activity->log('admin_location_created', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Admin: Ubicacion creada para negocio '.$business->name,
            'request' => $request,
        ]);

        return redirect()->route('admin.business.locations.index', $business->id)
            ->with('success', 'Ubicacion creada correctamente.');
    }

    public function locationsEdit(Request $request, Listing $business, BusinessLocation $location)
    {
        return Inertia::render('Admin/BusinessContent/LocationsEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'address_line_1' => $location->address_line_1,
                'address_line_2' => $location->address_line_2,
                'city' => $location->city,
                'state' => $location->state,
                'postal_code' => $location->postal_code,
                'country' => $location->country,
                'phone' => $location->phone,
                'email' => $location->email,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'directions_url' => $location->directions_url,
                'is_primary' => $location->is_primary,
                'is_active' => $location->is_active,
            ],
        ]);
    }

    public function locationsUpdate(Request $request, Listing $business, BusinessLocation $location, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $location->update($data);

        $activity->log('admin_location_updated', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Admin: Ubicacion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.locations.index', $business->id)
            ->with('success', 'Ubicacion actualizada correctamente.');
    }

    public function locationsDestroy(Request $request, Listing $business, BusinessLocation $location, ActivityService $activity)
    {
        $activity->log('admin_location_deleted', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Admin: Ubicacion eliminada',
        ]);

        $location->delete();

        return redirect()->route('admin.business.locations.index', $business->id)
            ->with('success', 'Ubicacion eliminada correctamente.');
    }

    public function servicesIndex(Request $request, Listing $business)
    {
        $services = $business->services()
            ->with('location')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/ServicesIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'services' => $services,
        ]);
    }

    public function servicesCreate(Request $request, Listing $business)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/ServicesCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function servicesStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        $service = $business->services()->create($data);

        $activity->log('admin_service_created', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Admin: Servicio creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.services.index', $business->id)
            ->with('success', 'Servicio creado correctamente.');
    }

    public function servicesEdit(Request $request, Listing $business, BusinessService $service)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/ServicesEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'image' => $service->image,
                'duration_minutes' => $service->duration_minutes,
                'price' => $service->price,
                'deposit_required' => $service->deposit_required,
                'deposit_amount' => $service->deposit_amount,
                'allows_online_booking' => $service->allows_online_booking,
                'whatsapp_contact' => $service->whatsapp_contact,
                'is_active' => $service->is_active,
                'sort_order' => $service->sort_order,
                'business_location_id' => $service->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function servicesUpdate(Request $request, Listing $business, BusinessService $service, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $service->update($data);

        $activity->log('admin_service_updated', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Admin: Servicio actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.services.index', $business->id)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function servicesDestroy(Request $request, Listing $business, BusinessService $service, ActivityService $activity)
    {
        $activity->log('admin_service_deleted', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Admin: Servicio eliminado',
        ]);

        $service->delete();

        return redirect()->route('admin.business.services.index', $business->id)
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function faqsIndex(Request $request, Listing $business)
    {
        $faqs = $business->faqs()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('question')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/FaqsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'faqs' => $faqs,
        ]);
    }

    public function faqsCreate(Request $request, Listing $business)
    {
        $categories = BusinessFaqCategory::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/FaqsCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'categories' => $categories,
        ]);
    }

    public function faqsStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:business_faq_categories,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;

        $faq = BusinessFaq::create($data);

        $activity->log('admin_faq_created', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Admin: Pregunta frecuente creada',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente creada correctamente.');
    }

    public function faqsEdit(Request $request, Listing $business, BusinessFaq $faq)
    {
        $categories = BusinessFaqCategory::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/FaqsEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'faq' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'category_id' => $faq->category_id,
                'is_active' => $faq->is_active,
                'sort_order' => $faq->sort_order,
            ],
            'categories' => $categories,
        ]);
    }

    public function faqsUpdate(Request $request, Listing $business, BusinessFaq $faq, ActivityService $activity)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:business_faq_categories,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $faq->update($data);

        $activity->log('admin_faq_updated', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Admin: Pregunta frecuente actualizada',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente actualizada correctamente.');
    }

    public function faqsDestroy(Request $request, Listing $business, BusinessFaq $faq, ActivityService $activity)
    {
        $activity->log('admin_faq_deleted', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Admin: Pregunta frecuente eliminada',
        ]);

        $faq->delete();

        return redirect()->route('admin.business.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente eliminada correctamente.');
    }

    public function faqCategoriesIndex(Request $request, Listing $business)
    {
        $categories = BusinessFaqCategory::where('listing_id', $business->id)
            ->with('faqs')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/BusinessContent/FaqCategoriesIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'categories' => $categories,
        ]);
    }

    public function faqCategoriesStore(Request $request, Listing $business)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = BusinessFaqCategory::generateUniqueSlug($business->id, $data['name']);

        BusinessFaqCategory::create($data);

        return redirect()->back()
            ->with('success', 'Categoria creada correctamente.');
    }

    public function faqCategoriesUpdate(Request $request, Listing $business, BusinessFaqCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update($data);

        return redirect()->back()
            ->with('success', 'Categoria actualizada correctamente.');
    }

    public function faqCategoriesDestroy(Request $request, Listing $business, BusinessFaqCategory $category)
    {
        $category->faqs()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()
            ->with('success', 'Categoria eliminada. Las preguntas fueron desvinculadas.');
    }

    public function productsIndex(Request $request, Listing $business)
    {
        $products = $business->products()
            ->with('location')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/ProductsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'products' => $products,
        ]);
    }

    public function productsCreate(Request $request, Listing $business)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/ProductsCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
        ]);
    }

    public function productsStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        $product = $business->products()->create($data);

        $activity->log('admin_product_created', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Admin: Producto creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.products.index', $business->id)
            ->with('success', 'Producto creado correctamente.');
    }

    public function productsEdit(Request $request, Listing $business, BusinessProduct $product)
    {
        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/ProductsEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'quantity' => $product->quantity,
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
                'whatsapp_contact' => $product->whatsapp_contact,
                'sort_order' => $product->sort_order,
                'business_location_id' => $product->business_location_id,
            ],
            'locations' => $locations,
        ]);
    }

    public function productsUpdate(Request $request, Listing $business, BusinessProduct $product, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $product->update($data);

        $activity->log('admin_product_updated', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Admin: Producto actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.products.index', $business->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function productsDestroy(Request $request, Listing $business, BusinessProduct $product, ActivityService $activity)
    {
        $activity->log('admin_product_deleted', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Admin: Producto eliminado',
        ]);

        $product->delete();

        return redirect()->route('admin.business.products.index', $business->id)
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function galleryIndex(Request $request, Listing $business, ?BusinessGallery $gallery = null)
    {
        $galleries = $business->galleries()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'is_primary', 'is_active']);

        if (! $gallery) {
            $gallery = BusinessGallery::primaryFor($business->id);
        }

        if (! $gallery || $gallery->listing_id !== $business->id) {
            $gallery = $galleries->firstWhere('is_primary', true) ? BusinessGallery::find($galleries->firstWhere('is_primary', true)['id']) : null;
        }

        $images = $gallery
            ? $business->galleryImages()->where('business_gallery_id', $gallery->id)
                ->with('location')
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->paginate(20)
            : collect();

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/GalleryIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'images' => $images,
            'galleries' => $galleries,
            'currentGalleryId' => $gallery?->id,
            'locations' => $locations,
            'maxSizeKb' => self::MAX_FILE_SIZE_KB,
        ]);
    }

    public function galleryStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:'.self::MAX_FILE_SIZE_KB, 'mimetypes:'.implode(',', self::ALLOWED_MIME_TYPES)],
            'business_gallery_id' => [
                'required',
                Rule::exists('business_galleries', 'id')->where('listing_id', $business->id),
            ],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ], [
            'file.max' => 'El archivo supera el tamaño máximo de 5MB.',
            'file.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
        ]);

        $file = $request->file('file');
        $disk = 'public';
        $path = $file->store('gallery/'.$business->id, ['disk' => $disk]);

        $image = $business->galleryImages()->create([
            'listing_id' => $business->id,
            'business_gallery_id' => $data['business_gallery_id'],
            'path' => Storage::disk($disk)->url($path),
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'business_location_id' => $request->input('business_location_id'),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $activity->log('gallery_image_uploaded', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen subida a galeria (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.gallery.index', $business->id)->with('success', 'Imagen subida correctamente.');
    }

    public function galleryUpdate(Request $request, Listing $business, BusinessGalleryImage $image, ActivityService $activity)
    {
        $data = $request->validate([
            'business_gallery_id' => [
                'required',
                Rule::exists('business_galleries', 'id')->where('listing_id', $business->id),
            ],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
        ]);

        $image->update($data);

        $activity->log('gallery_image_updated', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria actualizada (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.gallery.index', $business->id)->with('success', 'Imagen actualizada correctamente.');
    }

    public function galleryDestroy(Request $request, Listing $business, BusinessGalleryImage $image, ActivityService $activity)
    {
        if ($image->path) {
            $path = str_replace(url('/').'/storage/', '', $image->path);
            Storage::disk('public')->delete($path);
        }

        $activity->log('gallery_image_deleted', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria eliminada (admin)',
        ]);

        $image->delete();

        return redirect()->route('admin.business.gallery.index', $business->id)->with('success', 'Imagen eliminada correctamente.');
    }

    public function galleriesIndex(Request $request, Listing $business)
    {
        $galleries = $business->galleries()
            ->withCount('images')
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (BusinessGallery $gallery) => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'is_primary' => (bool) $gallery->is_primary,
                'is_active' => (bool) $gallery->is_active,
                'sort_order' => (int) $gallery->sort_order,
                'images_count' => (int) $gallery->images_count,
            ]);

        return Inertia::render('Admin/BusinessContent/GalleriesIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'galleries' => $galleries,
        ]);
    }

    public function galleriesCreate(Request $request, Listing $business)
    {
        return Inertia::render('Admin/BusinessContent/GalleriesCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function galleriesStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $gallery = DB::transaction(function () use ($business, $data) {
            if (! empty($data['is_primary'])) {
                BusinessGallery::where('listing_id', $business->id)->update(['is_primary' => false]);
            }

            return $business->galleries()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_primary' => (bool) ($data['is_primary'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);
        });

        $activity->log('gallery_created', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería creada (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.galleries.index', $business->id)->with('success', 'Galería creada correctamente.');
    }

    public function galleriesEdit(Request $request, Listing $business, BusinessGallery $gallery)
    {
        abort_unless($gallery->listing_id === $business->id, 404);

        return Inertia::render('Admin/BusinessContent/GalleriesEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'is_primary' => (bool) $gallery->is_primary,
                'is_active' => (bool) $gallery->is_active,
                'sort_order' => (int) $gallery->sort_order,
            ],
        ]);
    }

    public function galleriesUpdate(Request $request, Listing $business, BusinessGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($business, $gallery, $data) {
            if (! empty($data['is_primary'])) {
                BusinessGallery::where('listing_id', $business->id)
                    ->where('id', '!=', $gallery->id)
                    ->update(['is_primary' => false]);
            }

            $gallery->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_primary' => (bool) ($data['is_primary'] ?? $gallery->is_primary),
                'is_active' => (bool) ($data['is_active'] ?? $gallery->is_active),
                'sort_order' => (int) ($data['sort_order'] ?? $gallery->sort_order),
            ]);
        });

        $activity->log('gallery_updated', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería actualizada (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.galleries.index', $business->id)->with('success', 'Galería actualizada correctamente.');
    }

    public function galleriesDestroy(Request $request, Listing $business, BusinessGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);

        $activity->log('gallery_deleted', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería eliminada (admin)',
        ]);

        $gallery->delete();

        return redirect()->route('admin.business.galleries.index', $business->id)->with('success', 'Galería eliminada correctamente.');
    }

    public function galleriesSetPrimary(Request $request, Listing $business, BusinessGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);

        DB::transaction(function () use ($business, $gallery) {
            BusinessGallery::where('listing_id', $business->id)
                ->where('id', '!=', $gallery->id)
                ->update(['is_primary' => false]);

            $gallery->update([
                'is_primary' => true,
                'is_active' => true,
            ]);
        });

        $activity->log('gallery_set_primary', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería marcada como principal (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.galleries.index', $business->id)->with('success', 'Galería marcada como principal.');
    }

    public function appointmentsIndex(Request $request, Listing $business)
    {
        $appointments = $business->appointments()
            ->with(['location', 'service'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(20);

        return Inertia::render('Admin/BusinessContent/AppointmentsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'appointments' => $appointments,
        ]);
    }

    public function appointmentsCreate(Request $request, Listing $business)
    {
        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1']);

        return Inertia::render('Admin/BusinessContent/AppointmentsCreate', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function appointmentsStore(Request $request, Listing $business, ActivityService $activity)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['required', 'exists:business_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = BusinessService::findOrFail($data['business_service_id']);
        $endTime = date('H:i', strtotime($data['start_time'].' + '.$service->duration_minutes.' minutes'));

        $appointment = $business->appointments()->create([
            'listing_id' => $business->id,
            'business_location_id' => $data['business_location_id'],
            'business_service_id' => $data['business_service_id'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'status' => AppointmentStatus::PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        $activity->log('appointment_created', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita creada (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.appointments.index', $business->id)
            ->with('success', 'Cita creada correctamente.');
    }

    public function appointmentsShow(Request $request, Listing $business, BusinessAppointment $appointment)
    {
        $appointment->load(['location', 'service']);

        return Inertia::render('Admin/BusinessContent/AppointmentsShow', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status->value,
                'status_label' => $appointment->status->label(),
                'notes' => $appointment->notes,
                'location' => $appointment->location ? [
                    'id' => $appointment->location->id,
                    'name' => $appointment->location->name,
                ] : null,
                'service' => $appointment->service ? [
                    'id' => $appointment->service->id,
                    'name' => $appointment->service->name,
                ] : null,
            ],
        ]);
    }

    public function appointmentsEdit(Request $request, Listing $business, BusinessAppointment $appointment)
    {
        $appointment->load(['location', 'service']);

        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1']);

        return Inertia::render('Admin/BusinessContent/AppointmentsEdit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status->value,
                'notes' => $appointment->notes,
                'business_service_id' => $appointment->business_service_id,
                'business_location_id' => $appointment->business_location_id,
            ],
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function appointmentsUpdate(Request $request, Listing $business, BusinessAppointment $appointment, ActivityService $activity)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['required', 'exists:business_locations,id'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = BusinessService::findOrFail($data['business_service_id']);
        $endTime = date('H:i', strtotime($data['start_time'].' + '.$service->duration_minutes.' minutes'));

        $appointment->update([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'business_service_id' => $data['business_service_id'],
            'business_location_id' => $data['business_location_id'],
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'status' => AppointmentStatus::from($data['status']),
            'notes' => $data['notes'] ?? null,
            'cancelled_at' => $data['status'] === 'cancelled' ? now() : null,
        ]);

        $activity->log('appointment_updated', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita actualizada (admin)',
            'request' => $request,
        ]);

        return redirect()->route('admin.business.appointments.index', $business->id)
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function appointmentsDestroy(Request $request, Listing $business, BusinessAppointment $appointment, ActivityService $activity)
    {
        $activity->log('appointment_deleted', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita eliminada (admin)',
        ]);

        $appointment->delete();

        return redirect()->route('admin.business.appointments.index', $business->id)
            ->with('success', 'Cita eliminada correctamente.');
    }

    public function appointmentsCancel(Request $request, Listing $business, BusinessAppointment $appointment, ActivityService $activity)
    {
        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        $activity->log('appointment_cancelled', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita cancelada (admin)',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Cita cancelada correctamente.');
    }
}
