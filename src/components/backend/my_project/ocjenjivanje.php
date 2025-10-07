<?php
header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Nema podataka']);
    exit;
}

$projekt_id = $input['projekt_id'] ?? null;
$kriteriji = $input['kriteriji'] ?? [];

if (!$projekt_id || !is_array($kriteriji)) {
    echo json_encode(['success' => false, 'message' => 'Pogrešni podaci']);
    exit;
}


$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'project_db'; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Greška pri spajanju na bazu: ' . $conn->connect_error]);
    exit;
}


$conn->query("DELETE FROM ocjene WHERE projekt_id = $projekt_id");


$stmt = $conn->prepare("INSERT INTO ocjene (projekt_id, kriterij, bod, zadovoljen) VALUES (?, ?, ?, ?)");

foreach ($kriteriji as $k) {
    $naziv = $k['naziv'];
    $bod = $k['bod'];
    $zadovoljen = $k['zadovoljen'] ? 1 : 0;

    $stmt->bind_param("isii", $projekt_id, $naziv, $bod, $zadovoljen);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'message' => 'Ocjene spremljene']);
