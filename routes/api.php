<?php

use App\Controllers\Api\EventController;

return [
    'POST /api/v1/events/add' => [EventController::class, 'addEvent'],
    'POST /api/v1/events/clear' => [EventController::class, 'clearEvents'],
    'POST /api/v1/events/best-matching-event' => [EventController::class, 'findBestMatchingEvent'],
];