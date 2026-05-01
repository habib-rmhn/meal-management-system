<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = (int)$data['id'];
$field = preg_replace('/[^a-z0-9_]/', '', $data['field']);
$value = trim($data['value']);

// Allow only valid columns
$allowed = ['payment'];
for ($i = 1; $i <= 31; $i++) {
    $allowed[] = "day_$i";
}

if (!in_array($field, $allowed)) {
    http_response_code(403);
    exit;
}

$stmt = $conn->prepare("UPDATE members SET `$field`=? WHERE id=?");
$stmt->bind_param("si", $value, $id);
$stmt->execute();

echo json_encode(['status' => 'ok']);