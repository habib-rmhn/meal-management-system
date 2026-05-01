<?php
$pageTitle = "Overview";
$dasClass = "active";

require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-header.php';
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

// Get all members with their data
$membersQuery = $conn->query("SELECT * FROM members ORDER BY id");
$members = [];
$totalMembers = 0;
$totalMoney = 0;
$totalMeals = 0;

while ($member = $membersQuery->fetch_assoc()) {
    // Calculate total meals for this member
    $memberMeals = 0;
    for ($i = 1; $i <= 31; $i++) {
        $memberMeals += (int)$member["day_$i"];
    }
    $member['total_meals'] = $memberMeals;
    
    // Store member data
    $members[] = $member;
    $totalMembers++;
    $totalMoney += (float)$member['payment'];
    $totalMeals += $memberMeals;
}

/* TOTAL EXPENSES */
$expenseResult = $conn->query("SELECT SUM(CAST(amount AS DECIMAL(10,2))) AS total_expenses FROM expenses");
$totalExpenses = $expenseResult->fetch_assoc()['total_expenses'] ?? 0;

/* CALCULATIONS */
$mealRate = ($totalMeals > 0) ? ($totalExpenses / $totalMeals) : 0;
$moneyLeft = $totalMoney - $totalExpenses;

/* STATUS LOGIC */
if ($mealRate >= 50 && $mealRate <= 55) {
    $status = "Perfect";
} elseif ($mealRate > 55 && $mealRate <= 60) {
    $status = "Balanced";
} elseif ($mealRate >= 40 && $mealRate < 50) {
    $status = "Low";
} elseif ($mealRate < 40) {
    $status = "Very Low";
} elseif ($mealRate > 60 && $mealRate <= 70) {
    $status = "Moderate";
} elseif ($mealRate > 70 && $mealRate <= 75) {
    $status = "High";
} else {
    $status = "Very High";
}
?>

<div class="stats-grid">

    <div class="card">
        <div class="card-title">Total Members</div>
        <div class="card-value primary"><?= $totalMembers ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Money Collected</div>
        <div class="card-value success">৳ <?= number_format($totalMoney) ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Meals</div>
        <div class="card-value primary"><?= $totalMeals ?></div>
    </div>

    <div class="card">
        <div class="card-title">Meal Rate</div>
        <div class="card-value warning">৳ <?= number_format($mealRate, 2) ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Expenses</div>
        <div class="card-value danger">৳ <?= number_format($totalExpenses) ?></div>
    </div>

    <div class="card">
        <div class="card-title">Money Left</div>
        <div class="card-value <?= $moneyLeft >= 1000 ? 'success' : 'danger' ?>">
            ৳ <?= number_format($moneyLeft) ?>
        </div>
    </div>

</div>

<!-- Individual Member Cards Section -->
<div class="members-stats-section">
    <h2 style="margin: 30px 0 20px 0; color: var(--text-dark); font-size: 22px;">Member Details</h2>
    
    <div class="members-grid">
        <?php foreach ($members as $member): 
            $memberMeals = $member['total_meals'];
            $memberPaid = (float)$member['payment'];
            $memberCost = $memberMeals * $mealRate;
            $balance = $memberPaid - $memberCost;
            
            // Updated balance logic
            $absBalance = abs($balance);
            if ($absBalance <= 2.0) {
                $balanceClass = 'primary';
                $balanceText = 'Status';
                $balanceSymbol = '(Clear)';
            } else {
                if ($balance > 0) {
                    $balanceClass = 'success';
                    $balanceText = 'Refund';
                    $balanceSymbol = '↑';
                } elseif ($balance < 0) {
                    $balanceClass = 'danger';
                    $balanceText = 'Due';
                    $balanceSymbol = '↓';
                } else {
                    $balanceClass = 'clear';
                    $balanceText = 'Status';
                    $balanceSymbol = '(Clear)';
                }
            }
        ?>
        <div class="member-card">
            <div class="member-card-header">
                <h3><?= htmlspecialchars($member['name']) ?></h3>
                <span class="member-id"><?= $member['number'] ?></span>
            </div>
            
            <div class="member-card-body">
                <div class="member-stat">
                    <span class="stat-label">Amount Paid:</span>
                    <span class="stat-value primary">৳ <?= number_format($memberPaid) ?></span>
                </div>
                
                <div class="member-stat">
                    <span class="stat-label">Total Meals:</span>
                    <span class="stat-value primary"><?= $memberMeals ?></span>
                </div>
                
                <div class="member-stat">
                    <span class="stat-label">Total Cost:</span>
                    <span class="stat-value warning">৳ <?= number_format($memberCost, 2) ?></span>
                </div>
                
                <div class="member-stat">
                    <span class="stat-label"><?= $balanceText ?>:</span>
                    <span class="stat-value <?= $balanceClass ?>">
                        ৳ <?= number_format($absBalance) ?>
                        <?= $balanceSymbol ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
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
            <p><?= $status ?></p>
        </div>

    </div>
</div>

<?php
$conn->close();
require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-footer.php';
?>