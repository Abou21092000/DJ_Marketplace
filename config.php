<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "marketplace_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Pour supporter les accents et les emojis
$conn->set_charset("utf8mb4");
?>