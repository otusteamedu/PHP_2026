<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Domain\Event;
use App\Infrastructure\RedisEventService;
use App\Infrastructure\RedisFactory;
use App\Services\EventMatcherService;

$redis = RedisFactory::create();
$storage = new RedisEventService($redis);
$matcher = new EventMatcherService($storage);

$storage->clear();

$storage->add(new Event(
    id: 'event-1',
    priority: 1,
    conditions: [],
    eventData: [
        'action' => 'show_default_banner'
    ]
));

$storage->add(new Event(
    id: 'event-2',
    priority: 10,
    conditions: [
        'geo' => 'RU',
        'platform' => 'android'
    ],
    eventData: [
        'action' => 'show_banner_ru_android'
    ]
));

$storage->add(new Event(
    id: 'event-3',
    priority: 100,
    conditions: [
        'geo' => 'RU',
        'platform' => 'android',
        'vip' => '1'
    ],
    eventData: [
        'action' => 'show_banner_ru_android_vip'
    ]
));

$storage->add(new Event(
    id: 'event-4',
    priority: 80,
    conditions: [
        'platform' => 'ios',
        'subscription' => 'premium'
    ],
    eventData: [
        'action' => 'show_premium_ios_screen'
    ]
));

$storage->add(new Event(
    id: 'event-5',
    priority: 50,
    conditions: [
        'geo' => 'DE',
        'campaign' => 'spring_sale'
    ],
    eventData: [
        'action' => 'show_spring_sale_banner'
    ]
));


$params1 = [
    'geo' => 'RU',
    'platform' => 'android',
    'version' => '12'
];

$bestEvent1 = $matcher->findEvent($params1);
echo "Результат 1: " . ($bestEvent1 ? $bestEvent1->eventData['action'] : 'null') . "\n"; 

$params2 = [
    'geo' => 'RU',
    'platform' => 'android',
    'vip' => '1'
];

$bestEvent2 = $matcher->findEvent($params2);
echo "Результат 2: " . ($bestEvent2 ? $bestEvent2->eventData['action'] : 'null') . "\n";

$params3 = [
    'platform' => 'ios',
    'subscription' => 'premium'
];

$bestEvent3 = $matcher->findEvent($params3);
echo "Результат 3: " . ($bestEvent3 ? $bestEvent3->eventData['action'] : 'null') . "\n";

$params4 = [
    'geo' => 'DE',
    'campaign' => 'spring_sale'
];

$bestEvent4 = $matcher->findEvent($params4);
echo "Результат 4: " . ($bestEvent4 ? $bestEvent4->eventData['action'] : 'null') . "\n";

$params5 = [
    'geo' => 'BR',
    'platform' => 'web'
];

$bestEvent5 = $matcher->findEvent($params5);
echo "Результат 5: " . ($bestEvent5 ? $bestEvent5->eventData['action'] : 'null') . "\n";