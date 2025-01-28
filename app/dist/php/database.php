<?php

try {
    $pdo = new PDO ("mysql:host=".$_SERVER["DB_HOST"].":".$_SERVER["DB_PORT"].";dbname=".$_SERVER["DB_NAME"].";charset=utf8mb4", $_SERVER["DB_USER"], $_SERVER["DB_PASSWORD"]);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>