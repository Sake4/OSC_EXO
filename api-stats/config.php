<?php

$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'DATABASE_URL manquant dans les variables d\'environnement.']);
    exit;
}

$infos = parse_url($databaseUrl);
$host     = $infos['host'];
$port     = $infos['port'] ?? '5432';
$dbname   = ltrim($infos['path'], '/');
$user     = $infos['user'];
$password = $infos['pass'];

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

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