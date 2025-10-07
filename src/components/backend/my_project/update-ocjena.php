<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['rezultat_id'], $data['nova_ocjena'])) {
    echo json_encode(["success" => false, "message" => "Nedostaju podaci"]); exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE rezultati_testova SET ocjena=? WHERE id=?");
    $stmt->execute([$data['nova_ocjena'], $data['rezultat_id']]);

    echo json_encode(["success" => true, "message" => "Ocjena ažurirana"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
