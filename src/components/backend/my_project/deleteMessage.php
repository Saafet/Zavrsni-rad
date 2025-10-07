<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["poruka" => "Nedostaje ID poruke."]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "project_db");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška s bazom."]);
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM kontakt_poruke WHERE id = ?");
$stmt->bind_param("i", $data['id']);

if ($stmt->execute()) {
    echo json_encode(["poruka" => "Poruka uspješno obrisana."]);
} else {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška prilikom brisanja."]);
}

$stmt->close();
$mysqli->close();
