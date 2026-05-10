<?php
// ── CORS ──
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db.php';

// ── READ JSON BODY ──
$data     = json_decode(file_get_contents("php://input"), true);
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

// ── VALIDATE ──
if (!$username || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
    exit();
}

// ── FIND USER ──
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    exit();
}

// ── VERIFY PASSWORD ──
if (password_verify($password, $user['password'])) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Login successful!',
        'username' => $user['username'],
        'user_id'  => $user['id']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
