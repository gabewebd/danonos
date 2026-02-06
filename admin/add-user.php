<?php
session_start();
include '../includes/db_connect.php';

// Security Check: Only Admins can run this code
if (isset($_POST['add_user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {

    // 1. Get data from form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hashing
    $role = $_POST['role'];

    // 2. Prepare SQL command (Prevents hacking)
    // Removed username, added full_name
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("ssss", $full_name, $email, $password, $role);

    // 3. Execute and Redirect
    if ($stmt->execute()) {
        // Success! Go back to dashboard immediately
        header("Location: dashboard.php?msg=UserAdded");
        exit;
    } else {
        // If something goes wrong (like duplicate email)
        echo "Error adding user: " . $conn->error;
    }

} else {
    // If a non-admin tries to access this page, kick them out
    header("Location: dashboard.php");
    exit;
}
?>