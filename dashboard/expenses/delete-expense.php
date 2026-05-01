<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid expense ID']);
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Expense deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Expense not found or already deleted']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete expense: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>