<?php

declare(strict_types=1);

namespace App\app\Services;

class SessionService
{
    public static function start(): void
    {
        header('Content-Type: application/json');

        session_start();

        if (!isset($_SESSION['visits'])) {
            $_SESSION['visits'] = 1;
            $_SESSION['HOSTNAME'] = $_SERVER['HOSTNAME'];
            $_SESSION['message'] = "Welcome! This is your first visit. "
                . "Session started at container: " . $_SESSION['HOSTNAME'] . ". "
                . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
        } else {
            $_SESSION['visits']++;
            $_SESSION['message'] = "Welcome back! This is visit #" . $_SESSION['visits'] . ". "
                . "Session started at container: " . ($_SESSION['HOSTNAME'] ?? 'unknown') . ". "
                . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
        }
    }
}
