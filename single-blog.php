<?php
include 'includes/db_connect.php';

if (!isset($_GET['slug'])) {
    header('Location: blogs.php');
    exit;
}

$slug = $_GET['slug'];

// SQL: Get Post + Author Full Name
$stmt = $conn->prepare("SELECT p.*, u.full_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.slug = ? AND p.status = 'published'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    header('Location: blogs.php');
    exit;
}

// Read Time Calculation
$word_count = str_word_count(strip_tags($post['content']));
$read_time = max(1, ceil($word_count / 200)) . ' min read';

// Meta Description (Use DB field if available, else auto-generate)
$clean_content = strip_tags($post['content']);
$auto_desc = substr($clean_content, 0, 150) . '...';
$metaDesc = !empty($post['meta_description']) ? $post['meta_description'] : $auto_desc;

$pageTitle = $post['title'] . " | Danono's Blog";
$customCss = "single-blog.css";
include 'includes/header.php';

// Featured Image Source
$img_src = '';
if ($post['featured_image']) {
    $img_src = (strpos($post['featured_image'], 'http') === 0 || strpos($post['featured_image'], '/') === 0)
        ? $post['featured_image']
        : "uploads/" . $post['featured_image'];
}

// Author Name with fallback
$author_name = !empty($post['full_name']) ? $post['full_name'] : 'Danonos Team';
?>

<div class="blog-wrapper">

    <!-- Header: Meta, Title, Author -->
    <div class="blog-header">
        <div class="blog-meta">
            <?php echo date('F j, Y', strtotime($post['created_at'])); ?> •
            <?php echo $read_time; ?>
        </div>
        <h1 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="blog-author">
            Written by <strong><?php echo htmlspecialchars($author_name); ?></strong>
        </div>
    </div>

    <!-- Featured Image -->
    <?php if ($img_src): ?>
        <div class="featured-image-container">
            <img src="<?php echo htmlspecialchars($img_src); ?>"
                alt="<?php echo htmlspecialchars($post['image_alt_text'] ?? $post['title']); ?>">
        </div>
    <?php endif; ?>

    <!-- Content -->
    <div class=" blog-content">
        <?php echo $post['content']; ?>
    </div>

    <!-- Footer: Back & Share -->
    <div class="blog-footer">
        <a href="blogs.php" class="back-link">
            <i class="ph ph-arrow-left"></i> Back to Stories
        </a>
        <button onclick="copyLink()" class="btn-share">
            <i class="ph ph-share-network"></i> Share this Post
        </button>
    </div>

</div>

<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        const btn = document.querySelector('.btn-share');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ph ph-check"></i> Link Copied!';
        btn.style.background = '#16a34a';
        btn.style.borderColor = '#16a34a';
        btn.style.color = 'white';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.style.borderColor = '';
            btn.style.color = '';
        }, 2000);
    }
</script>

<?php include 'includes/footer.php'; ?>