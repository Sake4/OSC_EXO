<?php

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

if ($uri === '/stats/profils') {
    $stmt = $pdo->query(
        'SELECT profil, COUNT(*) AS total
         FROM individu
         GROUP BY profil
         ORDER BY total DESC'
    );
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($uri === '/stats/individus-par-groupe') {
    $stmt = $pdo->query(
        'SELECT g.id_groupe, g.numero_groupe, COUNT(i.id_individu) AS nombre_individus
         FROM groupe g
         LEFT JOIN individu i ON i.id_groupe = g.id_groupe
         GROUP BY g.id_groupe, g.numero_groupe
         ORDER BY g.id_groupe'
    );
    echo json_encode($stmt->fetchAll());
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Route introuvable.']);
