<?php
// HOSTINGER PRODUCTION CONFIGURATION
// This file is edited directly on the server via File Manager.

// 1. TiDB Cloud Credentials (Hardcoded here because this file stays on the server)
$hostname = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com"; // Your TiDB Host
$username = "2wZmDK3nzviaCyQ.root"; // Your TiDB User
$password = "CDgcSPEK1jhXdCJc"; //
$database = "test"; // Your Database Name
$port     = 4000;

$conn = mysqli_init();

// 2. SSL/TLS Setting (Crucial for TiDB on Hostinger)
// We use NULL to use Hostinger's built-in CA certificates
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); 

// 3. Connect
$success = mysqli_real_connect($conn, $hostname, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$success) {
    // If connection fails, show error (Disable this for final submission for security)
    die("Database Connection Error: " . mysqli_connect_error());
}
?>