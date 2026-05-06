<?php

namespace Hw11\LearningPackage\Contracts;

interface SignatureBuilderInterface
{
    public function build(string $subject): string;
}
