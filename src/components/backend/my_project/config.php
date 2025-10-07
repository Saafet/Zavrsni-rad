<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"));
$username = $data->username;
$password = $data->password;

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Pogrešno korisničko ime ili lozinka"]);
}

$conn->close();
?>
