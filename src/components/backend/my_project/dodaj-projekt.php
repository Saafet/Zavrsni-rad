<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['ime'], $data['prezime'], $data['email'], $data['datum'], $data['tema_id']) ||
    empty(trim($data['ime'])) ||
    empty(trim($data['prezime'])) ||
    empty(trim($data['email'])) ||
    empty(trim($data['datum'])) ||
    empty($data['tema_id'])
) {
    echo json_encode(["success" => false, "message" => "Sva polja su obavezna."]);
    exit;
}

$ime = trim($data['ime']);
$prezime = trim($data['prezime']);
$email = trim($data['email']);
$datum = $data['datum'];
$tema_id = (int)$data['tema_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projekti WHERE tema_id = ?");
    $stmt->execute([$tema_id]);
    if ($stmt->fetchColumn() >= 2) {
        echo json_encode(["success" => false, "message" => "Tema je već zauzeta."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO projekti (ime, prezime, email, datum, tema_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$ime, $prezime, $email, $datum, $tema_id]);

    echo json_encode(["success" => true, "message" => "Projekt uspješno prijavljen."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Greška pri prijavi projekta."]);
}
