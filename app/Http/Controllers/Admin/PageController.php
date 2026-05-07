<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Page\PageAggregate;
use App\Domain\Page\PageRepository;
use App\Domain\Page\ValueObjects\PageBody;
use App\Domain\Page\ValueObjects\PageSlug;
use App\Domain\Page\ValueObjects\PageTitle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageRepository $pages
    ) {}

    public function index(): View
    {
        $pages = Page::query()->latest('updated_at')->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $aggregate = PageAggregate::draft(
            PageTitle::fromString($data['title']),
            PageSlug::fromString($data['slug']),
            PageBody::fromNullable($data['body'] ?? null),
            (bool) ($data['is_published'] ?? false),
        );

        $this->pages->save($aggregate);

        return redirect()->route('admin.pages.index')->with('ok', 'Страница создана.');
    }

    public function edit(string $locale, Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(string $locale, Page $page, UpdatePageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $aggregate = $this->pages->findById((int) $page->getKey());
        if ($aggregate === null) {
            abort(404);
        }

        $aggregate->revise(
            PageTitle::fromString($data['title']),
            PageSlug::fromString($data['slug']),
            PageBody::fromNullable($data['body'] ?? null),
            (bool) ($data['is_published'] ?? false),
        );

        $this->pages->save($aggregate);

        return redirect()->route('admin.pages.index')->with('ok', 'Сохранено.');
    }

    public function destroy(string $locale, Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('ok', 'Удалено.');
    }
}
