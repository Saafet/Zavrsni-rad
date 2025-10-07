<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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
        $result = $mysqli->query("SELECT * FROM rokovi ORDER BY datum ASC, vrijeme ASC");
        $rokovi = [];
        while ($row = $result->fetch_assoc()) {
            $rokovi[] = $row;
        }
        echo json_encode(["success" => true, "data" => $rokovi]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['datum'], $data['vrijeme'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Nedostaju podaci."]);
            exit;
        }
        $stmt = $mysqli->prepare("INSERT INTO rokovi (datum, vrijeme, napomena) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['datum'], $data['vrijeme'], $data['napomena']);
        $stmt->execute();
        echo json_encode(["success" => true]);
        break;

    case 'PUT':
        parse_str($_SERVER['QUERY_STRING'], $params);
        $id = $params['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID nije poslan."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("UPDATE rokovi SET datum = ?, vrijeme = ?, napomena = ? WHERE id = ?");
        $stmt->bind_param("sssi", $data['datum'], $data['vrijeme'], $data['napomena'], $id);
        $stmt->execute();
        echo json_encode(["success" => true]);
        break;

    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $params);
        $id = $params['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID nije poslan."]);
            exit;
        }
        $stmt = $mysqli->prepare("DELETE FROM rokovi WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["success" => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Metoda nije dozvoljena."]);
}
?>
