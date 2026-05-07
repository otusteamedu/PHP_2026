<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LanguageAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Language::query()->orderBy('code')->get());
    }

    public function show(Language $language): JsonResponse
    {
        return response()->json($language);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:128'],
        ]);

        $model = Language::query()->create($validated);

        return response()->json($model, 201);
    }

    public function update(Request $request, Language $language): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['sometimes', 'string', 'max:32'],
            'name' => ['sometimes', 'string', 'max:128'],
        ]);

        $language->fill($validated);
        $language->save();

        return response()->json($language);
    }

    public function destroy(Language $language): Response
    {
        $language->delete();

        return response()->noContent();
    }
}
