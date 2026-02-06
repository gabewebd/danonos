<?php
$pageTitle = "Our Menu - Danono's";
$metaDesc = "Explore Danono's delicious menu of premium brioche doughnuts, brownies, and refreshing drinks.";
$customCss = "menu.css";
include 'includes/db_connect.php';
?>
<?php include 'includes/header.php'; ?>
<style>
    /* --- CARD STYLING --- */
    .card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #FFF8F0;
        /* Very light cream border */
        box-shadow: 0 10px 30px rgba(67, 20, 7, 0.03);
        /* Soft shadow */
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        /* Ensures all cards in a row are same height */
        position: relative;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(67, 20, 7, 0.08);
        border-color: #EF7D32;
        /* Orange border on hover */
    }

    /* Image */
    .card img {
        width: 100%;
        height: 260px;
        /* Fixed height for uniformity */
        object-fit: cover;
        background-color: #FFF8F0;
        transition: transform 0.5s ease;
    }

    .card:hover img {
        transform: scale(1.05);
        /* Subtle zoom effect */
    }

    /* Text Content */
    .card h3 {
        font-size: 22px;
        margin: 20px 25px 10px;
        color: #431407;
        font-weight: 700;
    }

    .card p {
        font-size: 15px;
        color: #666;
        margin: 0 25px 20px;
        line-height: 1.6;
        flex-grow: 1;
        /* Pushes the price to the bottom */
    }

    /* Price Tag */
    .price {
        margin-top: auto;
        /* Forces to bottom */
        padding: 20px 25px;
        background-color: #FFFDF9;
        border-top: 1px solid #FFF0E0;
        color: #EF7D32 !important;
        /* Force Orange */
        font-weight: 800;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .price i {
        font-size: 20px;
    }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="section-header">
        <span class="section-label">Fresh Daily</span>
        <h1 class="page-title">Most Loved <span>Treats</span></h1>
    </div>

    <!-- Menu Filters -->
    <div class="menu-filters">
        <button class="filter-btn active" onclick="filterMenu('all', this)">All</button>
        <button class="filter-btn" onclick="filterMenu('doughnuts', this)">Doughnuts</button>
        <button class="filter-btn" onclick="filterMenu('brownies', this)">Brownies</button>
        <button class="filter-btn" onclick="filterMenu('coffee', this)">Beverages</button>
    </div>

    <!-- Menu Grid -->
    <div class="grid" id="menuGrid">
        <?php
        $sql = "SELECT * FROM menu_items WHERE (is_visible = 1 OR is_visible IS NULL) ORDER BY category, name";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $name = htmlspecialchars($row["name"]);
                $price = number_format($row["price"], 2);
                $description = isset($row["description"]) ? htmlspecialchars($row["description"]) : "A delicious treat made fresh daily.";
                $image = isset($row["image"]) && !empty($row["image"]) ? "uploads/" . $row["image"] : "https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&h=200&fit=crop";
                $cat = strtolower($row["category"]);
                // Use DB alt_text if available, otherwise fallback to name
                $altText = !empty($row["alt_text"]) ? htmlspecialchars($row["alt_text"]) : $name;
                ?>
                <div class="card" data-category="<?php echo $cat; ?>">
                    <img src="<?php echo $image; ?>" alt="<?php echo $altText; ?>"
                        onerror="this.src='https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&h=200&fit=crop'">
                    <h3><?php echo $name; ?></h3>
                    <p><?php echo $description; ?></p>
                    <p class="price"><i class="ph ph-tag"></i> ₱<?php echo $price; ?></p>
                </div>
                <?php
            }
        } else {
            // Fallback static menu items if database is empty
            $menuItems = [
                ["name" => "Classic Glazed", "category" => "doughnuts", "price" => "45.00", "desc" => "Our signature brioche doughnut with vanilla glaze."],
                ["name" => "Chocolate Dream", "category" => "doughnuts", "price" => "55.00", "desc" => "Rich chocolate ganache on fluffy brioche."],
                ["name" => "Ube Delight", "category" => "doughnuts", "price" => "60.00", "desc" => "Filipino favorite purple yam with cheese crumble."],
                ["name" => "Matcha Bliss", "category" => "doughnuts", "price" => "65.00", "desc" => "Premium Japanese matcha glaze with white chocolate."],
                ["name" => "Fudge Brownie", "category" => "brownies", "price" => "45.00", "desc" => "Dense, chewy brownie with chocolate chunks."],
                ["name" => "Iced Coffee", "category" => "coffee", "price" => "85.00", "desc" => "Cold brew coffee, perfect with any doughnut."],
            ];

            foreach ($menuItems as $item) {
                ?>
                <div class="card" data-category="<?php echo $item['category']; ?>">
                    <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&h=200&fit=crop"
                        alt="<?php echo $item['name']; ?>">
                    <h3><?php echo $item['name']; ?></h3>
                    <p><?php echo $item['desc']; ?></p>
                    <p class="price"><i class="ph ph-tag"></i> ₱<?php echo $item['price']; ?></p>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <script>
        function filterMenu(cat, btn) {
            // Remove active class
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                const cardCat = card.dataset.category;
                const isMatch = (cat === 'all') ||
                    (cat === cardCat) ||
                    (cat === 'coffee' && (cardCat === 'coffee' || cardCat === 'beverages'));

                if (isMatch) {
                    card.style.display = 'block';
                    // Re-trigger animation if any
                    card.style.animation = 'none';
                    card.offsetHeight; /* trigger reflow */
                    card.style.animation = 'fadeIn 0.5s';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</div>




<?php include 'includes/footer.php'; ?>