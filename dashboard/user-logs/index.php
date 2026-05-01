<?php
$pageTitle = "User Logs";
$logClass = "active";
$cstlClass = "/assets/css/logs.css";

require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-header.php';
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

/* FETCH EXPENSES */
$result = $conn->query("SELECT * FROM users_logs ORDER BY id DESC");
?>

<div class="table-wrapper">
    <table class="log-table">
        <thead>
            <tr>
                <th></th>
                <th>SL</th>
                <th>Name</th>
                <th>Date & Time</th>
                <th>Device</th>
                <th>Location</th>
                <th>IP Address</th>
            </tr>
        </thead>

        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            $sl = 1;
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td></td>
                    <td><?= $sl++; ?></td>
                    <td><?= htmlspecialchars($row['member_name']); ?></td>
                    <td><?= date('j M Y, H:i', strtotime($row['checked_at'])); ?></td>
                    <td><?= htmlspecialchars($row['device']); ?></td>
                    <td><?= htmlspecialchars($row['location']); ?></td>
                    <td><?= htmlspecialchars($row['ip_address']); ?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="6" style="text-align:center;">No expenses found</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-footer.php';
?>