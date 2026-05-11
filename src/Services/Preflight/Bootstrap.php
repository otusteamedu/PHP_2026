<?php

declare(strict_types=1);

namespace App\Services\Preflight;

final readonly class Bootstrap
{
    /**
     * Confirm system can run script
     */
    public static function preflight(): void
    {
        $phpversion_array = explode('.', PHP_VERSION);
        if ((int)$phpversion_array[0] . $phpversion_array[1] < 56) {
            die('minimum php required is 5.6. exiting');
        }

        if (!extension_loaded('posix')) {
            die('posix required. exiting');
        }
    }
}
