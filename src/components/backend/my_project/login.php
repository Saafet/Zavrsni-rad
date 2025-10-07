<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$dbname = "project_db";
$dbUser = "root";
$dbPass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"]) || !isset($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

$username = $data["username"];
$password = $data["password"];

$sql = "
SELECT u.id, u.username, u.password, r.role_name AS role
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
WHERE u.username = :username
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user["password"])) {
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"] ?? 'user'
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
}
?>
