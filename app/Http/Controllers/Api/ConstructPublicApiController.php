<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Construct;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstructPublicApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['nullable', 'string', 'max:32'],
            'q' => ['nullable', 'string', 'max:200'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);
        $q = isset($validated['q']) ? trim((string) $validated['q']) : '';
        $languageCode = isset($validated['language']) ? trim((string) $validated['language']) : '';

        $query = Construct::query()
            ->with('language:id,code,name')
            ->select(['id', 'language_id', 'slug', 'title', 'summary']);

        if ($languageCode !== '') {
            $languageId = Language::query()->where('code', $languageCode)->value('id');
            if ($languageId === null) {
                return response()->json(['data' => []]);
            }
            $query->where('language_id', $languageId);
        }

        if ($q !== '') {
            $query->where(function ($q1) use ($q): void {
                $q1->where('title', 'like', '%'.$q.'%')
                    ->orWhere('summary', 'like', '%'.$q.'%')
                    ->orWhere('details', 'like', '%'.$q.'%');
            });
        }

        $items = $query->orderBy('title')->limit($limit)->get();

        return response()->json([
            'data' => $items->map(static fn (Construct $c) => [
                'language' => $c->language?->code,
                'slug' => $c->slug,
                'title' => $c->title,
                'summary' => $c->summary,
            ])->values(),
        ]);
    }

    public function show(string $language, string $slug): JsonResponse
    {
        $languageId = Language::query()->where('code', $language)->value('id');
        if ($languageId === null) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $construct = Construct::query()
            ->where('language_id', $languageId)
            ->where('slug', $slug)
            ->with([
                'language:id,code,name',
                'snippets:id,construct_id,title,code,sort',
                'links:id,construct_id,title,url,sort',
            ])
            ->first();

        if ($construct === null) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'language' => $construct->language?->code,
            'slug' => $construct->slug,
            'title' => $construct->title,
            'summary' => $construct->summary,
            'details' => $construct->details,
            'snippets' => $construct->snippets->map(static fn ($s) => [
                'title' => $s->title,
                'code' => $s->code,
            ])->values(),
            'links' => $construct->links->map(static fn ($l) => [
                'title' => $l->title,
                'url' => $l->url,
            ])->values(),
        ]);
    }
}
