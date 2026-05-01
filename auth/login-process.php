<?php
session_start();

require "db.php";
register_shutdown_function(function () use ($conn) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
});

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use DeviceDetector\DeviceDetector;

header("Content-Type: application/json");

/* SECURITY SETTINGS */
$MAX_ATTEMPTS = 5;
$BLOCK_TIME = 30 * 60; // 30 minutes

$_SESSION['login_attempts'] ??= 0;
$_SESSION['first_attempt_time'] ??= time();
$_SESSION['blocked_until'] ??= 0;

/* BLOCK CHECK */
if (time() < $_SESSION['blocked_until']) {
    echo json_encode([
        "status" => "blocked",
        "message" => "Too many attempts! Try later."
    ]);
    exit;
}

/* POST ONLY */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$secretKey = trim($_POST["secretKey"] ?? "");

if ($secretKey === "") {
    echo json_encode(["status" => "error", "message" => "Secret key is required"]);
    exit;
}

/* FETCH MANAGER */
$stmt = $conn->prepare("SELECT id, username, secret_key FROM manager LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($secretKey, $row["secret_key"])) {

        /* =========================
           SUCCESS LOGIN
        ========================= */

        session_regenerate_id(true);
        $_SESSION["manager_id"] = $row["id"];
        $_SESSION["manager_username"] = $row["username"];
        $_SESSION["login_attempts"] = 0;
        $_SESSION["first_attempt_time"] = time();
        $_SESSION["blocked_until"] = 0;

        /* LOGIN LOGGING */

        /* DEVICE */
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

        /* IP */
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

        /* LOCATION */
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

        /* INSERT LOG */
        $logSql = "INSERT INTO users_logs
            (member_name, device, location, ip_address)
            VALUES (?, ?, ?, ?)";

        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param(
            "ssss",
            $row['username'],   // manager username stored as name
            $deviceInfo,
            $location,
            $ipAddress
        );
        $logStmt->execute();
        $logStmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "Login successful. Redirecting..."
        ]);
        exit;
    }
}

/* FAILED ATTEMPT */
$_SESSION["login_attempts"]++;

if (time() - $_SESSION["first_attempt_time"] > 1800) {
    $_SESSION["login_attempts"] = 1;
    $_SESSION["first_attempt_time"] = time();
}

if ($_SESSION["login_attempts"] >= $MAX_ATTEMPTS) {
    $_SESSION["blocked_until"] = time() + $BLOCK_TIME;

    echo json_encode([
        "status" => "blocked",
        "message" => "Too many attempts! Try later."
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Invalid secret key."
]);