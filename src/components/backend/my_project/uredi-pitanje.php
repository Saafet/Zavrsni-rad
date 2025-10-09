<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !is_numeric($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID pitanja je obavezan."]);
    exit;
}

if (!isset($data['naziv']) || empty(trim($data['naziv']))) {
    echo json_encode(["success" => false, "message" => "Naziv pitanja je obavezan."]);
    exit;
}

if (!isset($data['tip']) || !in_array(strtolower($data['tip']), ['text', 'truefalse'])) {
    echo json_encode(["success" => false, "message" => "Tip pitanja je obavezan."]);
    exit;
}

if (!isset($data['tocan_odgovor']) || ($data['tip'] === 'text' && empty(trim($data['tocan_odgovor'])))) {
    echo json_encode(["success" => false, "message" => "Odgovor je obavezan."]);
    exit;
}

$id = (int) $data['id'];
$naziv = trim($data['naziv']);
$tip = strtolower($data['tip']);
$odgovor = $tip === 'text' 
    ? trim($data['tocan_odgovor']) 
    : (int) filter_var($data['tocan_odgovor'], FILTER_VALIDATE_BOOLEAN);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pitanja WHERE naziv = ? AND id != ?");
    $stmt->execute([$naziv, $id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Pitanje s tim nazivom već postoji."]);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE pitanja SET naziv = ?, tip = ?, odgovor = ? WHERE id = ?");
    $stmt->execute([$naziv, $tip, $odgovor, $id]);

    echo json_encode([
        "success" => true,
        "message" => "Pitanje uređeno.",
        "pitanje" => [
            "id" => $id,
            "naziv" => $naziv,
            "tip" => $tip,
            "tocan_odgovor" => $odgovor
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Greška kod uređivanja pitanja."]);
}
