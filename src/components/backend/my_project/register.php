<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
$host = "localhost";
$dbname = "project_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"]) || !isset($data["email"]) || !isset($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

$username = $data["username"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);

$sql = "SELECT * FROM users WHERE username = :username OR email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username);
$stmt->bindParam(":email", $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(["status" => "error", "message" => "Username or email already exists"]);
    exit();
}

$sql = "INSERT INTO users (username, email, password, user_role, status) VALUES (:username, :email, :password, 'user', 'active')";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":password", $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "User registration failed"]);
}
?>
