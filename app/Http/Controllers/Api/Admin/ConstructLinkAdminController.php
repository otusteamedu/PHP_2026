<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Construct;
use App\Models\ConstructLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstructLinkAdminController extends Controller
{
    public function store(Request $request, Construct $construct): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $link = ConstructLink::query()->create([
            'construct_id' => $construct->id,
            'title' => $validated['title'] ?? null,
            'url' => $validated['url'],
            'sort' => (int) ($validated['sort'] ?? 0),
        ]);

        return response()->json($link, 201);
    }

    public function update(Request $request, ConstructLink $link): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'url' => ['sometimes', 'url', 'max:2048'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $link->fill($validated);
        $link->save();

        return response()->json($link);
    }

    public function destroy(ConstructLink $link): JsonResponse
    {
        $link->delete();

        return response()->noContent();
    }
}
