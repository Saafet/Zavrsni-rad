<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    $ime = isset($data['ime']) ? trim($data['ime']) : '';
    $prezime = isset($data['prezime']) ? trim($data['prezime']) : '';
    $test_id = isset($data['test_id']) ? (int)$data['test_id'] : 0;
    $odgovori = isset($data['odgovori']) ? $data['odgovori'] : [];

    if (!$ime || !$prezime || !$test_id || empty($odgovori)) {
        throw new Exception("Nedostaju podaci (ime, prezime, test_id ili odgovori).");
    }

    $puno_ime = $ime . ' ' . $prezime;
    $stmt = $pdo->prepare("SELECT id FROM korisnici WHERE ime = ?");
    $stmt->execute([$puno_ime]);
    $ucenik_id = $stmt->fetchColumn();

    if (!$ucenik_id) {
        $ins = $pdo->prepare("INSERT INTO korisnici (ime) VALUES (?)");
        $ins->execute([$puno_ime]);
        $ucenik_id = $pdo->lastInsertId();
    }

    $tocno = 0;
    $ukupno = count($odgovori);

    $ids = array_map(function ($o) { return (int)$o['pitanje_id']; }, $odgovori);
    if (empty($ids)) throw new Exception("Nema pitanja u predaji.");
    $in = implode(',', array_fill(0, count($ids), '?'));

    $q = $pdo->prepare("SELECT id, tip, odgovor FROM pitanja WHERE id IN ($in)");
    $q->execute($ids);
    $map = [];
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        $map[(int)$row['id']] = $row;
    }

    foreach ($odgovori as $o) {
        $pitanje_id = (int)$o['pitanje_id'];
        $odgovorKorisnika = isset($o['odgovor']) ? $o['odgovor'] : null;

        if (!isset($map[$pitanje_id])) {
            continue;
        }

        $tip = $map[$pitanje_id]['tip'];  
        $tacan = $map[$pitanje_id]['odgovor'];  

        $is_correct = 0;
        if ($tip === 'truefalse') {
            $is_correct = ((int)$odgovorKorisnika === (int)$tacan) ? 1 : 0;
        } else {
            $is_correct = (mb_strtolower(trim((string)$odgovorKorisnika)) === mb_strtolower(trim((string)$tacan))) ? 1 : 0;
        }

        if ($is_correct) $tocno++;
    }

    $procenat = $ukupno > 0 ? round(($tocno / $ukupno) * 100, 2) : 0.00;

    if ($procenat >= 90) $ocjena = 5.0;
    elseif ($procenat >= 70) $ocjena = 4.0;
    elseif ($procenat >= 50) $ocjena = 3.0;
    elseif ($procenat >= 30) $ocjena = 2.0;
    else $ocjena = 1.0;
    $insr = $pdo->prepare("
        INSERT INTO rezultati_testova
            (test_id, ime, prezime, ucenik_id, tocno, ukupno, procenat, ocjena, ime_ucenika, prezime_ucenika)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insr->execute([$test_id, $ime, $prezime, $ucenik_id, $tocno, $ukupno, $procenat, $ocjena, $ime, $prezime]);

    echo json_encode([
        "status" => "success",
        "tocno" => $tocno,
        "ukupno" => $ukupno,
        "procenat" => $procenat,
        "ocjena" => $ocjena
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
