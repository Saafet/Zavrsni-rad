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

if (!isset($data['datum'], $data['napomena'])) {
    http_response_code(400);
    echo json_encode(["poruka" => "Nedostaju podaci."]);
    exit;
}

$datum = htmlspecialchars($data['datum']);
$napomena = htmlspecialchars($data['napomena']);

$conn = new mysqli("localhost", "root", "", "project_db");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška u bazi."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO rokovi (datum, napomena) VALUES (?, ?)");
$stmt->bind_param("ss", $datum, $napomena);

if ($stmt->execute()) {
    echo json_encode(["poruka" => "Rok postavljen."]);
} else {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška pri unosu."]);
}

$stmt->close();
$conn->close();
