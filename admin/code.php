<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'editor';

// ========================
// SAVE NEW USER
// ========================
if (isset($_POST['save_user']) && $role === 'admin') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $password, $user_role);
    $stmt->execute();

    header("Location: dashboard.php?tab=users&msg=user_added");
    exit;
}

// ========================
// UPDATE USER
// ========================
if (isset($_POST['update_user']) && $role === 'admin') {
    $user_id = (int) $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $user_role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $full_name, $email, $password, $user_role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $full_name, $email, $user_role, $user_id);
    }
    $stmt->execute();

    header("Location: dashboard.php?tab=users&msg=user_updated");
    exit;
}

// ========================
// SAVE NEW MENU ITEM
// ========================
if (isset($_POST['save_menu'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $alt_text = isset($_POST['alt_text']) ? $_POST['alt_text'] : '';
    $image_filename = "";

    // Image Upload (Optimized: 1000px, 75% compression)
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $uploadDir = '../uploads/';

        // Clean filename
        $rawName = pathinfo($file['name'], PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $rawName);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim(strtolower($cleanName), '-');

        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $gdAvailable = function_exists('imagecreatefromjpeg');
        $finalExt = $gdAvailable ? 'jpg' : $origExt;

        // Handle duplicates
        $fileName = $cleanName . '.' . $finalExt;
        $counter = 1;
        while (file_exists($uploadDir . $fileName)) {
            $fileName = $cleanName . '-' . $counter . '.' . $finalExt;
            $counter++;
        }
        $destination = $uploadDir . $fileName;

        // Process with GD
        if ($gdAvailable) {
            $max_width = 1000;
            $quality = 75;

            list($width, $height) = getimagesize($file['tmp_name']);
            $ratio = $width / $height;

            if ($width > $max_width) {
                $newWidth = $max_width;
                $newHeight = $max_width / $ratio;
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $src = null;
            if ($origExt == 'jpg' || $origExt == 'jpeg') {
                $src = imagecreatefromjpeg($file['tmp_name']);
            } elseif ($origExt == 'png') {
                $src = imagecreatefrompng($file['tmp_name']);
                $bg = imagecreatetruecolor($width, $height);
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $src, 0, 0, 0, 0, $width, $height);
                $src = $bg;
            } elseif ($origExt == 'gif') {
                $src = imagecreatefromgif($file['tmp_name']);
            }

            if ($src) {
                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($dst, $destination, $quality);
                imagedestroy($src);
                imagedestroy($dst);
                $image_filename = $fileName;
            } else {
                move_uploaded_file($file['tmp_name'], $destination);
                $image_filename = $fileName;
            }
        } else {
            move_uploaded_file($file['tmp_name'], $destination);
            $image_filename = $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category, image, alt_text) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $name, $description, $price, $category, $image_filename, $alt_text);
    $stmt->execute();

    header("Location: dashboard.php?tab=menu&msg=item_added");
    exit;
}

// ========================
// UPDATE MENU ITEM
// ========================
if (isset($_POST['update_menu'])) {
    $menu_id = (int) $_POST['menu_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $alt_text = isset($_POST['alt_text']) ? $_POST['alt_text'] : '';

    // Get existing image
    $existing = $conn->query("SELECT image FROM menu_items WHERE id=$menu_id")->fetch_assoc();
    $image_filename = $existing['image'];

    // Image Upload (if new image provided)
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $uploadDir = '../uploads/';

        $rawName = pathinfo($file['name'], PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $rawName);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim(strtolower($cleanName), '-');

        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $gdAvailable = function_exists('imagecreatefromjpeg');
        $finalExt = $gdAvailable ? 'jpg' : $origExt;

        $fileName = $cleanName . '.' . $finalExt;
        $counter = 1;
        while (file_exists($uploadDir . $fileName)) {
            $fileName = $cleanName . '-' . $counter . '.' . $finalExt;
            $counter++;
        }
        $destination = $uploadDir . $fileName;

        if ($gdAvailable) {
            $max_width = 1000;
            $quality = 75;

            list($width, $height) = getimagesize($file['tmp_name']);
            $ratio = $width / $height;

            if ($width > $max_width) {
                $newWidth = $max_width;
                $newHeight = $max_width / $ratio;
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $src = null;
            if ($origExt == 'jpg' || $origExt == 'jpeg') {
                $src = imagecreatefromjpeg($file['tmp_name']);
            } elseif ($origExt == 'png') {
                $src = imagecreatefrompng($file['tmp_name']);
                $bg = imagecreatetruecolor($width, $height);
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $src, 0, 0, 0, 0, $width, $height);
                $src = $bg;
            } elseif ($origExt == 'gif') {
                $src = imagecreatefromgif($file['tmp_name']);
            }

            if ($src) {
                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($dst, $destination, $quality);
                imagedestroy($src);
                imagedestroy($dst);
                $image_filename = $fileName;
            } else {
                move_uploaded_file($file['tmp_name'], $destination);
                $image_filename = $fileName;
            }
        } else {
            move_uploaded_file($file['tmp_name'], $destination);
            $image_filename = $fileName;
        }
    }

    $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, category=?, image=?, alt_text=? WHERE id=?");
    $stmt->bind_param("ssdsssi", $name, $description, $price, $category, $image_filename, $alt_text, $menu_id);
    $stmt->execute();

    header("Location: dashboard.php?tab=menu&msg=item_updated");
    exit;
}

// ========================
// TOGGLE MENU VISIBILITY
// ========================
if (isset($_POST['toggle_visibility'])) {
    $id = (int) $_POST['toggle_visibility_id'];
    $current = (int) $_POST['current_status'];
    $new_status = ($current == 1) ? 0 : 1;

    $stmt = $conn->prepare("UPDATE menu_items SET is_visible = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $id);
    $stmt->execute();

    header("Location: dashboard.php?tab=menu");
    exit;
}

// ========================
// DELETE USER
// ========================
if (isset($_POST['delete_user']) && $role === 'admin') {
    $user_id = (int) $_POST['delete_user_id'];
    $current_user = $_SESSION['user_id'];

    // Prevent self-deletion
    if ($user_id != $current_user) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    header("Location: dashboard.php?tab=users&msg=user_deleted");
    exit;
}

// ========================
// DELETE MENU ITEM
// ========================
if (isset($_POST['delete_menu'])) {
    $menu_id = (int) $_POST['delete_menu_id'];

    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();

    header("Location: dashboard.php?tab=menu&msg=item_deleted");
    exit;
}

// If no action matched, redirect back
header("Location: dashboard.php");
exit;
?>