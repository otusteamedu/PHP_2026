<?php

namespace Hw11\LearningPackage;

use Hw11\LearningPackage\Console\PrintSignatureCommand;
use Hw11\LearningPackage\Contracts\SignatureBuilderInterface;
use Illuminate\Support\ServiceProvider;

class LearningPackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/learning-package.php',
            'learning-package'
        );

        $this->app->singleton(SignatureBuilderInterface::class, SignatureBuilder::class);
    }

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__).'/config/learning-package.php' => config_path('learning-package.php'),
        ], 'learning-package-config');

        $this->commands([
            PrintSignatureCommand::class,
        ]);
    }
}
