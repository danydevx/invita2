<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Properties\GeneralFieldService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\GeneralFieldSection;

class GeneralFieldSectionController extends Controller
{
    public function __construct(
        protected GeneralFieldService $generalFieldService
    ) {}

    public function index()
    {
        $sections = GeneralFieldSection::with(['fields.fieldOptions'])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Properties/GeneralFields/Index', [
            'sections' => $sections,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $section = $this->generalFieldService->createSection($data);

        return redirect()->back()->with('success', 'Sección creada.');
    }

    public function update(Request $request, GeneralFieldSection $generalFieldSection)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_locked' => ['boolean'],
        ]);

        $this->generalFieldService->updateSection($generalFieldSection, $data);

        return redirect()->back()->with('success', 'Sección actualizada.');
    }

    public function destroy(GeneralFieldSection $generalFieldSection)
    {
        if ($generalFieldSection->assignments()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una sección que está asignada a tipos de propiedad.');
        }

        $this->generalFieldService->deleteSection($generalFieldSection);

        return redirect()->back()->with('success', 'Sección eliminada.');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*' => ['required', 'integer'],
        ]);

        foreach ($data['sections'] as $index => $id) {
            GeneralFieldSection::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }
}
