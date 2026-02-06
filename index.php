<?php
$pageTitle = "Danono's | Home";
$metaDesc = "Welcome to Danono's! Home of the best brioche doughnuts and brownies in Angeles City. Freshly baked daily.";
$customCss = "index.css";
include 'includes/db_connect.php'; // Optional, but consistent
?>
<?php include 'includes/header.php'; ?>
<style>
    /* --- OUR STORY SECTION (FIXED) - FAILSAFE --- */
    .story-section {
        padding: 100px 5%;
        background-color: transparent;
        position: relative;
    }

    .story-content {
        display: flex !important;
        flex-direction: row !important;
        /* Force row layout */
        flex-wrap: nowrap !important;
        /* Prevent wrapping */
        align-items: center !important;
        justify-content: center !important;
        gap: 80px !important;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Image Side */
    .story-image {
        flex: 1 !important;
        min-width: 0 !important;
        /* Prevent flex overflow */
        max-width: 450px;
        position: relative;
        border-radius: 30px;
        border: 8px solid #431407;
        /* Dark Brown */
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .story-image img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    /* The "Since 2018" Badge */
    .story-badge {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #FFC107;
        /* Gold */
        border: 5px solid #431407;
        width: 90px;
        height: 90px;
        border-radius: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #431407;
        z-index: 2;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .story-badge-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: -2px;
    }

    .story-badge-year {
        font-size: 26px;
        font-weight: 900;
        line-height: 1;
    }

    /* Text Side */
    .story-text {
        flex: 1.2 !important;
        min-width: 0 !important;
        /* Prevent flex overflow */
    }

    .story-text h2 {
        font-size: 48px;
        line-height: 1.1;
        margin-bottom: 24px;
        color: #431407;
    }

    .story-text p {
        font-size: 17px;
        line-height: 1.6;
        color: #666;
        margin-bottom: 25px;
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-text">
        <h1>More Choices,<br><span class="pop-out-text">MORE VALUE.</span></h1>
        <p>Indulge in our famous Glazed Donuts and refreshing Spanish Latte. Crafted fresh daily in Los Angeles.</p>
        <div class="hero-buttons">
            <a href="menu.php" class="btn btn-orange"><i class="ph ph-bicycle"></i> Order Delivery</a>
            <a href="locations.php" class="btn btn-outline"><i class="ph ph-map-pin"></i> Find a branch</a>
        </div>
        <p class="tagline">
            AVAILABLE ON: <span class="tagline-available">GrabFood</span>
        </p>
    </div>
    <div class="hero-image">
        <img src="assets/img/danonos-image.png" alt="Danono's Donuts Box">
    </div>
</section>

<!-- MOST LOVED TREATS SECTION -->
<section class="treats-section">
    <div class="section-header">
        <span class="section-label">FEATURED FAVORITES</span>
        <h2>Most Loved <span class="pop-out-text-sm">TREATS</span></h2>
        <a href="menu.php" class="view-all">View full menu <span>→</span></a>
    </div>
    <div class="treats-carousel">
        <div class="treat-card">
            <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?w=300&h=300&fit=crop"
                alt="Choco Heaven Supreme">
            <h3>Choco Heaven Supreme</h3>
            <p>Indulge in our decadent chocolate glazed creation</p>
            <a href="menu.php" class="btn btn-small">PKG</a>
        </div>
        <div class="treat-card">
            <img src="https://images.unsplash.com/photo-1631397834789-32263f357564?w=300&h=300&fit=crop"
                alt="Biscoff Bite">
            <h3>Biscoff Bite</h3>
            <p>Sweet treat with delightful Biscoff crunch</p>
            <a href="menu.php" class="btn btn-small">PKG</a>
        </div>
        <div class="treat-card">
            <img src="https://images.unsplash.com/photo-1534940568018-05b1062b53eb?w=300&h=300&fit=crop"
                alt="Molly Marshmallow">
            <h3>Molly Marshmallow</h3>
            <p>Fluffy and light with a sweet marshmallow center</p>
            <a href="menu.php" class="btn btn-small">PKG</a>
        </div>
        <div class="treat-card">
            <img src="https://images.unsplash.com/photo-1558500204-7a1ae5d33306?w=300&h=300&fit=crop" alt="Pistachio C">
            <h3>Pistachio C</h3>
            <p>Nutty flavor with creamy pistachio filling</p>
            <a href="menu.php" class="btn btn-small">PKG</a>
        </div>
    </div>
</section>

<!-- DRINKS AND BROWNIES SECTION -->
<section class="drinks-brownies">
    <div class="drinks-content">
        <div class="drinks-image">
            <img src="https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=500&fit=crop"
                alt="Spanish Latte Drinks">
        </div>
        <div class="drinks-text">
            <span class="section-label">PERFECT PAIRING</span>
            <h2>Drinks and Brownies That<br><span class="pop-out-text-sm">COMPLEMENT</span></h2>
            <p>From our signature Spanish Latte to classic Filipino hot coffee, every sip is crafted to pair
                perfectly with our favorite doughnuts.</p>
            <a href="menu.php" class="btn btn-dark"><i class="ph ph-cookie"></i> See Full Menu</a>
        </div>
        <div class="brownies-image">
            <img src="https://images.unsplash.com/photo-1606313564200-e75d5e30476d?w=400&h=450&fit=crop" alt="Brownies">
        </div>
    </div>
</section>

<!-- OUR STORY SECTION -->
<section class="story-section">
    <div class="story-content">
        <div class="story-image">
            <img src="https://images.unsplash.com/photo-1507048331197-7d4ac70811cf?w=400&h=500&fit=crop"
                alt="Danono's Kitchen">
            <div class="story-badge">
                <div class="story-badge-label">SINCE</div>
                <div class="story-badge-year">2018</div>
            </div>
        </div>
        <div class="story-text">
            <span class="section-label">OUR STORY</span>
            <h2>From Nono's to<br><span class="pop-out-text-sm">DANONO'S</span></h2>
            <p>What started as a small home kitchen project in 2018 has grown into Danono's Doughnuts. Our mission:
                to create treats that bring happiness and sweetness in every bite.</p>
            <p>Every morning at 6AM we'll be baking hand-cut donuts with pride, using, filling, and frying in small
                batches to ensure perfection in every bite.</p>
            <a href="about.php" class="btn btn-orange">Read Our Full Story <span>→</span></a>
        </div>
    </div>
</section>

<!-- LOCATIONS AND BLOGS SECTION -->
<section class="nav-cards-section">
    <div class="nav-cards-container">
        <div class="nav-card">
            <img src="https://images.unsplash.com/photo-1555507036-ab1f40388085?w=600&h=400&fit=crop"
                alt="Our Locations">
            <div class="nav-card-overlay">
                <h3>Our Locations</h3>
                <a href="locations.php" class="btn btn-orange"><i class="ph ph-map-pin"></i> Find a Branch</a>
            </div>
        </div>
        <div class="nav-card">
            <img src="https://images.unsplash.com/photo-1483389127517-711bc0d54b2c?w=600&h=400&fit=crop"
                alt="Our Blogs">
            <div class="nav-card-overlay">
                <h3>Our Blogs</h3>
                <a href="blogs.php" class="btn btn-orange"><i class="ph ph-read-cv-logo"></i> Read Blogs</a>
            </div>
        </div>
    </div>
</section>

<!-- JOIN OUR FAMILY SECTION -->
<section class="family-section">
    <div class="family-content">
        <h2>BE PART OF OUR<br><span class="double-stroke" data-text="GROWING FAMILY">GROWING FAMILY</span></h2>
        <p>Ready to taste the handmade? Join a box today and make your day a little sweeter.</p>
        <div class="family-buttons">
            <a href="franchise.php" class="btn btn-white">Partner With Us</a>
            <a href="locations.php" class="btn btn-white">Find a Branch</a>
        </div>
    </div>
    <div class="family-image">
        <img src="https://images.unsplash.com/photo-1517486430290-356570d17c92?w=500&h=600&fit=crop"
            alt="Family of Donuts">
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>

</html>