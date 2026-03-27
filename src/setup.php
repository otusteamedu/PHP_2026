<?php
declare(strict_types=1);

// Настройки индекса с русской морфологией
$indexConfig = [
    'settings' => [
        'analysis' => [
            'analyzer' => [
                'ru_analyzer' => [
                    'tokenizer' => 'standard',
                    'filter' => ['lowercase', 'russian_morphology', 'russian_stop', 'russian_stemmer']
                ]
            ]
        ]
    ],
    'mappings' =>,
            'category' => ['type' => 'keyword'],
            'price' => ['type' => 'float'],
            'stock' => ['type' => 'integer']
        ]
    ]
];

// Отправляем в Elastic
$ch = curl_init("http://evgeny87-elasticsearch:9200/books");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($indexConfig));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Индекс 'books' настроен.\n";

