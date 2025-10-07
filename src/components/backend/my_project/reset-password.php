<?php
$pdo = new PDO("mysql:host=localhost;dbname=project_db;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!$token || !$new_password) {
        echo json_encode(['success' => false, 'message' => 'Token i nova lozinka su obavezni.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Neispravan token.']);
        exit;
    }

    if (strtotime($user['reset_expires']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Token je istekao.']);
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$hashed_password, $user['id']]);

    echo json_encode(['success' => true, 'message' => 'Lozinka je uspješno promijenjena.']);
}
?>
