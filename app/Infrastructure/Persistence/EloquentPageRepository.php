<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Page\PageAggregate;
use App\Domain\Page\PageRepository;
use App\Domain\Page\ValueObjects\PageBody;
use App\Domain\Page\ValueObjects\PageSlug;
use App\Domain\Page\ValueObjects\PageTitle;
use App\Models\Page;

final class EloquentPageRepository implements PageRepository
{
    public function save(PageAggregate $aggregate): Page
    {
        $model = $aggregate->id() !== null
            ? Page::query()->whereKey($aggregate->id())->firstOrFail()
            : new Page;

        $body = $aggregate->body();

        $model->forceFill([
            'title' => $aggregate->title()->toString(),
            'slug' => $aggregate->slug()->toString(),
            'body' => $body?->toPlain(),
            'is_published' => $aggregate->isPublished(),
        ]);

        $model->save();

        if ($aggregate->id() === null) {
            $aggregate->assignPersistedIdentity((int) $model->getKey());
        }

        return $model->fresh();
    }

    public function findById(int $id): ?PageAggregate
    {
        $row = Page::query()->whereKey($id)->first();
        if ($row === null) {
            return null;
        }

        return PageAggregate::restore(
            (int) $row->getKey(),
            PageTitle::fromString($row->title),
            PageSlug::fromString($row->slug),
            PageBody::fromNullable($row->body),
            (bool) $row->is_published
        );
    }
}
