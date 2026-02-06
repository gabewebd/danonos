<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: If user is not logged in, kick them out!
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Session Timeout: 30 minutes of inactivity
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session has expired due to inactivity
    session_unset();
    session_destroy();
    header("Location: index.php?timeout=1");
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Danonos</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background-color: #f4f4f4;
        }

        .admin-nav {
            background: #3E2723;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }

        .admin-nav a:hover {
            text-decoration: underline;
        }

        .content-wrapper {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <div class="admin-nav">
        <div class="brand">
            <img src="/assets/img/danonos-logo.jpg" alt="Danonos" class="rounded-circle"
                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
        </div>
        <div class="links">
            <a href="dashboard.php">Dashboard</a>
            <a href="dashboard.php?tab=menu">Menu Items</a>
            <a href="post-create.php">Write New Blog</a>
            <a href="../index.php" target="_blank">View Live Site</a>
            <a href="logout.php" style="color: #ffcccc;">Logout</a>
        </div>
    </div>

    <div class="content-wrapper">