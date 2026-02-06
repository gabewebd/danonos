<?php
$pageTitle = "Blogs - Danono's";
$metaDesc = "Read the latest news, recipes, and stories from Danono's Doughnuts & Brownies.";
$customCss = "blogs.css";
include 'includes/db_connect.php';
?>
<?php include 'includes/header.php'; ?>

<div class="blog-container">
    <!-- Page Header -->
    <div class="section-header">
        <span class="section-label">From Our Kitchen</span>
        <h1 class="page-title">Latest <span>News</span></h1>
        <p class="page-description"
            style="color: #666; max-width: 600px; margin: 0 auto; font-family: 'Barlow', sans-serif; font-size: 18px;">
            Stay updated with the sweetest news, new flavor launches, and behind-the-scenes stories from the Danono's
            kitchen.
        </p>
    </div>

    <!-- Blog Grid -->
    <div class="blog-grid">
        <?php
        $sql = "SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $title = htmlspecialchars($row["title"]);

                // Clean content for excerpt
                $clean_content = $row['content'];
                $clean_content = str_replace('&nbsp;', ' ', $clean_content);
                $clean_content = strip_tags($clean_content);
                $clean_content = html_entity_decode($clean_content);
                $excerpt = substr($clean_content, 0, 120) . '...';

                $date = date('M d, Y', strtotime($row["created_at"]));
                $image = !empty($row["featured_image"]) ? "uploads/" . $row["featured_image"] : "https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=250&fit=crop";
                $slug = isset($row["slug"]) ? $row["slug"] : $id;
                ?>
                <div class="blog-card">
                    <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>"
                        onerror="this.src='https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=250&fit=crop'">
                    <div class="blog-card-content">
                        <p class="blog-card-meta">
                            <i class="ph ph-calendar"></i> <?php echo $date; ?>
                        </p>
                        <h3><?php echo $title; ?></h3>
                        <p><?php echo $excerpt; ?></p>
                        <a href="single-blog.php?slug=<?php echo $slug; ?>" class="btn-read-more">
                            Read More <i class="ph ph-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            // No posts - simple message, no fallback/dummy posts
            ?>
            <div class="no-posts" style="grid-column: 1 / -1;">
                <h3>No blogs created yet.</h3>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>