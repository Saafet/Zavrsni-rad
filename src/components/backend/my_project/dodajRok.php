<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

$data = json_decode(file_get_contents("php://input"), true);
$datum = $data['datum'] ?? null;
$napomena = $data['napomena'] ?? "";

$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška u vezi s bazom."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO rokovi (datum, napomena) VALUES (?, ?)");
$stmt->bind_param("ss", $datum, $napomena);

if ($stmt->execute()) {
    echo json_encode(["poruka" => "Rok dodan."]);
} else {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška prilikom spremanja."]);
}

$stmt->close();
$conn->close();
