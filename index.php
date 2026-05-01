<?php
session_start();
if (isset($_SESSION['manager_id'])) {
    header("Location: /dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Count - Manager Login - Eidhl</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <h1>Meal Management</h1>
                <p>Manager Access Only</p>
            </div>
            
            <form id="loginForm">
                <div class="input-group">
                    <label for="secretKey"><i class="fas fa-key"></i> Secret Key</label>
                    <input type="password" id="secretKey" name="secretKey" placeholder="Enter manager secret key">
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                
                <div class="login-footer">
                    <a href="/account" class="member-link">
                        <i class="fa-solid fa-magnifying-glass"></i> Account Lookup
                    </a>
                </div>
            </form>
            
            <div id="message" class="message">
                <i class="fas fa-exclamation-circle"></i>
                <div class="message-text"></div>
            </div>
        </div>
    </div>
    
    <script src="/assets/js/login-script.js"></script>
</body>
</html>