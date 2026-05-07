<?php

namespace App\Domain\Page;

use App\Models\Page;

interface PageRepository
{
    public function save(PageAggregate $aggregate): Page;

    public function findById(int $id): ?PageAggregate;
}
