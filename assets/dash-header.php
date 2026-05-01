<?php
session_start();
if (!isset($_SESSION['manager_id'])) {
    header("Location: /");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meal Count - <?php echo isset($pageTitle) ? $pageTitle : "Portal"; ?> - Eidhl</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo isset($cstlClass) ? $cstlClass : "No-Custom-StyleSheet"; ?>">
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
                <button class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <nav class="inline-nav" id="navMenu">
                    <a href="/dashboard" class="<?php echo isset($dasClass) ? $dasClass : "Dumb"; ?>">Dashboard</a>
                    <a href="/dashboard/members" class="<?php echo isset($memClass) ? $memClass : "Dumb"; ?>">Members</a>
                    <a href="/dashboard/expenses" class="<?php echo isset($expClass) ? $expClass : "Dumb"; ?>">Expenses</a>
                    <a href="/account" target="_blank">Account <sup style="font-size: 10px;"><i class="fa-solid fa-arrow-up-right-from-square"></i></sup></a>
                    <a href="/dashboard/user-logs" class="<?php echo isset($logClass) ? $logClass : "Dumb"; ?>">Logs</a>
                    <a href="/auth/logout" title="Logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </nav>
            </div>

        </div>
    </div>