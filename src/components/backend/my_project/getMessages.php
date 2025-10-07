<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "", "project_db");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["poruka" => "Greška s bazom."]);
    exit;
}

$result = $mysqli->query("SELECT id, ime, email, poruka, vrijeme FROM kontakt_poruke ORDER BY vrijeme DESC");

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$mysqli->close();
