<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
    if (!$testId) {
        echo json_encode(["success" => false, "message" => "Nedostaje test_id"]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.test_id,
            r.ucenik_id,
            COALESCE(r.ime_ucenika, r.ime, 'Nepoznato') AS ime_ucenika,
            COALESCE(r.prezime_ucenika, r.prezime, '') AS prezime_ucenika,
            r.tocno,
            r.ukupno,
            r.procenat,
            r.ocjena,
            r.datum
        FROM rezultati_testova r
        WHERE r.test_id = ?
        ORDER BY r.datum DESC
    ");
    $stmt->execute([$testId]);
    $rezultati = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "rezultati" => $rezultati], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
