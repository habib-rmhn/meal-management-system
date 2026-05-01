<?php
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';
/* TOTAL MEALS */
$mQueryParts = [];
for ($i = 1; $i <= 31; $i++) {
    $mQueryParts[] = "SUM(CAST(day_$i AS UNSIGNED))";
}
$mQuery = "SELECT (" . implode(" + ", $mQueryParts) . ") AS total_meals FROM members";
$mResult = $conn->query($mQuery);
$tMeals = $mResult->fetch_assoc()['total_meals'] ?? 0;

/* TOTAL EXPENSES */
$eResult = $conn->query("SELECT SUM(CAST(amount AS DECIMAL(10,2))) AS total_expenses FROM expenses");
$tExpenses = $eResult->fetch_assoc()['total_expenses'] ?? 0;

/* CALCULATIONS */
$mRate = ($tMeals > 0) ? ($tExpenses / $tMeals) : 0;

/* STATUS LOGIC */
if ($mRate >= 50 && $mRate <= 55) {
    $stat = "Perfect";
} elseif ($mRate > 55 && $mRate <= 60) {
    $stat = "Balanced";
} elseif ($mRate >= 40 && $mRate < 50) {
    $stat = "Low";
} elseif ($mRate < 40) {
    $stat = "Very Low";
} elseif ($mRate > 60 && $mRate <= 70) {
    $stat = "Moderate";
} elseif ($mRate > 70 && $mRate <= 75) {
    $stat = "High";
} else {
    $stat = "Very High";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meal Count - Account - Eidhl</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard">
   <div class="top-section">
    <div class="header">
        <div class="header-title">
            <h1>Meal Count</h1>
            <span>January 2026</span>
        </div>

        <div class="header-left">
            <div class="nav-wrapper">
                
                <nav class="inline-nav" id="navMenu">
                    <a href="/">Manager Login
                        <i class="fa-solid fa-sign-in-alt"></i>
                    </a>
                </nav>
            </div>

        </div>
    </div>

    <div class="lookup-card">
        <div class="lookup-header">
            <h2>Check Your Meal Account</h2>
            <p>Enter your phone number to view your meal statistics and balance</p>
        </div>

        <div class="lookup-input-group">
            <input type="number" 
                   id="phone" 
                   class="phone-input"
                   placeholder="Enter your phone number"
                   onkeypress="if(event.key === 'Enter') fetchMember()">
            <button id="lookupBtn" class="lookup-btn" onclick="fetchMember()">
                <div id="loadingSpinner" class="loading" style="display:none;"></div>
                <span id="btnText">Check Account</span>
            </button>
        </div>

        <div id="error" class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <div class="error-text"></div>
        </div>

        <div id="result" class="results-container">
            <div class="results-header">
                <i class="fas fa-user-circle"></i>
                <h3>Account Overview</h3>
            </div>
            <div class="results-grid">
                <div class="result-item">
                    <div class="result-label">Member Name</div>
                    <div class="result-value" id="r-name">-</div>
                </div>
                <div class="result-item">
                    <div class="result-label">Amount Paid</div>
                    <div class="result-value">৳ <span id="r-paid">0.00</span></div>
                </div>
                <div class="result-item">
                    <div class="result-label">Total Meals</div>
                    <div class="result-value" id="r-meals">0</div>
                </div>
                <div class="result-item">
                    <div class="result-label">Current Meal Rate</div>
                    <div class="result-value">৳ <span id="r-rate">0.00</span></div>
                </div>
                <div class="result-item">
                    <div class="result-label">Total Cost</div>
                    <div class="result-value">৳ <span id="r-cost">0.00</span></div>
                </div>
                <div class="result-item">
                    <div class="result-label">Account Status</div>
                    <div id="r-due" class="status-badge">
                        <i class="fas fa-circle"></i>
                        <span>Pending Check</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
  </div>

    <div class="bottom-section">
      <div class="summary">
        <div class="summary-item">
            <h3>Targeted Meal Rate</h3>
            <p>৳ 60.00</p>
        </div>
        <div class="summary-item">
            <h3>Minimum Meals Per Member</h3>
            <p>NA</p>
        </div>
        <div class="summary-item">
            <h3>Meal Rate Status</h3>
            <p><?= $stat ?></p>
        </div>
    </div>
  </div>

</div>
<script src="/assets/js/account.js"></script>
</body>
</html>