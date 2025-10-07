<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['projekt_id']) || !isset($data['kriteriji']) || !isset($data['ukupno_bodova'])) {
    echo json_encode(["success" => false, "message" => "Podaci nisu potpuni"]);
    exit;
}

$projektId = $data['projekt_id'];
$ukupnoBodova = $data['ukupno_bodova'];
$kriteriji = $data['kriteriji'];

$conn = new mysqli("localhost", "root", "", "project_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB greška"]);
    exit;
}


$stmt = $conn->prepare("INSERT INTO ocjene (projekt_id, ukupno_bodova, datum) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $projektId, $ukupnoBodova);
$stmt->execute();
$ocjenaId = $stmt->insert_id;
$stmt->close();


foreach ($kriteriji as $k) {
    $naziv = $conn->real_escape_string($k['naziv']);
    $obvezan = $k['obvezan'] ? 1 : 0;
    $zadovoljen = $k['zadovoljen'] ? 1 : 0;
    $bod = $k['bod'];

    $conn->query("INSERT INTO ocjena_kriteriji (ocjena_id, naziv, obvezan, zadovoljen, bod)
                  VALUES ($ocjenaId, '$naziv', $obvezan, $zadovoljen, $bod)");
}


$result = $conn->query("
    SELECT GROUP_CONCAT(naziv SEPARATOR ', ') AS nezadovoljni_obvezni
    FROM ocjena_kriteriji
    WHERE ocjena_id = $ocjenaId AND obvezan = 1 AND zadovoljen = 0
");

$nezadovoljniString = '-';
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['nezadovoljni_obvezni'] !== null && $row['nezadovoljni_obvezni'] !== '') {
        $nezadovoljniString = $row['nezadovoljni_obvezni'];
    }
}


$updateStmt = $conn->prepare("UPDATE ocjene SET nezadovoljni_obvezni = ? WHERE id = ?");
$updateStmt->bind_param("si", $nezadovoljniString, $ocjenaId);
$updateStmt->execute();
$updateStmt->close();

echo json_encode(["success" => true]);

$conn->close();
?>
