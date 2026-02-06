<?php
session_start();
include '../includes/db_connect.php';

// Security Check: Only Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Get User ID
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($user_id == 0) {
    header("Location: dashboard.php?tab=users");
    exit;
}

// Fetch existing user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: dashboard.php?tab=users&msg=not_found");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // If password provided, update it
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $full_name, $email, $password, $role, $user_id);
    } else {
        // Keep existing password
        $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $full_name, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php?tab=users&msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Danono's Admin</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Fredoka:wght@500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-family: 'Fredoka', sans-serif;
            color: var(--dark);
        }

        .form-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Barlow', sans-serif;
            font-size: 15px;
        }

        .form-group small {
            color: var(--gray);
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Barlow', sans-serif;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--gray);
            text-decoration: none;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: #EF7D32;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            font-weight: 600;
            margin: 0 auto 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="page-header">
            <h1>Edit User</h1>
            <p style="color: var(--gray); margin-top: 5px;">Update user information</p>
        </div>

        <div class="form-card">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current">
                    <small>Only fill this if you want to change the password</small>
                </div>

                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" required>
                        <option value="editor" <?php echo $user['role'] == 'editor' ? 'selected' : ''; ?>>Editor</option>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" name="update" class="btn-submit">
                    <i class="ph ph-floppy-disk"></i> Save Changes
                </button>
            </form>
        </div>

        <a href="dashboard.php?tab=users" class="back-link">
            <i class="ph ph-arrow-left"></i> Back to Users
        </a>
    </div>
</body>

</html>