<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

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

$naziv = trim($data['naziv']);
$tip = strtolower($data['tip']);
$odgovor = $tip === 'text' 
    ? trim($data['tocan_odgovor']) 
    : (int) filter_var($data['tocan_odgovor'], FILTER_VALIDATE_BOOLEAN);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pitanja WHERE naziv = ?");
    $stmt->execute([$naziv]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Pitanje s tim nazivom već postoji."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO pitanja (naziv, tip, odgovor) VALUES (?, ?, ?)");
    $stmt->execute([$naziv, $tip, $odgovor]);

    echo json_encode([
        "success" => true,
        "message" => "Pitanje dodano.",
        "pitanje" => [
            "id" => $pdo->lastInsertId(),
            "naziv" => $naziv,
            "tip" => $tip,
            "tocan_odgovor" => $odgovor
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Greška kod dodavanja pitanja."]);
}
