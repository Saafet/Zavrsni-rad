<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['naziv']) || empty($data['odabranaPitanja']) || !is_array($data['odabranaPitanja'])) {
    echo json_encode(["success" => false, "message" => "Nedostaju podaci ili su neispravni."]);
    exit;
}

$naziv = trim($data['naziv']);
$odabranaPitanja = $data['odabranaPitanja'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO testovi (naziv) VALUES (?)");
    $stmt->execute([$naziv]);
    $testId = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO test_pitanja (test_id, pitanje_id) VALUES (?, ?)");
    foreach ($odabranaPitanja as $pid) {
        $stmt2->execute([$testId, (int)$pid]);
    }

    $pdo->commit();

    echo json_encode(["success" => true, "message" => "Test kreiran."]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => "Greška: " . $e->getMessage()]);
}
