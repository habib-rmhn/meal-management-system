<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid expense ID']);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'status' => 'success',
        'expense' => [
            'id' => $row['id'],
            'date' => $row['date'],
            'amount' => $row['amount'],
            'comment' => $row['comment']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Expense not found']);
}

$stmt->close();
$conn->close();
?>