<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get Post ID
$post_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($post_id == 0) {
    header("Location: dashboard.php");
    exit;
}

// Fetch existing post
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    header("Location: dashboard.php?msg=not_found");
    exit;
}

// Handle Update Submission
if (isset($_POST['update_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $meta_desc = $_POST['meta_description'];
    $alt_text = $_POST['alt_text'];
    $status = $_POST['status'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

    // Handle Image: New upload, new URL, or keep existing (SEO-Friendly Names + Resize & Compress with GD Fallback)
    $image_filename = $post['featured_image']; // Keep existing by default

    if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] == 0) {
        $file = $_FILES['featured_image'];
        $uploadDir = '../uploads/';

        // 1. CLEAN FILENAME (SEO Friendly)
        $rawName = pathinfo($file['name'], PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $rawName);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim(strtolower($cleanName), '-');

        $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $gdAvailable = function_exists('imagecreatefromjpeg');
        $finalExt = $gdAvailable ? 'jpg' : $origExt;

        // 2. HANDLE DUPLICATES
        $fileName = $cleanName . '.' . $finalExt;
        $counter = 1;
        while (file_exists($uploadDir . $fileName)) {
            $fileName = $cleanName . '-' . $counter . '.' . $finalExt;
            $counter++;
        }
        $destination = $uploadDir . $fileName;

        // 3. PROCESS IMAGE
        if ($gdAvailable) {
            // GD AVAILABLE - Resize & Compress
            $max_width = 1200;  // Larger for better detail
            $quality = 90;      // Higher quality (less blur)

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
                // Unsupported format fallback
                move_uploaded_file($file['tmp_name'], $destination);
                $image_filename = $fileName;
            }
        } else {
            // GD NOT AVAILABLE - Simple move
            move_uploaded_file($file['tmp_name'], $destination);
            $image_filename = $fileName;
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_filename = $_POST['image_url'];
    }

    $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, content=?, meta_description=?, featured_image=?, image_alt_text=?, status=? WHERE id=?");
    $stmt->bind_param("sssssssi", $title, $slug, $content, $meta_desc, $image_filename, $alt_text, $status, $post_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Danono's Admin</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Fredoka:wght@500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="admin-style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        /* Make editor fill vertical space - Match post-create.php */
        .editor-layout {
            min-height: calc(100vh - 80px);
        }

        .editor-main {
            display: flex;
            flex-direction: column;
        }

        .note-editor {
            flex: 1;
            min-height: 600px !important;
        }

        .note-editor .note-editing-area {
            height: calc(100% - 42px) !important;
        }

        .note-editor .note-editable {
            height: 100% !important;
        }
    </style>
</head>

<body>

    <div class="admin-page" style="padding: 0;">

        <!-- Header -->
        <div class="admin-header" style="margin: 0; width: 100%;">
            <div class="admin-header-left">
                <a href="dashboard.php" class="back-link">
                    <i class="ph ph-arrow-left"></i> Back
                </a>
                <h1>Edit Post</h1>
            </div>
            <div class="admin-header-right">
                <a href="../single-blog.php?slug=<?php echo urlencode($post['slug']); ?>" target="_blank"
                    class="btn btn-secondary">
                    <i class="ph ph-eye"></i> Preview Post
                </a>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="editor-layout">

                <!-- LEFT: Main Editor (75%) -->
                <div class="editor-main">
                    <!-- Title -->
                    <input type="text" name="title" class="title-input" placeholder="Enter post title..."
                        value="<?php echo htmlspecialchars($post['title']); ?>" required>

                    <!-- Editor -->
                    <textarea id="summernote"
                        name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <!-- RIGHT: Sidebar (25%) -->
                <div class="editor-sidebar">

                    <!-- PUBLISH SECTION -->
                    <div class="sidebar-section">
                        <div class="sidebar-title"><i class="ph ph-rocket-launch"></i> Publish</div>

                        <label class="field-label">Status</label>
                        <select name="status" id="status-select" class="form-input">
                            <option value="published" <?php echo $post['status'] == 'published' ? 'selected' : ''; ?>>
                                Published</option>
                            <option value="draft" <?php echo $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft
                            </option>
                        </select>

                        <button type="submit" name="update_post" id="submit-btn" class="btn btn-primary btn-full btn-lg"
                            style="margin-top: 15px;">
                            <i class="ph ph-floppy-disk"></i> <span id="btn-text">Update Post</span>
                        </button>
                    </div>

                    <!-- FEATURED IMAGE SECTION -->
                    <div class="sidebar-section">
                        <div class="sidebar-title"><i class="ph ph-image"></i> Featured Image</div>

                        <div id="featured-preview" class="image-preview-area">
                            <?php if ($post['featured_image']): ?>
                                <?php
                                $img_preview = (strpos($post['featured_image'], 'http') === 0)
                                    ? $post['featured_image']
                                    : '../uploads/' . $post['featured_image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_preview); ?>" alt="Current Image">
                            <?php else: ?>
                                <span class="placeholder">No image selected</span>
                            <?php endif; ?>
                        </div>

                        <label class="field-label">Upload New Image</label>
                        <input type="file" name="featured_image" class="form-input" accept="image/*" id="file-input">

                        <label class="field-label" style="margin-top: 10px;">Or Image URL</label>
                        <input type="text" name="image_url" class="form-input"
                            placeholder="https://example.com/image.jpg"
                            value="<?php echo (strpos($post['featured_image'], 'http') === 0) ? htmlspecialchars($post['featured_image']) : ''; ?>">

                        <label class="field-label" style="margin-top: 10px;">Alt Text (SEO)</label>
                        <input type="text" name="alt_text" class="form-input" placeholder="Describe the image..."
                            value="<?php echo htmlspecialchars($post['image_alt_text'] ?? ''); ?>">
                    </div>

                    <!-- SEO SECTION -->
                    <div class="sidebar-section">
                        <div class="sidebar-title"><i class="ph ph-magnifying-glass"></i> SEO</div>

                        <label class="field-label">Meta Description</label>
                        <textarea name="meta_description" class="form-input" rows="4"
                            placeholder="Brief summary for search engines (max 160 chars)..."
                            maxlength="160"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                        <p class="field-hint">Keep under 160 characters</p>
                    </div>

                    <!-- DYNAMIC: Image Settings Panel -->
                    <div id="image-settings-panel" class="image-panel">
                        <div class="sidebar-title" style="color: var(--primary);"><i class="ph ph-pencil-simple"></i>
                            Edit Image</div>

                        <div class="image-panel-preview">
                            <img id="panel-preview" src="" alt="">
                        </div>

                        <label class="field-label">Alt Text</label>
                        <input type="text" id="panel-alt" class="form-input" placeholder="Image description...">

                        <label class="field-label" style="margin-top: 10px;">Caption / Title</label>
                        <input type="text" id="panel-title" class="form-input" placeholder="Tooltip on hover...">

                        <button type="button" id="apply-image-btn" class="btn btn-primary btn-full"
                            style="margin-top: 12px;">
                            <i class="ph ph-check"></i> Update Image
                        </button>
                        <p class="field-hint" style="text-align: center;">Click image in editor to edit</p>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            // Init Summernote with Server-side Image Upload
            $('#summernote').summernote({
                placeholder: 'Start writing...',
                tabsize: 2,
                height: 550,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                fontNames: ['Barlow', 'Fredoka', 'Arial', 'Georgia'],
                defaultFontName: 'Barlow',
                callbacks: {
                    onImageUpload: function (files) {
                        // Upload image to server instead of Base64
                        var data = new FormData();
                        data.append("file", files[0]);
                        $.ajax({
                            url: 'upload_image.php',
                            method: 'POST',
                            data: data,
                            contentType: false,
                            processData: false,
                            success: function (url) {
                                // Insert the image as a normal URL, not Base64
                                $('#summernote').summernote('insertImage', url);
                            },
                            error: function (data) {
                                console.log('Upload error:', data);
                                alert('Image upload failed. Please try again.');
                            }
                        });
                    }
                }
            });

            // Dynamic Button Text based on Status
            function updateButtonText() {
                var status = $('#status-select').val();
                if (status === 'draft') {
                    $('#btn-text').text('Save as Draft');
                    $('#submit-btn i').attr('class', 'ph ph-floppy-disk');
                } else {
                    $('#btn-text').text('Update Post');
                    $('#submit-btn i').attr('class', 'ph ph-check-circle');
                }
            }

            $('#status-select').on('change', updateButtonText);
            updateButtonText(); // Initial call

            // Image click in editor
            let $currentImage = null;

            $(document).on('click', '.note-editable img', function (e) {
                e.stopPropagation();
                $currentImage = $(this);

                $('#image-settings-panel').addClass('active');
                $('#panel-preview').attr('src', $currentImage.attr('src'));
                $('#panel-alt').val($currentImage.attr('alt') || '');
                $('#panel-title').val($currentImage.attr('title') || '');
            });

            // Apply Changes
            $('#apply-image-btn').on('click', function () {
                if ($currentImage) {
                    $currentImage.attr('alt', $('#panel-alt').val());
                    $currentImage.attr('title', $('#panel-title').val());

                    $(this).html('<i class="ph ph-check"></i> Saved!');
                    setTimeout(() => {
                        $(this).html('<i class="ph ph-check"></i> Update Image');
                    }, 1200);
                }
            });

            // Close panel when clicking elsewhere
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#image-settings-panel').length &&
                    !$(e.target).closest('.note-editable img').length) {
                    $('#image-settings-panel').removeClass('active');
                }
            });

            // Featured Image Preview
            $('#file-input').on('change', function () {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#featured-preview').html('<img src="' + e.target.result + '" alt="Preview">');
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

</body>

</html>