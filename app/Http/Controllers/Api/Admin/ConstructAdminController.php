<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Construct;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstructAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['nullable', 'string', 'max:32'],
        ]);

        $query = Construct::query()->with('language:id,code,name');

        if (! empty($validated['language'])) {
            $languageId = Language::query()->where('code', $validated['language'])->value('id');
            $query->where('language_id', $languageId ?? 0);
        }

        return response()->json($query->orderBy('title')->paginate(50));
    }

    public function show(Construct $construct): JsonResponse
    {
        $construct->load(['language:id,code,name', 'snippets', 'links']);

        return response()->json($construct);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'max:32'],
            'slug' => ['required', 'string', 'max:128'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $languageId = Language::query()->where('code', $validated['language'])->value('id');
        if ($languageId === null) {
            return response()->json(['message' => 'Unknown language'], 422);
        }

        $model = Construct::query()->create([
            'language_id' => $languageId,
            'slug' => $validated['slug'],
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'details' => $validated['details'] ?? null,
        ]);

        return response()->json($model, 201);
    }

    public function update(Request $request, Construct $construct): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['sometimes', 'string', 'max:128'],
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $construct->fill($validated);
        $construct->save();

        return response()->json($construct);
    }

    public function destroy(Construct $construct): JsonResponse
    {
        $construct->delete();

        return response()->noContent();
    }
}
