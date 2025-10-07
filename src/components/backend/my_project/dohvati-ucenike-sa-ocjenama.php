<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$mysqli = new mysqli("localhost", "root", "", "project_db");

if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$query = "
SELECT 
    p.id AS id,
    p.ime,
    p.prezime,
    t.naziv AS tema_naziv,
    o.ukupno_bodova,
    o.nezadovoljni_obvezni
FROM projekti p
JOIN teme t ON p.tema_id = t.id
LEFT JOIN ocjene o ON p.id = o.projekt_id
";

$result = $mysqli->query($query);

$ucenici = [];

while ($row = $result->fetch_assoc()) {
    
    if (is_null($row['nezadovoljni_obvezni']) || $row['nezadovoljni_obvezni'] === '') {
        $row['nezadovoljni_obvezni'] = '-';
    }
    $ucenici[] = $row;
}

echo json_encode(["success" => true, "ucenici" => $ucenici]);
?>
