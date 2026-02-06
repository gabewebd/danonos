<?php
// admin/upload_image.php
// SEO-Friendly Image Upload - With GD Fallback

if (!empty($_FILES['file']['name'])) {
    if (!$_FILES['file']['error']) {

        $file = $_FILES['file'];
        $uploadDir = '../uploads/';

        // 1. CLEAN FILENAME (SEO Friendly)
        $rawName = pathinfo($file['name'], PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $rawName);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim(strtolower($cleanName), '-');

        // Get original extension
        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Check if GD is available
        $gdAvailable = function_exists('imagecreatefromjpeg');

        // Set extension based on GD availability
        $finalExt = $gdAvailable ? 'jpg' : $origExt;

        // 2. HANDLE DUPLICATES
        $finalName = $cleanName . '.' . $finalExt;
        $counter = 1;

        while (file_exists($uploadDir . $finalName)) {
            $finalName = $cleanName . '-' . $counter . '.' . $finalExt;
            $counter++;
        }

        $destination = $uploadDir . $finalName;
        $returnUrl = '/danonos/uploads/' . $finalName;

        // 3. UPLOAD ORIGINAL IMAGE (NO PROCESSING)
        // Skip all resizing and compression to preserve original quality
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            echo $returnUrl;
        } else {
            echo 'Error: Failed to upload file';
        }
    } else {
        echo 'Error: ' . $_FILES['file']['error'];
    }
}
?>