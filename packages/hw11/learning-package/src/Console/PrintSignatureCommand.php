<?php

namespace Hw11\LearningPackage\Console;

use Hw11\LearningPackage\Contracts\SignatureBuilderInterface;
use Illuminate\Console\Command;

class PrintSignatureCommand extends Command
{
    protected $signature = 'learning-package:signature {subject=laravel-homework : Text to sign}';

    protected $description = 'Build a signature string using the package SignatureBuilder';

    public function handle(SignatureBuilderInterface $signer): int
    {
        $this->line($signer->build((string) $this->argument('subject')));

        return self::SUCCESS;
    }
}
