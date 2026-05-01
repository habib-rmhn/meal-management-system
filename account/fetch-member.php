<?php
header('Content-Type: application/json');

require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use DeviceDetector\DeviceDetector;

/* INPUT */
$phone = $_POST['phone'] ?? '';

if (!$phone) {
    echo json_encode(['error' => 'Invalid phone number']);
    exit;
}

/* FETCH MEMBER */
$sql = "SELECT * FROM members WHERE number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Member not found']);
    exit;
}

$member = $result->fetch_assoc();

/* TOTAL MEALS (INDIVIDUAL) */
$totalMeals = 0;
for ($i = 1; $i <= 31; $i++) {
    $totalMeals += (int)($member["day_$i"] ?? 0);
}

/* TOTAL EXPENSES */
$exp = $conn->query("SELECT SUM(CAST(amount AS DECIMAL(10,2))) AS total FROM expenses");
$totalExpenses = $exp->fetch_assoc()['total'] ?? 0;

/* TOTAL MEALS (ALL MEMBERS) */
$mealSum = [];
for ($i = 1; $i <= 31; $i++) {
    $mealSum[] = "SUM(CAST(day_$i AS UNSIGNED))";
}
$q = "SELECT (" . implode(" + ", $mealSum) . ") AS total FROM members";
$tm = $conn->query($q)->fetch_assoc()['total'] ?? 0;

/* MEAL RATE */
$mealRate = ($tm > 0) ? ($totalExpenses / $tm) : 0;

/* COST CALCULATION */
$totalCost = $mealRate * $totalMeals;
$paid = (float)$member['payment'];
$diff = $paid - $totalCost;

// Check if difference is within 0 to 2 ৳ range (absolute value)
if ($diff >= 0 && $diff <= 2.0) {
    // Positive difference but within 0-2 ৳ range
    $status = "<span class='primary'>Clear</span>";
} elseif ($diff < 0 && abs($diff) <= 2.0) {
    // Negative difference but within 0-2 ৳ range
    $status = "<span class='primary'>Clear</span>";
} elseif ($diff > 2.0) {
    $status = "<span class='success'>Refund ৳ " . number_format($diff) . "</span>";
} elseif ($diff < -2.0) {
    $status = "<span class='danger'>Due ৳ " . number_format(abs($diff)) . "</span>";
} else {
    $status = "<span class='primary'>Clear</span>";
}

/* =========================
   CHECKING LOG SECTION
========================= */

/* DEVICE DETECTION */
$dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT'] ?? '');
$dd->parse();

$deviceType = $dd->isMobile()
    ? 'Mobile'
    : ($dd->isTablet() ? 'Tablet' : 'Desktop');

$os = $dd->getOs();
$client = $dd->getClient();

$deviceInfo = $deviceType;

if (!empty($os['name'])) {
    $deviceInfo .= ' > ' . $os['name'];
}

if (!empty($client['name'])) {
    $deviceInfo .= ' > ' . $client['name'];
}

/* IP ADDRESS */
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

/* LOCATION (ip-api.com) */
$location = 'Unknown';

if ($ipAddress !== 'Unknown') {
    $apiUrl = "http://ip-api.com/json/{$ipAddress}";
    $locationData = @file_get_contents($apiUrl);
    $locationJson = $locationData ? json_decode($locationData, true) : null;

    if ($locationJson && ($locationJson['status'] ?? '') === 'success') {
        $city = $locationJson['city'] ?? '';
        $country = $locationJson['country'] ?? '';
        $location = trim($city . ($city && $country ? ', ' : '') . $country);
    }
}

/* INSERT CHECK LOG */
$logSql = "INSERT INTO users_logs
    (member_name, device, location, ip_address)
    VALUES (?, ?, ?, ?)";

$logStmt = $conn->prepare($logSql);
$logStmt->bind_param(
    "ssss",
    $member['name'],
    $deviceInfo,
    $location,
    $ipAddress
);
$logStmt->execute();
$logStmt->close();

/* RESPONSE */
echo json_encode([
    'name'   => $member['name'],
    'paid'  => number_format($paid),
    'meals' => $totalMeals,
    'rate'  => number_format($mealRate, 2),
    'cost'  => number_format($totalCost),
    'status'=> $status
]);

$stmt->close();
$conn->close();
