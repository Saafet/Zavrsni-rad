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

if (!isset($data['ime'], $data['email'], $data['poruka'])) {
    http_response_code(400);
    echo json_encode(["poruka" => "Nedostaju podaci."]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "project_db");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška s bazom."]);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO kontakt_poruke (ime, email, poruka) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['ime'], $data['email'], $data['poruka']);

if ($stmt->execute()) {
    echo json_encode(["poruka" => "Poruka uspješno poslana!"]);
} else {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška prilikom slanja."]);
}

$stmt->close();
$mysqli->close();