<?php
header('Content-Type: text/plain; charset=utf-8');
echo "Hello from PHP-FPM\n";


// Redis test
if (class_exists('Redis')) {
    $r = new Redis();
    $r->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
    $r->set('test', 'ok');
    echo "Redis: " . $r->get('test') . "\n";
} else {
    echo "Redis extension not installed\n";
}

// Memcached test
if (class_exists('Memcached')) {
    $m = new Memcached();
    $m->addServer(getenv('MEMCACHED_HOST') ?: 'memcached', 11211);
    $m->set('test', 'ok');
    echo "Memcached: " . $m->get('test') . "\n";
} else {
    echo "Memcached extension not installed\n";
}

// phpinfo();


