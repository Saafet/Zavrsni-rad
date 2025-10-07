<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$pdo = new PDO('mysql:host=localhost;dbname=project_db;charset=utf8', 'root', '');

try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.ime, p.prezime, p.email, p.datum, p.tema_id, t.naziv AS tema_naziv
        FROM projekti p
        JOIN teme t ON p.tema_id = t.id
    ");
    $stmt->execute();
    $projekti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'projekti' => $projekti
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}