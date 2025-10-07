<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID pitanja nije poslan"]);
    exit;
}

$id = intval($data['id']);
$force = isset($data['force']) && $data['force'] === true;

$pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");

try {


    // Obriši pitanje iz baze
    $stmt = $pdo->prepare("DELETE FROM pitanja WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Pitanje uspješno obrisano."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Greška: " . $e->getMessage()]);
}
