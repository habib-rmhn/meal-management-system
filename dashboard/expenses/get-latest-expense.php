<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM expenses ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
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
    echo json_encode(['status' => 'error', 'message' => 'No expenses found']);
}

$conn->close();
?>