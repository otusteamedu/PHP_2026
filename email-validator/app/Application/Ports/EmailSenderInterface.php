<?php

namespace App\Application\Ports;

interface EmailSenderInterface
{
    public function send(string $to,string $subject,string $body,string $pdfContent,string $fileName): void;
}