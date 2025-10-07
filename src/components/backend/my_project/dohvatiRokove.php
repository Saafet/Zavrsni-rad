<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška u vezi s bazom."]);
    exit;
}

$result = $conn->query("SELECT * FROM rokovi ORDER BY datum ASC");

$rokovi = [];
while ($row = $result->fetch_assoc()) {
    $rokovi[] = $row;
}

echo json_encode($rokovi);

$conn->close();
