<?php

declare(strict_types=1);

namespace App\Services\Cli;

class CliHelper
{
    public private(set) array $args = [
        'title' => null,
        'category' => null,
        'price' => null,
        'sku' => null,
        'search' => null,
        'sort' => null,
        'stock_gt' => null,
        'strict' => null,
        'flags' => [],
        'positional' => [],
    ];

    public function mapInputToArgs(array $input): void
    {
        $input = array_slice($input, 1);

        foreach ($input as $arg) {
            match (substr_count($arg, '-', 0, 2)) {
                2 => $this->defineDoubleDash($arg),
                1 => $this->defineSingleDash($arg),
                default => $this->args['positional'][] = $arg,
            };
        }
    }

    private function defineSingleDash(string $arg): void
    {
        foreach (str_split(ltrim($arg, '-')) as $a) {
            $this->args['flags'][$a] = ($this->args['flags'][$a] ?? 0) + 1;
        }
    }

    private function defineDoubleDash(string $arg): void
    {
        [$argName, $argValue] = array_pad(explode('=', ltrim($arg, '-'), 2), 2, true);
        $argName = str_replace('-', '_', $argName);

        if (array_key_exists($argName, $this->args)) {
            $this->args[$argName] = $argValue;
        }
    }
}
