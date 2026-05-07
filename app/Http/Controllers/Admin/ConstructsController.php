<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Construct;
use App\Models\ConstructLink;
use App\Models\ConstructSnippet;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstructsController extends Controller
{
    public function index(Request $request): View
    {
        $language = trim((string) $request->query('language', ''));
        $q = trim((string) $request->query('q', ''));

        $query = Construct::query()->with('language');

        if ($language !== '') {
            $languageId = Language::query()->where('code', $language)->value('id');
            $query->where('language_id', $languageId ?? 0);
        }

        if ($q !== '') {
            $query->where(function ($q1) use ($q): void {
                $q1->where('title', 'like', '%'.$q.'%')
                    ->orWhere('slug', 'like', '%'.$q.'%')
                    ->orWhere('summary', 'like', '%'.$q.'%')
                    ->orWhere('details', 'like', '%'.$q.'%');
            });
        }

        return view('admin.constructs.index', [
            'items' => $query->orderBy('title')->paginate(30)->withQueryString(),
            'languages' => Language::query()->orderBy('code')->get(),
            'filters' => ['language' => $language, 'q' => $q],
        ]);
    }

    public function create(): View
    {
        return view('admin.constructs.create', [
            'languages' => Language::query()->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'slug' => ['required', 'string', 'max:128'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $construct = Construct::query()->create($validated);

        return redirect()->route('admin.constructs.edit', $construct);
    }

    public function edit(Construct $construct): View
    {
        $construct->load(['language', 'snippets', 'links', 'aliases']);

        return view('admin.constructs.edit', [
            'construct' => $construct,
            'languages' => Language::query()->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Construct $construct): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => ['required', 'integer', 'exists:languages,id'],
            'slug' => ['required', 'string', 'max:128'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $construct->fill($validated)->save();

        return back()->with('ok', 'Сохранено');
    }

    public function destroy(Construct $construct): RedirectResponse
    {
        $construct->delete();

        return redirect()->route('admin.constructs.index');
    }

    public function storeSnippet(Request $request, Construct $construct): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        ConstructSnippet::query()->create([
            'construct_id' => $construct->id,
            'title' => $validated['title'] ?? null,
            'code' => $validated['code'],
            'sort' => (int) ($validated['sort'] ?? 0),
        ]);

        return back();
    }

    public function destroySnippet(ConstructSnippet $snippet): RedirectResponse
    {
        $snippet->delete();

        return back();
    }

    public function storeLink(Request $request, Construct $construct): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        ConstructLink::query()->create([
            'construct_id' => $construct->id,
            'title' => $validated['title'] ?? null,
            'url' => $validated['url'],
            'sort' => (int) ($validated['sort'] ?? 0),
        ]);

        return back();
    }

    public function destroyLink(ConstructLink $link): RedirectResponse
    {
        $link->delete();

        return back();
    }
}
