<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Construct;
use App\Models\ConstructSnippet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstructSnippetAdminController extends Controller
{
    public function store(Request $request, Construct $construct): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $snippet = ConstructSnippet::query()->create([
            'construct_id' => $construct->id,
            'title' => $validated['title'] ?? null,
            'code' => $validated['code'],
            'sort' => (int) ($validated['sort'] ?? 0),
        ]);

        return response()->json($snippet, 201);
    }

    public function update(Request $request, ConstructSnippet $snippet): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'code' => ['sometimes', 'string'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $snippet->fill($validated);
        $snippet->save();

        return response()->json($snippet);
    }

    public function destroy(ConstructSnippet $snippet): JsonResponse
    {
        $snippet->delete();

        return response()->noContent();
    }
}
