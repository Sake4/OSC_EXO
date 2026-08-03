<?php

$user     = 'sake';
$password = 'Y3dheuMhj7ACMC6YGPqc4lThIsqgRhJP';

$dsn = "pgsql:host=dpg-d9nir3fqj5pc73f0103g-a.frankfurt-postgres.render.com;port=5432;dbname=oscprojet;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connexion à la base impossible : ' . $e->getMessage()]);
    exit;
}
