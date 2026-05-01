<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$date = isset($_POST['date']) ? $_POST['date'] : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : '';
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';

// Validation
if (empty($date) || empty($amount)) {
    echo json_encode(['status' => 'error', 'message' => 'Date and amount are required']);
    exit;
}

if (!is_numeric($amount) || floatval($amount) <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Amount must be a positive number']);
    exit;
}

if ($id > 0) {
    // Update existing expense
    $stmt = $conn->prepare("UPDATE expenses SET date = ?, amount = ?, comment = ? WHERE id = ?");
    $stmt->bind_param("sssi", $date, $amount, $comment, $id);
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Expense updated successfully!',
            'id' => $id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update expense: ' . $conn->error]);
    }
} else {
    // Insert new expense
    $stmt = $conn->prepare("INSERT INTO expenses (date, amount, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $date, $amount, $comment);
    $result = $stmt->execute();
    
    if ($result) {
        $newId = $stmt->insert_id;
        echo json_encode([
            'status' => 'success', 
            'message' => 'Expense added successfully!',
            'id' => $newId
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add expense: ' . $conn->error]);
    }
}

$stmt->close();
$conn->close();
?>