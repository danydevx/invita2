<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\ContactForm\Models\BusinessContactForm;
use Modules\ContactForm\Models\BusinessContactFormField;
use Modules\Leads\Models\BusinessLead;

class ContactFormController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $forms = $business->contactForms()
            ->withCount('fields')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($form) => [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
                'shortcode' => $form->shortcode,
                'is_active' => $form->is_active,
                'fields_count' => $form->fields_count,
                'created_at' => $form->created_at->toDateTimeString(),
            ]);

        $maxForms = $this->getMaxFormsPerBusiness();

        return Inertia::render('Member/ContactForm/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'forms' => $forms,
            'maxForms' => $maxForms,
            'canCreateMore' => $forms->count() < $maxForms,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $maxForms = $this->getMaxFormsPerBusiness();
        $currentCount = $business->contactForms()->count();

        if ($currentCount >= $maxForms) {
            return redirect()->back()->with('error', "Has alcanzado el límite de {$maxForms} formularios.");
        }

        return Inertia::render('Member/ContactForm/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        $maxForms = $this->getMaxFormsPerBusiness();
        $currentCount = $business->contactForms()->count();

        if ($currentCount >= $maxForms) {
            return redirect()->back()->with('error', "Has alcanzado el límite de {$maxForms} formularios.");
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'success_message' => ['nullable', 'string', 'max:1000'],
            'show_phone' => ['boolean'],
            'show_email' => ['boolean'],
        ]);

        $form = $business->contactForms()->create($validated);

        if ($validated['is_active'] ?? false) {
            $business->contactForms()->where('id', '!=', $form->id)->update(['is_active' => false]);
        }

        return redirect()->route('member.business.contact-forms.edit', [$business->id, $form->id])
            ->with('success', 'Formulario creado correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $form->load('fields');

        $fields = $form->fields->map(fn ($field) => [
            'id' => $field->id,
            'name' => $field->field_name,
            'type' => $field->field_type,
            'label' => $field->label,
            'placeholder' => $field->placeholder,
            'is_required' => (bool) $field->is_required,
            'is_active' => (bool) $field->is_active,
            'options' => $field->options ?? [],
            'field_config' => $field->field_config ?? null,
            'order' => $field->order,
            'row' => $field->row ?? 1,
            'width' => $field->width ?? 'full',
        ]);

        return Inertia::render('Member/ContactForm/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
                'shortcode' => $form->shortcode,
                'is_active' => $form->is_active,
            ],
            'fields' => $fields,
        ]);
    }

    public function update(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'success_message' => ['nullable', 'string', 'max:1000'],
            'show_phone' => ['boolean'],
            'show_email' => ['boolean'],
        ]);

        $form->update($validated);

        if ($validated['is_active'] ?? false) {
            $business->contactForms()->where('id', '!=', $form->id)->update(['is_active' => false]);
        }

        return redirect()->back()->with('success', 'Formulario actualizado.');
    }

    public function destroy(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $form->fields()->delete();
        $form->delete();

        return redirect()->route('member.business.contact-forms.index', [$business->id])
            ->with('success', 'Formulario eliminado.');
    }

    public function preview(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $fields = $form->fields()
            ->where('is_active', true)
            ->orderBy('row')
            ->orderBy('order')
            ->get()
            ->map(fn ($field) => $field->getConfig())
            ->toArray();

        return Inertia::render('Member/ContactForm/Preview', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'shortcode' => $form->shortcode,
                'success_message' => $form->success_message,
                'show_phone' => $form->show_phone,
                'show_email' => $form->show_email,
            ],
            'fields' => $fields,
        ]);
    }

    public function storeField(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $fieldConfig = $request->validate([
            'type' => ['required', 'string'],
            'required' => ['boolean'],
            'label' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'className' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'subtype' => ['nullable', 'string'],
            'maxlength' => ['nullable', 'integer'],
            'value' => ['nullable', 'string', 'max:500'],
            'options' => ['nullable', 'array'],
            'access' => ['nullable'],
            'role' => ['nullable'],
            'row' => ['integer', 'min:1'],
            'width' => ['string', 'in:full,half,third,quarter'],
            'multiple' => ['boolean'],
            'default_value' => ['nullable'],
            'min_date_type' => ['string', 'in:none,now,date'],
            'min_date' => ['nullable', 'string'],
            'max_date_type' => ['string', 'in:none,now,date'],
            'max_date' => ['nullable', 'string'],
            'file_types' => ['nullable', 'array'],
            'file_types.*' => ['string', 'in:pdf,xlsx,docx,jpg,png,webp'],
            'max_file_size' => ['integer', 'min:1', 'max:20'],
        ]);

        $fieldType = $fieldConfig['type'];
        $fieldLabel = $fieldConfig['label'];
        $fieldName = $fieldConfig['name'];

        $maxOrder = $form->fields()->max('order') ?? 0;

        $field = $form->fields()->create([
            'business_id' => $business->id,
            'business_contact_form_id' => $form->id,
            'field_type' => $fieldType,
            'field_name' => $fieldName,
            'label' => $fieldLabel,
            'placeholder' => $fieldConfig['placeholder'] ?? null,
            'options' => isset($fieldConfig['options']) ? json_encode($fieldConfig['options']) : null,
            'is_required' => $fieldConfig['required'] ?? false,
            'is_active' => true,
            'order' => $maxOrder + 1,
            'row' => $fieldConfig['row'] ?? 1,
            'width' => $fieldConfig['width'] ?? 'full',
            'field_config' => $fieldConfig,
        ]);

        return redirect()->back()->with('success', 'Campo agregado.');
    }

    public function updateField(Request $request, Business $business, BusinessContactForm $form, BusinessContactFormField $field)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id || $field->business_contact_form_id !== $form->id) {
            abort(403);
        }

        $fieldConfig = $request->validate([
            'type' => ['required', 'string'],
            'required' => ['boolean'],
            'label' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'className' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'subtype' => ['nullable', 'string'],
            'maxlength' => ['nullable', 'integer'],
            'value' => ['nullable', 'string', 'max:500'],
            'options' => ['nullable', 'array'],
            'access' => ['nullable'],
            'role' => ['nullable'],
            'row' => ['integer', 'min:1'],
            'width' => ['string', 'in:full,half,third,quarter'],
            'multiple' => ['boolean'],
            'default_value' => ['nullable'],
            'min_date_type' => ['string', 'in:none,now,date'],
            'min_date' => ['nullable', 'string'],
            'max_date_type' => ['string', 'in:none,now,date'],
            'max_date' => ['nullable', 'string'],
            'file_types' => ['nullable', 'array'],
            'file_types.*' => ['string', 'in:pdf,xlsx,docx,jpg,png,webp'],
            'max_file_size' => ['integer', 'min:1', 'max:20'],
        ]);

        $field->update([
            'field_type' => $fieldConfig['type'],
            'field_name' => $fieldConfig['name'],
            'label' => $fieldConfig['label'],
            'placeholder' => $fieldConfig['placeholder'] ?? null,
            'options' => isset($fieldConfig['options']) ? json_encode($fieldConfig['options']) : null,
            'is_required' => $fieldConfig['required'] ?? false,
            'row' => $fieldConfig['row'] ?? 1,
            'width' => $fieldConfig['width'] ?? 'full',
            'field_config' => $fieldConfig,
        ]);

        return redirect()->back()->with('success', 'Campo actualizado.');
    }

    public function destroyField(Request $request, Business $business, BusinessContactForm $form, BusinessContactFormField $field)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id || $field->business_contact_form_id !== $form->id) {
            abort(403);
        }

        $field->delete();

        return redirect()->back()->with('success', 'Campo eliminado.');
    }

    public function submissions(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['name', 'email', 'status', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->leads()
            ->where('business_contact_form_id', $form->id)
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $submissions = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($submissions->items())->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'notes' => $lead->notes,
                    'status' => $lead->status->value,
                    'status_label' => $lead->status->label(),
                    'created_at' => $lead->created_at->toDateTimeString(),
                    'location' => $lead->location ? [
                        'id' => $lead->location->id,
                        'name' => $lead->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $submissions->currentPage(),
            'last_page' => $submissions->lastPage(),
            'per_page' => $submissions->perPage(),
            'total' => $submissions->total(),
            'from' => $submissions->firstItem(),
            'to' => $submissions->lastItem(),
        ];

        return Inertia::render('Member/ContactForm/Submissions', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
            ],
            'submissions' => $submissions,
            'dataTable' => $dataTable,
        ]);
    }

    public function export(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $leads = $business->leads()
            ->where('business_contact_form_id', $form->id)
            ->with('location')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="formulario_' . $form->id . '_' . date('Y-m-d') . '.csv"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Nombre', 'Email', 'Telefono', 'Notas', 'Estado', 'Ubicacion', 'Fecha']);

        foreach ($leads as $lead) {
            fputcsv($handle, [
                $lead->name,
                $lead->email,
                $lead->phone ?? '',
                $lead->notes ?? '',
                $lead->status->label() ?? '',
                $lead->location?->name ?? '',
                $lead->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    public function reorder(Request $request, Business $business, BusinessContactForm $form)
    {
        $this->authorize('viewAny', [BusinessLead::class, $business]);

        if ($form->business_id !== $business->id) {
            abort(403);
        }

        $validated = $request->validate([
            'fields' => ['required', 'array'],
            'fields.*.id' => ['required', 'integer'],
            'fields.*.order' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['fields'] as $fieldData) {
            $form->fields()
                ->where('id', $fieldData['id'])
                ->update(['order' => $fieldData['order']]);
        }

        return back(303);
    }

    protected function getMaxFormsPerBusiness(): int
    {
        return 5;
    }
}
