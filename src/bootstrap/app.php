<?php

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['visits'])) {
    $_SESSION['visits'] = 1;
    $_SESSION['HOSTNAME'] = $_SERVER['HOSTNAME'];
    $message = "Welcome! This is your first visit. "
        . "Session started at container: " . $_SESSION['HOSTNAME'] . ". "
        . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
} else {
    $_SESSION['visits']++;
    $message = "Welcome back! This is visit #" . $_SESSION['visits'] . ". "
        . "Session started at container: " . ($_SESSION['HOSTNAME'] ?? 'unknown') . ". "
        . "Current container is: " . $_SERVER['HOSTNAME'] . ".";
}

