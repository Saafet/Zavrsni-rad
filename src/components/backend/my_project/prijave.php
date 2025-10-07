<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "", "project_db");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Greška pri povezivanju s bazom."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    exit;
}

switch ($method) {
    case 'GET':
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Nedostaje user_id."]);
            exit;
        }
        $stmt = $mysqli->prepare("SELECT * FROM prijave WHERE ucenik_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $prijave = [];
        while ($row = $result->fetch_assoc()) {
            $prijave[] = $row;
        }
        echo json_encode(["success" => true, "data" => $prijave]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['user_id'], $data['rok_id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Nedostaju podaci."]);
            exit;
        }
        $stmt = $mysqli->prepare("INSERT IGNORE INTO prijave (ucenik_id, rok_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $data['user_id'], $data['rok_id']);
        $stmt->execute();
        echo json_encode(["success" => true]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['user_id'], $data['rok_id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Nedostaju podaci."]);
            exit;
        }
        $stmt = $mysqli->prepare("DELETE FROM prijave WHERE ucenik_id = ? AND rok_id = ?");
        $stmt->bind_param("ii", $data['user_id'], $data['rok_id']);
        $stmt->execute();
        echo json_encode(["success" => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Metoda nije dozvoljena."]);
}
?>
