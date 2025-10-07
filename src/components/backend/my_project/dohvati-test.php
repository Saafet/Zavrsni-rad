<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Provjera parametra
if (!isset($_GET['test_id'])) {
    echo json_encode(["success" => false, "message" => "Nedostaje test_id"]);
    exit;
}

$test_id = (int)$_GET['test_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Dohvati naziv testa
    $stmt = $pdo->prepare("SELECT naziv FROM testovi WHERE id = ?");
    $stmt->execute([$test_id]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        echo json_encode(["success" => false, "message" => "Test nije pronađen"]);
        exit;
    }

    // Dohvati samo pitanja povezana sa ovim testom preko junction table
    $sql = "SELECT p.id, p.naziv, p.tip
            FROM pitanja p
            JOIN test_pitanja tp ON p.id = tp.pitanje_id
            WHERE tp.test_id = ?
            ORDER BY p.id ASC";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute([$test_id]);
    $pitanja = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "naziv"   => $test['naziv'],
        "pitanja" => $pitanja
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Greška baze: " . $e->getMessage()]);
}
