<?php
$pageTitle = "About Us - Danono's";
$customCss = "about.css";
$metaDesc = "Learn about Danono's story - from a small home kitchen in 2019 to Angeles City's favorite doughnut destination.";
?>
<?php include 'includes/header.php'; ?>

<!-- Story Section -->
<section class="story-section" style="background-color: transparent;">
    <div class="story-content">
        <!-- Image with Year Badge -->
        <div class="story-image">
            <img src="uploads/about-story.jpg" alt="Danono's humble beginnings"
                onerror="this.src='https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=500&fit=crop'">
            <div class="story-badge">
                <span class="story-badge-label">SINCE</span>
                <span class="story-badge-year">2019</span>
            </div>
        </div>

        <!-- Story Text -->
        <div class="story-text">
            <span class="section-label">Our Story</span>
            <h2>From Nono's to <span class="highlight-orange">Danono's</span></h2>

        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>