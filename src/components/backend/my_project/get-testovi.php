<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, naziv, datum_kreiranja FROM testovi ORDER BY datum_kreiranja DESC");
    $testovi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "testovi" => $testovi], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
