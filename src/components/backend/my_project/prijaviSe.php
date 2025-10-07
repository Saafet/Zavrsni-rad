<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

$data = json_decode(file_get_contents("php://input"), true);
$ime = $data['ime'] ?? null;
$rok_id = $data['rok_id'] ?? null;

$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška u vezi s bazom."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO prijave (ime, rok_id) VALUES (?, ?)");
$stmt->bind_param("si", $ime, $rok_id);

if ($stmt->execute()) {
    echo json_encode(["poruka" => "Prijava uspješna."]);
} else {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška prilikom prijave."]);
}

$stmt->close();
$conn->close();
