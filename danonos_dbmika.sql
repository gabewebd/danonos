-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 03:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `danonos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('Doughnuts','Brownies','Beverages') DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `image`, `alt_text`, `created_at`, `is_visible`) VALUES
(1, 'Glazed Doughnut', 'Classic glazed brioche doughnuts', 65.00, 'Doughnuts', '', 'glazed-doughnut', '2026-01-29 17:19:19', 1),
(2, 'Chocolate Brownie', 'Rich fudgy brownie', 85.00, 'Brownies', '', NULL, '2026-01-29 17:19:19', 1),
(3, 'Iced Americano', 'Bold espresso over ice', 95.00, 'Beverages', '', NULL, '2026-01-29 17:19:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `image_alt_text` varchar(255) DEFAULT 'Delicious Donut',
  `meta_description` varchar(160) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `featured_image`, `image_alt_text`, `meta_description`, `status`, `created_at`, `author_id`) VALUES
(16, 'Beyond the Doughnut: The Ultimate Guide to Our Iced Drinks', 'beyond-the-doughnut-the-ultimate-guide-to-our-iced-drinks', '<p class=\"lead\" style=\"font-size: 20px; line-height: 1.6; color: #555;\">\r\n    There is a rule of thermodynamics we live by at <strong>Danonos</strong>: For every warm, fluffy doughnut, there must be an equal and opposite <strong>ice-cold beverage</strong>. Whether you need a caffeine rocket-boost to start your morning or a creamy, sweet escape in the afternoon, our drink menu is designed to do one thing: <strong>Refresh you instantly.</strong>\r\n</p>\r\n\r\n<p style=\"font-size: 18px; color: #555; margin-bottom: 40px;\">\r\n    Move over, pastries. Today, we are talking about the liquid lineup that keeps our regulars coming back.\r\n</p>\r\n\r\n<hr style=\"border-top: 1px solid #e5e7eb; margin: 40px 0;\">\r\n\r\n<div style=\"margin-bottom: 60px;\">\r\n    <h2 style=\"color: #EF7D32; font-size: 32px; margin-bottom: 20px;\">The Energizer: Iced Spanish Latte</h2>\r\n    \r\n    <p>Sometimes, a regular iced coffee just doesn\'t cut it. You need something that hits harder but tastes sweeter. Enter the <strong>Spanish Latte</strong>.</p>\r\n\r\n    <p>This isn\'t your average coffee shop brew. We use a base of sweetened condensed milk to create a rich, velvety texture, then top it with our signature <strong>Medium-Dark Roast Espresso</strong>. The result? A beautiful gradient look and a taste that strikes the perfect balance between strong coffee and creamy sweetness.</p>\r\n\r\n    <p><strong>Perfect Pairing:</strong> Try this with a <em>Classic Glazed Doughnut</em>. The bitterness of the espresso cuts right through the sugar glaze.</p>\r\n\r\n    <div style=\"text-align: center; margin-top: 30px;\">\r\n        <img src=\"/danonos/uploads/danonos-iced-spanish-latte.jpg\" alt=\"Danonos Iced Spanish Latte with layers of milk and espresso\" style=\"width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);\">\r\n        <p style=\"font-size: 14px; color: #888; margin-top: 10px; font-style: italic;\">\r\n            Bold espresso meets sweet condensed milk.\r\n        </p>\r\n    </div>\r\n</div>\r\n\r\n<hr style=\"border-top: 1px solid #e5e7eb; margin: 40px 0;\">\r\n\r\n<div style=\"margin-bottom: 60px;\">\r\n    <h2 style=\"color: #EF7D32; font-size: 32px; margin-bottom: 20px;\">The Indulgence: Cookies &amp; Cream Frappe</h2>\r\n    \r\n    <p>Let’s be real—sometimes you don\'t want a drink; you want a <strong>dessert you can sip</strong>. The Cookies &amp; Cream Frappe is our love letter to everyone who grew up dunking cookies in milk.</p>\r\n\r\n    <p>We blend rich vanilla cream with heaps of crushed chocolate cookies, then crown it with a mountain of whipped cream. It is thick, frosty, and unapologetically decadent. It’s comforting, creamy, and undeniably classic.</p>\r\n\r\n    <p><strong>Pro Tip:</strong> This is a favorite among the \"I don\'t drink coffee\" crowd!</p>\r\n\r\n    <div style=\"text-align: center; margin-top: 30px;\">\r\n        <img src=\"/danonos/uploads/danonos-cookies-cream-frappe.jpg\" alt=\"Danonos Cookies and Cream Frappe topped with whipped cream\" style=\"width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);\">\r\n        <p style=\"font-size: 14px; color: #888; margin-top: 10px; font-style: italic;\">\r\n            Creamy, comforting, and packed with cookie crunch.\r\n        </p>\r\n    </div>\r\n</div>\r\n\r\n<hr style=\"border-top: 1px solid #e5e7eb; margin: 40px 0;\">\r\n\r\n<div style=\"margin-bottom: 60px; text-align: center;\">\r\n    <h2 style=\"color: #431407; font-size: 36px; margin-bottom: 20px;\">Stay Chilled All Summer</h2>\r\n    <p style=\"max-width: 700px; margin: 0 auto 30px auto; color: #555;\">\r\n        From the zesty kick of our <strong>Strawberry Lemonade</strong> to the earthy smooth vibes of our <strong>Strawberry Matcha Latte</strong>, our menu is exploding with color. We don\'t just do brown and beige here—we do vibrant.\r\n    </p>\r\n\r\n    <img src=\"/danonos/uploads/danonos-summer-beverage-lineup.jpg\" alt=\"Danonos Summer Drink Menu Collection\" style=\"width: 100%; max-width: 800px; border-radius: 20px; box-shadow: 0 12px 30px rgba(0,0,0,0.1);\">\r\n    \r\n    <p style=\"font-size: 14px; color: #EF7D32; margin-top: 15px; font-weight: 600; letter-spacing: 1px;\">\r\n        ▲ THE SUMMER SQUAD IS HERE\r\n    </p>\r\n</div>\r\n\r\n<h3 style=\"text-align: center; color: #431407; margin-top: 50px;\">Found Your Flavor Yet?</h3>\r\n<p style=\"text-align: center; max-width: 600px; margin: 0 auto;\">\r\n    Whether you need to wake up, cool down, or just treat yourself, there is a cup with your name on it at Danonos. Come visit us and sip the difference!\r\n</p>', 'danonos-iced-beverage-lineup.jpg', 'Danonos \'Feel Refreshed\' summer lineup featuring four iced beverages: Strawberry Milk, Cookies & Cream Frappe, Mixed Berries Frappe, and Red Iced Tea.', 'Discover the refreshing side of Danonos! Beyond doughnuts, explore our iced lineup: energizing artisan coffee and creamy frappes to beat the heat.', 'published', '2026-01-29 16:57:17', 7),
(18, 'Sweetest Ways to Say \"I Love You\": Danono\'s Valentine\'s Collection', 'sweetest-ways-to-say-i-love-you-danono-s-valentine-s-collection', '<p class=\"lead\" style=\"font-size: 20px; line-height: 1.6; color: #555;\">\r\n    Love is in the air—and in the dough! This February, we’re trading our classic rings for something a little more romantic. Whether you need a gift for your significant other, your bestie, or let\'s be honest—<strong>yourself</strong>—our limited-time Valentine\'s Collection is the ultimate language of love.\r\n</p>\r\n\r\n<p style=\"font-size: 18px; color: #555; margin-bottom: 40px;\">\r\n    Forget the generic chocolates. Nothing says \"I adore you\" quite like a box of warm, fluffy brioche.\r\n</p>\r\n\r\n<hr style=\"border-top: 1px solid #e5e7eb; margin: 40px 0;\">\r\n\r\n<div style=\"margin-bottom: 60px;\">\r\n    <h2 style=\"color: #EF7D32; font-size: 32px; margin-bottom: 40px; text-align: center;\">Heart-Shaped Happiness</h2>\r\n    \r\n    <div style=\"margin-bottom: 50px; text-align: center;\">\r\n        <img src=\"/danonos/uploads/danonos-dark-choco-love.jpg\" alt=\"Dark Chocolate Heart Doughnut\" style=\"width: 100%; max-width: 400px; border-radius: 16px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 20px;\">\r\n        <h3 style=\"color: #431407; font-size: 24px; margin-bottom: 10px;\">Dark Choco Love</h3>\r\n        <p style=\"color: #555; max-width: 500px; margin: 0 auto; line-height: 1.6;\">\r\n            For the lover of intense flavors. We dip our soft brioche heart in rich, semi-sweet dark chocolate ganache. It is finished with golden edible pearls and a cute \"Love U\" topper. Elegant, classic, and deeply satisfying.\r\n        </p>\r\n    </div>\r\n\r\n    <div style=\"margin-bottom: 50px; text-align: center;\">\r\n        <img src=\"/danonos/uploads/danonos-red-velvet-crush.jpg\" alt=\"Red Velvet Heart Doughnut\" style=\"width: 100%; max-width: 400px; border-radius: 16px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 20px;\">\r\n        <h3 style=\"color: #431407; font-size: 24px; margin-bottom: 10px;\">Red Velvet Crush</h3>\r\n        <p style=\"color: #555; max-width: 500px; margin: 0 auto; line-height: 1.6;\">\r\n            Bold, bright, and impossible to ignore. This doughnut features a striking red vanilla glaze that encases a surprise center of sweet cream cheese filling. Topped with a \"Love\" heart, it’s a total crush at first bite.\r\n        </p>\r\n    </div>\r\n\r\n    <div style=\"margin-bottom: 30px; text-align: center;\">\r\n        <img src=\"/danonos/uploads/danonos-white-choco-embrace.jpg\" alt=\"White Chocolate Bear Doughnut\" style=\"width: 100%; max-width: 400px; border-radius: 16px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 20px;\">\r\n        <h3 style=\"color: #431407; font-size: 24px; margin-bottom: 10px;\">White Choco Embrace</h3>\r\n        <p style=\"color: #555; max-width: 500px; margin: 0 auto; line-height: 1.6;\">\r\n            The cutest of the bunch! Coated in smooth, milky white chocolate, this heart features an adorable edible teddy bear holding a red heart. It’s almost too cute to eat—almost.\r\n        </p>\r\n    </div>\r\n\r\n</div>\r\n\r\n<hr style=\"border-top: 1px solid #e5e7eb; margin: 40px 0;\">\r\n\r\n<div style=\"margin-bottom: 60px;\">\r\n    <h2 style=\"color: #EF7D32; font-size: 32px; margin-bottom: 20px;\">The Perfect Date Pairing</h2>\r\n    \r\n    <p>What is a sweet treat without a refreshing partner? We have curated the ultimate pairings to elevate your doughnut date.</p>\r\n\r\n    <p>The richness of our <strong>Dark Choco Love</strong> doughnut pairs beautifully with our <strong>Iced Americano</strong>, balancing the sweet and bitter notes. If you prefer something creamier, try the <strong>Red Velvet Crush</strong> with our signature <strong>Spanish Latte</strong>—it’s like a hug in a cup.</p>\r\n\r\n    <div style=\"background: #FFF8F0; padding: 30px; border-radius: 20px; text-align: center; margin-top: 30px; border: 2px dashed #EF7D32;\">\r\n        <h3 style=\"color: #431407; margin-bottom: 10px;\">Valentine\'s Combo Deal</h3>\r\n        <p style=\"font-size: 18px; color: #555; margin-bottom: 20px;\">\r\n            Get a <strong>Box of 3 Heart Doughnuts</strong> + <strong>2 Large Drinks</strong> for a special price!\r\n        </p>\r\n        \r\n        <img src=\"/danonos/uploads/danonos-valentines-box-set.jpg\" alt=\"Danonos Box of 3 Heart Doughnuts\" style=\"width: 100%; max-width: 500px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);\">\r\n        \r\n        <p style=\"font-size: 14px; color: #888;\">*Available February 10-14 only.</p>\r\n    </div>\r\n</div>\r\n\r\n<h3 style=\"text-align: center; color: #431407; margin-top: 50px;\">Spread the Love</h3>\r\n<p style=\"text-align: center; max-width: 600px; margin: 0 auto; line-height: 1.6;\">\r\n    These treats are limited edition, so don\'t wait until the last minute! Visit <strong>Danono\'s</strong> in Angeles City and make this Valentine\'s Day the sweetest one yet.\r\n    <br><br>\r\n    <strong>#DanonosDoughnuts #ValentinesDay #SpreadTheLove</strong>\r\n</p>', 'danonos-valentines-poster.jpg', 'Danono\'s Valentine\'s Day special collection featuring heart-shaped doughnuts and iced coffee pairings.', 'Celebrate Valentine\'s 2026 with Danono\'s limited-edition heart-shaped brioche doughnuts and coffee pairings. Available now in Angeles City!', 'published', '2026-02-04 11:57:40', 7),
(19, 'Why Danono\'s Doughnuts Stand Out in Pampanga', 'why-danono-s-doughnuts-stand-out-in-pampanga', '<div style=\"display: flex; gap: 50px; align-items: center; margin-bottom: 80px; padding: 40px 0;\">\r\n    <div style=\"flex: 1.2;\">\r\n        <img src=\"/danonos/uploads/threegirlsdanonos.png\" alt=\"Danono\'s Building\" style=\"width: 100%; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);\">\r\n    </div>\r\n    <div style=\"flex: 1;\">\r\n        <h1 style=\"color: #EF7D32; font-size: 42px; font-weight: 800; line-height: 1.1; margin-bottom: 25px;\">Danono\'s Brioche<br>Doughnuts: A Pampanga Favorite</h1>\r\n        <p style=\"color: #555; font-size: 18px; line-height: 1.6; margin-bottom: 20px;\">\r\n            Danono\'s Doughnuts &amp; Brownies stands out in Pampanga for its brioche-style doughnuts, known for their <strong>soft, buttery, and fluffy texture</strong>. This focus on premium ingredients makes it a unique and indulgent choice among local doughnut shops.\r\n        </p>\r\n        <p style=\"color: #777; font-size: 16px;\">Whether you\'re in Pampanga or just passing by, Danono\'s Doughnuts &amp; Brownies is a must-try for everyone who appreciates quality treats.</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-bottom: 80px;\">\r\n    <div>\r\n        <h3 style=\"color: #EF7D32; font-size: 22px; margin-bottom: 15px;\">The secret: brioche dough</h3>\r\n        <p style=\"font-size: 15px; color: #666; line-height: 1.6;\">Pampanga is known for its delicious cuisine, but Danono\'s Doughnuts &amp; Brownies stands out when it comes to doughnuts — and the secret is their brioche dough.</p>\r\n    </div>\r\n    <div>\r\n        <h3 style=\"color: #EF7D32; font-size: 22px; margin-bottom: 15px;\">Rich but never heavy</h3>\r\n        <p style=\"font-size: 15px; color: #666; line-height: 1.6;\">Danono\'s doughnuts aren\'t your usual yeast or cake kind. Their buttery brioche is soft, fluffy, and perfectly indulgent without feeling too heavy.</p>\r\n    </div>\r\n    <div>\r\n        <h3 style=\"color: #EF7D32; font-size: 22px; margin-bottom: 15px;\">Flavors that shine</h3>\r\n        <p style=\"font-size: 15px; color: #666; line-height: 1.6;\">The rich, buttery brioche dough at Danono\'s provides the perfect base for every flavor and filling, letting each one shine while never overpowering the treat.</p>\r\n    </div>\r\n    <div>\r\n        <h3 style=\"color: #EF7D32; font-size: 22px; margin-bottom: 15px;\">A unique spot in Pampanga</h3>\r\n        <p style=\"font-size: 15px; color: #666; line-height: 1.6;\">In a province full of doughnut options, it is one of the few specializing in brioche, giving it a unique place in the local dessert scene. Simply put: it\'s doughnuts done right.</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 80px;\">\r\n    <div style=\"flex: 1;\">\r\n        <h2 style=\"color: #EF7D32; font-size: 36px; margin-bottom: 10px;\">Why you should visit</h2>\r\n        <h3 style=\"color: #431407; font-size: 24px;\">Danono\'s Doughnuts and Brownies</h3>\r\n    </div>\r\n    <div style=\"flex: 1.5; color: #666; line-height: 1.7; font-size: 16px;\">\r\n        <p>Whether you\'re in Pampanga or just passing by, Danono\'s Doughnuts &amp; Brownies is a must-try. Their brioche dough makes each doughnut soft, light, and deliciously indulgent.</p>\r\n        <p style=\"margin-top: 15px;\">With a variety of flavors and fillings, there\'s something for everyone — perfect for a quick snack, dessert after a meal, or a late-night treat. Stop by and see why locals keep coming back!</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"background-color: #fcfcfc; border-radius: 20px; padding: 50px; display: flex; align-items: center; gap: 40px; box-shadow: inset 0 0 10px rgba(0,0,0,0.02);\">\r\n    <div style=\"flex: 1.5;\">\r\n        <h2 style=\"color: #EF7D32; font-size: 36px; margin-bottom: 25px;\">Best Flavors to Try!</h2>\r\n        <p style=\"color: #555; line-height: 1.8; margin-bottom: 20px;\">\r\n            Danono\'s offers a delicious selection of brioche doughnuts that are soft, buttery, and simply irresistible. Each doughnut is made to bring out the best in its flavors, so every bite is a perfect mix of taste and texture.\r\n        </p>\r\n        <p style=\"color: #555; line-height: 1.8; margin-bottom: 20px;\">There are some flavors that people keep coming back for, and they\'re definitely worth trying. Curious to see which ones made the list? Check out the full blog here!</p>\r\n        <button style=\"background-color: #E8C4A0; color: #431407; border: none; padding: 12px 30px; border-radius: 5px; font-size: 14px; font-weight: 600; cursor: pointer;\" fdprocessedid=\"kr8z7u\">Click me!</button>\r\n    </div>\r\n    <div style=\"flex: 1; text-align: center;\">\r\n        <img src=\"/danonos/uploads/danonos-donuts.jpg\" alt=\"Danono\'s Flavors\" style=\"width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);\">\r\n    </div>\r\n</div>', '', '', '', 'published', '2026-02-04 14:00:49', 10),
(20, 'Danono\'s Doughnuts: Where Every Bite Feels Like Home', 'danono-s-doughnuts-where-every-bite-feels-like-home', '<div style=\"background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white; padding: 100px 40px; margin-bottom: 60px; border-radius: 0;\">\r\n    <h1 style=\"color: #FFB366; font-size: 56px; font-weight: 900; line-height: 1.3; margin-bottom: 25px; max-width: 900px;\">Danono\'s Doughnuts: Where Every Bite Feels Like Home</h1>\r\n    <p style=\"color: #FFB366; font-size: 22px; font-weight: 700; margin-bottom: 20px;\">There are doughnuts… and then there are Danono\'s Doughnuts.</p>\r\n    <p style=\"color: #ccc; font-size: 16px; line-height: 1.8; max-width: 800px;\">The difference? One is just a snack. The other is an experience. At Danono\'s, doughnuts aren\'t rushed, frozen, or treated like fast food. They\'re crafted with intention. Soft on the inside, perfectly golden on the outside, and balanced just right—not overly sweet, not bland, just dangerously good.</p>\r\n</div>\r\n\r\n<div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 60px; margin-bottom: 80px; padding: 40px 0; align-items: start;\">\r\n    <div style=\"order: 2;\">\r\n        <img src=\"/danonos/uploads/danonos-treats.jpg\" alt=\"Fresh Danono\'s Doughnuts\" style=\"width: 100%; border-radius: 20px; box-shadow: rgba(0, 0, 0, 0.15) 0px 15px 40px;\">\r\n    </div>\r\n    <div style=\"order: 1;\">\r\n        <h2 style=\"color: #1a1a1a; font-size: 38px; font-weight: 900; line-height: 1.2; margin-bottom: 20px;\">Made Fresh.<br>No Shortcuts.</h2>\r\n        <p style=\"color: #444; font-size: 16px; line-height: 1.8; margin-bottom: 18px;\">Let\'s be honest: most doughnuts today taste the same. Mass-produced. Forgettable. Danono\'s refuses to play that game.</p>\r\n        <p style=\"color: #666; font-size: 15px; line-height: 1.7;\">Every batch is made fresh, using quality ingredients and recipes that prioritize flavor over convenience. The dough is airy, the glaze melts into every curve, and the fillings are generous—because a doughnut that skimps is a doughnut that failed.</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"display: grid; grid-template-columns: repeat(2, 1fr); gap: 35px; margin-bottom: 80px;\">\r\n    <div style=\"border-left: 5px solid #FFB366; padding-left: 30px; padding-top: 15px; padding-bottom: 15px;\">\r\n        <h3 style=\"color: #1a1a1a; font-size: 24px; margin-bottom: 12px; font-weight: 800;\">Flavors That Actually Deliver</h3>\r\n        <p style=\"font-size: 15px; color: #555; line-height: 1.7;\">From classic crowd-pleasers to creative twists, Danono\'s flavors don\'t just look good—they taste good. No gimmicks. No empty hype. Just flavors that hit exactly how they should.</p>\r\n    </div>\r\n    <div style=\"border-left: 5px solid #FFB366; padding-left: 30px; padding-top: 15px; padding-bottom: 15px;\">\r\n        <h3 style=\"color: #1a1a1a; font-size: 24px; margin-bottom: 12px; font-weight: 800;\">More Than Doughnuts—It\'s a Mood</h3>\r\n        <p style=\"font-size: 15px; color: #555; line-height: 1.7;\">Danono\'s Doughnuts isn\'t just about food; it\'s about how it makes you feel. It\'s that first warm bite. That quiet \"wow\" moment. It\'s the kind of place you recommend without being asked.</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"background-color: #f5f5f5; padding: 60px 50px; border-radius: 15px; margin-bottom: 80px;\">\r\n    <h2 style=\"color: #1a1a1a; font-size: 34px; margin-bottom: 35px; font-weight: 900;\">Why Danono\'s Stands Out</h2>\r\n    <div style=\"display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;\">\r\n        <div>\r\n            <p style=\"color: #333; font-size: 15px; line-height: 1.9; margin-bottom: 15px;\"><strong style=\"color: #FFB366; font-size: 18px;\">→</strong> Freshly made, always</p>\r\n            <p style=\"color: #333; font-size: 15px; line-height: 1.9; margin-bottom: 15px;\"><strong style=\"color: #FFB366; font-size: 18px;\">→</strong> Balanced sweetness (never overwhelming)</p>\r\n            <p style=\"color: #333; font-size: 15px; line-height: 1.9;\"><strong style=\"color: #FFB366; font-size: 18px;\">→</strong> Soft, fluffy texture that doesn\'t disappoint</p>\r\n        </div>\r\n        <div>\r\n            <p style=\"color: #333; font-size: 15px; line-height: 1.9; margin-bottom: 15px;\"><strong style=\"color: #FFB366; font-size: 18px;\">→</strong> Flavors crafted with care, not shortcuts</p>\r\n            <p style=\"color: #333; font-size: 15px; line-height: 1.9;\"><strong style=\"color: #FFB366; font-size: 18px;\">→</strong> A brand that actually respects doughnuts</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 80px; padding: 50px 0;\">\r\n    <div>\r\n        <img src=\"/danonos/uploads/danonos-blog-donuts.jpg\" alt=\"Danono\'s Doughnuts\" style=\"width: 100%; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.15);\">\r\n    </div>\r\n    <div style=\"padding: 20px;\">\r\n        <h2 style=\"color: #1a1a1a; font-size: 38px; margin-bottom: 20px; font-weight: 900;\">Final Bite</h2>\r\n        <p style=\"color: #555; line-height: 1.8; margin-bottom: 20px; font-size: 16px;\">If you think all doughnuts are the same, Danono\'s will prove you wrong. This isn\'t just a treat—it\'s a standard. And once you\'ve had Danono\'s Doughnuts, settling for anything less feels like a mistake.</p>\r\n        <p style=\"color: #FFB366; font-size: 26px; font-weight: 900; margin-bottom: 0;\">One bite. No regrets.</p>\r\n    </div>\r\n</div>\r\n\r\n<div style=\"background: linear-gradient(135deg, #FFB366 0%, #FF9D33 100%); padding: 80px 50px; border-radius: 20px; text-align: center; color: white;\">\r\n    <h2 style=\"color: white; font-size: 42px; font-weight: 900; margin-bottom: 25px;\">Ready to Experience the Difference?</h2>\r\n    <p style=\"color: white; font-size: 17px; line-height: 1.8; margin-bottom: 35px; max-width: 650px; margin-left: auto; margin-right: auto;\">Whether you\'re craving something comforting with your morning coffee or a sweet reward after a long day, Danono\'s has a doughnut that fits the moment. Visit us today and discover why our customers keep coming back.</p>\r\n    <button style=\"background-color: white; color: #FFB366; border: none; padding: 16px 45px; border-radius: 8px; font-size: 16px; font-weight: 800; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 20px rgba(0,0,0,0.15);\" fdprocessedid=\"xv1dhq\">Visit Danono\'s Today</button>\r\n</div>', '', '', '', 'published', '2026-02-04 14:15:22', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','pending') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(5, 'SuperAdmin', 'admin@danonos.com', '$2y$10$7ihdn9QDiFwv6pEVAbIkf.aIljPQxklN9h7z4Lqp3dmS3GGIaCgPG', 'admin', '2026-01-29 13:21:29', 'active'),
(7, 'Gabrielle Ainshley Velasquez', 'eiyadwnlds@gmail.com', '$2y$10$ABZYDjESy.k/KSDYv58DTeMjaJgC9JkYkYx1V6xafjdy3dDbnMvja', 'editor', '2026-01-29 14:09:55', 'active'),
(8, 'Mark Dave Camus', 'camusmarkdave@gmail.com', '$2y$10$WgzNNFTgaSlCyxeac4EJiOql80APTB0rtp.4d9qxR8RoF4J6aYFRm', 'editor', '2026-02-04 08:20:55', 'active'),
(9, 'Josh Andrei Aguiluz', 'josh.dizon.aguiluz25@gmail.com', '$2y$10$6K/hCTYDfql1/BwWPbc7Eu0Ev8ZNKyiXGnBJyknMRyX.BG3koT1Hy', 'editor', '2026-02-04 08:21:11', 'active'),
(10, 'Mikaella Yamaguchi', 'mikaellayamaguchi23@gmail.com', '$2y$10$y/aSaquR6UFaDgdL66w9je0y/8ryHmibgSA2jiJcajRKgd8K15Dra', 'editor', '2026-02-04 08:21:30', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
