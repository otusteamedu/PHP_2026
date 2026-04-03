<?php

// Object style
$servername = "172.30.185.21";
$port=33092;
$username = "admin";
$password = "admin";
$dbname = "php-2026";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

$firstname = 'object test';
$stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
$stmt->bind_param("s", $firstname);
$stmt->execute();
$stmt->close();

// Procedural style
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);
//$firstname = 'procedural test';
$firstname = "test'); TRUNCATE TABLE USERS; -- ";
$sql = "INSERT INTO users (username) VALUES ('$firstname')";

mysqli_query($conn, $sql);
mysqli_close($conn);
