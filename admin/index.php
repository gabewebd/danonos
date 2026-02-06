<?php
session_start();
include '../includes/db_connect.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Query: Email ONLY
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = isset($user['username']) ? $user['username'] : 'User';
            $_SESSION['full_name'] = isset($user['full_name']) ? $user['full_name'] : $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Danono's Admin</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Fredoka:wght@500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            /* 60% Black Overlay on top of login-bg.jpg */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/assets/img/danonos-login-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Barlow', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-card h1 {
            font-family: 'Fredoka', sans-serif;
            color: #EF7D32;
            font-size: 36px;
            margin-bottom: 8px;
        }

        .login-card .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Barlow', sans-serif;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #EF7D32;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 50px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
        }

        .password-toggle:hover {
            color: #431407;
        }

        .btn-login {
            width: 100%;
            padding: 16px 20px;
            background: #EF7D32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Barlow', sans-serif;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #d66a22;
            transform: translateY(-2px);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .back-link a {
            color: #EF7D32;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <img src="/assets/img/danonos-logo.jpg" alt="Danonos"
            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 15px;">
        <p class="subtitle">Admin Panel Login</p>

        <?php if ($error): ?>
            <div class="alert-error">
                <i class="ph ph-warning-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email..." required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="login-password" placeholder="Enter password..." required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="ph ph-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="ph ph-sign-in"></i> Login
            </button>
        </form>

        <div class="back-link">
            <a href="../index.php"><i class="ph ph-arrow-left"></i> Back to Website</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('login-password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ph ph-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'ph ph-eye';
            }
        }
    </script>

</body>

</html>