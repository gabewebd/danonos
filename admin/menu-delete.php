<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get Item ID
$item_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($item_id > 0) {
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
}

// Redirect back
header("Location: dashboard.php?tab=menu");
exit;
?>