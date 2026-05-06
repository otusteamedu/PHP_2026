<?php

namespace App\Domain\Page;

use App\Domain\Page\ValueObjects\PageBody;
use App\Domain\Page\ValueObjects\PageSlug;
use App\Domain\Page\ValueObjects\PageTitle;

final class PageAggregate
{
    private function __construct(
        private ?int $id,
        private PageTitle $title,
        private PageSlug $slug,
        private ?PageBody $body,
        private bool $isPublished,
    ) {}

    public static function draft(
        PageTitle $title,
        PageSlug $slug,
        ?PageBody $body,
        bool $isPublished,
    ): self {
        return new self(null, $title, $slug, $body, $isPublished);
    }

    public static function restore(
        int $id,
        PageTitle $title,
        PageSlug $slug,
        ?PageBody $body,
        bool $isPublished,
    ): self {
        if ($id < 1) {
            throw new \InvalidArgumentException('id');
        }

        return new self($id, $title, $slug, $body, $isPublished);
    }

    public function assignPersistedIdentity(int $id): void
    {
        if ($this->id !== null) {
            throw new \LogicException('id');
        }
        if ($id < 1) {
            throw new \InvalidArgumentException('id');
        }
        $this->id = $id;
    }

    public function publish(): void
    {
        $this->isPublished = true;
    }

    public function unpublish(): void
    {
        $this->isPublished = false;
    }

    public function revise(PageTitle $title, PageSlug $slug, ?PageBody $body, bool $isPublished): void
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->body = $body;
        $this->isPublished = $isPublished;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): PageTitle
    {
        return $this->title;
    }

    public function slug(): PageSlug
    {
        return $this->slug;
    }

    public function body(): ?PageBody
    {
        return $this->body;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
