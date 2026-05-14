<?php

declare(strict_types=1);

namespace App\Core\Module;

use App\Core\Http\Controller\MovieController;
use App\Service\MovieService;
use App\Storage\DataMapper\IdentityMap;
use App\Storage\DataMapper\MovieMapper;
use App\Storage\MovieRepositoryInterface;
use App\Storage\PdoMovieRepository;
use PDO;

final class MovieModule
{
    private ?MovieController $controller = null;

    private ?MovieService $service = null;

    private ?MovieRepositoryInterface $repository = null;

    private ?MovieMapper $mapper = null;

    private ?IdentityMap $identityMap = null;

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function controller(): MovieController
    {
        return $this->controller ??= new MovieController($this->service());
    }

    private function service(): MovieService
    {
        return $this->service ??= new MovieService($this->repository());
    }

    private function repository(): MovieRepositoryInterface
    {
        return $this->repository ??= new PdoMovieRepository($this->mapper());
    }

    private function mapper(): MovieMapper
    {
        return $this->mapper ??= new MovieMapper($this->pdo, $this->identityMap());
    }

    private function identityMap(): IdentityMap
    {
        return $this->identityMap ??= new IdentityMap();
    }
}
