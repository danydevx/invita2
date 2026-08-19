<?php

namespace Modules\Properties\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Properties\GeneralFieldService;
use Illuminate\Http\Request;
use Modules\Properties\Models\GeneralField;
use Modules\Properties\Models\GeneralFieldOption;

class GeneralFieldOptionController extends Controller
{
    public function __construct(
        protected GeneralFieldService $generalFieldService
    ) {}

    public function store(Request $request, GeneralField $generalField)
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $option = $this->generalFieldService->createFieldOption($generalField, $data);

        return response()->json($option);
    }

    public function update(Request $request, GeneralField $generalField, GeneralFieldOption $generalFieldOption)
    {
        $data = $request->validate([
            'value' => ['sometimes', 'required', 'string', 'max:100'],
            'label' => ['sometimes', 'required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $this->generalFieldService->updateFieldOption($generalFieldOption, $data);

        return response()->json($generalFieldOption);
    }

    public function destroy(GeneralField $generalField, GeneralFieldOption $generalFieldOption)
    {
        $this->generalFieldService->deleteFieldOption($generalFieldOption);

        return response()->json(['success' => true]);
    }
}
