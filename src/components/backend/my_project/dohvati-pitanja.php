<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, naziv, tip, odgovor AS tocan_odgovor FROM pitanja ORDER BY naziv ASC");
    $pitanja = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "pitanja" => $pitanja
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Greška kod dohvaćanja pitanja."
    ]);
}
