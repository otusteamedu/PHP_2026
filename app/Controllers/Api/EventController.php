<?php

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Models\Event;
use App\Repositores\EventRepository;
use App\Storages\RedisStorage;

class EventController
{
    private EventRepository $repository;
    public function __construct()
    {
        $this->repository = new EventRepository(new RedisStorage);
    }
    public function addEvent(Request $request)
    {
        $this->repository->addEvent(Event::fromRequest($request));
        return new Response([
            'message' => "Успешно добавлено",
        ]);
    }

    public function clearEvents(Request $request)
    {
        $this->repository->clearEvents();
        return new Response([
            'message' => 'Очищено'
        ]);
    }

    public function findBestMatchingEvent(Request $request)
    {
        $event = $this->repository->findBestEvent($request->getJsonBody()['params']);
        return new Response([
            'event' => $event
        ]);
    }
}
