<?php
session_start();

// Security Check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include '../includes/db_connect.php';

// Validate that an ID was provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = (int) $_GET['id'];

// Use prepared statement to prevent SQL injection
// Fixed: Changed table from 'blogs' to 'posts'
// Fixed: Using $conn instead of $pdo
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect back to dashboard
header('Location: dashboard.php');
exit;
?>