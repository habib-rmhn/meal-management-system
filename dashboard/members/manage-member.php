<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'add') {
    $name    = isset($_POST['name'])   ? trim($_POST['name'])   : '';
    $number  = isset($_POST['number']) ? trim($_POST['number']) : '';

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Name is required']);
        exit;
    }

    // Build the INSERT with all 31 day columns defaulting to empty string
    $placeholders = str_repeat("'',", 31);
    $placeholders = rtrim($placeholders, ',');

    $stmt = $conn->prepare("INSERT INTO members (name, number, payment, day_1, day_2, day_3, day_4, day_5, day_6, day_7, day_8, day_9, day_10, day_11, day_12, day_13, day_14, day_15, day_16, day_17, day_18, day_19, day_20, day_21, day_22, day_23, day_24, day_25, day_26, day_27, day_28, day_29, day_30, day_31) VALUES (?, ?, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')");
    $stmt->bind_param("ss", $name, $number);

    if ($stmt->execute()) {
        $newId = $stmt->insert_id;
        echo json_encode([
            'status'  => 'success',
            'message' => 'Member added successfully!',
            'member'  => ['id' => $newId, 'name' => $name, 'number' => $number]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add member: ' . $conn->error]);
    }

    $stmt->close();

} elseif ($action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid member ID']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Member deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Member not found or already deleted']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete member: ' . $conn->error]);
    }

    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}

$conn->close();
?>