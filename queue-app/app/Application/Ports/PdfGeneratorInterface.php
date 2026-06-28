<?php

namespace App\Application\Ports;

interface PdfGeneratorInterface
{    
    public function generate(array $validationResults): string;
}