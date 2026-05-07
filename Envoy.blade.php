@servers(['localhost' => '127.0.0.1'])

@setup
    $devRoot = getenv('DEPLOY_DEV_ROOT');
    if ($devRoot === false || $devRoot === '') {
        $devRoot = getcwd();
    }
    $prodRoot = getenv('DEPLOY_PROD_ROOT');
    if ($prodRoot === false || $prodRoot === '') {
        $prodRoot = dirname($devRoot).DIRECTORY_SEPARATOR.basename($devRoot).'-production';
    }
    $devRoot = str_replace('\\', '/', $devRoot);
    $prodRoot = str_replace('\\', '/', $prodRoot);
    $branch = getenv('DEPLOY_BRANCH');
    if ($branch === false || $branch === '') {
        $branch = 'main';
        $headFile = $devRoot.'/.git/HEAD';
        if (is_readable($headFile)) {
            $ref = trim((string) file_get_contents($headFile));
            if (str_starts_with($ref, 'ref: refs/heads/')) {
                $branch = substr($ref, 16);
            }
        }
    }
@endsetup

@story('deploy')
    quality
    publish
@endstory

@task('quality', ['on' => 'localhost'])
    cd "{{ $devRoot }}" && php artisan test && php vendor/bin/pint --test
@endtask

@task('publish', ['on' => 'localhost'])
    cd "{{ $prodRoot }}" && git pull origin {{ $branch }} && composer install --no-dev --optimize-autoloader --no-interaction && npm install --omit=dev && npm run build && php artisan migrate --force --no-interaction && php artisan optimize && php artisan queue:restart && php artisan cache:warm-app
@endtask
