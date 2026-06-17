<?php

namespace Hw11\LearningPackage;

use Hw11\LearningPackage\Contracts\SignatureBuilderInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class SignatureBuilder implements SignatureBuilderInterface
{
    public function __construct(
        private readonly ConfigRepository $config
    ) {}

    public function build(string $subject): string
    {
        $prefix = (string) $this->config->get('learning-package.prefix', 'HW11');

        return $prefix.':'.hash('sha256', $subject);
    }
}
