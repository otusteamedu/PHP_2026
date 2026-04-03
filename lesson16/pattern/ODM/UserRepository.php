<?php

class UserRepository extends AbstractRepository
{
    public function __construct(
        private DocumentManager $dm
    ) {
    }

    public function save(UserDocument $user): void
    {
        $this->dm->save($user, UserMetadata::class);
    }

    public function find(string $id): ?UserDocument
    {
        return $this->dm->find(UserMetadata::class, $id, UserDocument::class);
    }

    public function all(): array
    {
        return $this->dm->all(UserMetadata::class, UserDocument::class);
    }
}