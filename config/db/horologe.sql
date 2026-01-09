-- horologe schema (clean rebuild)
-- Generated: 2026-01-09

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Users
CREATE TABLE `users` (
  `user_id` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Products (watches)
CREATE TABLE `watch` (
  `watch_id` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `image_file` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CHECK (`price` >= 0),
  CHECK (`stock_quantity` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cart header (no cached total to avoid drift)
CREATE TABLE `cart` (
  `cart_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cart items
CREATE TABLE `cartitems` (
  `cart_item_id` varchar(50) NOT NULL,
  `watch_id` varchar(50) NOT NULL,
  `cart_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  CHECK (`quantity` > 0),
  CHECK (`subtotal` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Orders (one row per product with embedded user + shipping snapshots; no status)
CREATE TABLE `orders` (
  `order_id` varchar(50) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_name` varchar(120) NOT NULL,
  `user_email` varchar(120) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `watch_id` varchar(50) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `ship_full_name` varchar(150) NOT NULL,
  `ship_phone_number` varchar(20) NOT NULL,
  `ship_street_address` varchar(255) NOT NULL,
  `ship_city` varchar(100) NOT NULL,
  `ship_province_state` varchar(100) NOT NULL,
  `ship_postal_code` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CHECK (`quantity` > 0),
  CHECK (`price_at_purchase` >= 0),
  CHECK (`total_amount` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment
CREATE TABLE `payment` (
  `payment_id` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  CHECK (`amount` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- PayPal payment details
CREATE TABLE `paypalpayment` (
  `paypal_transaction_id` varchar(100) NOT NULL,
  `payer_email` varchar(100) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `payment_status` varchar(50) NOT NULL,
  `payment_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed data for products
INSERT INTO `watch` (`watch_id`, `brand`, `model`, `description`, `price`, `stock_quantity`, `image_file`) VALUES
('W001', 'Cartier', 'Americane Rose Gold W2607456', 'A graceful expression of Cartier''s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 9500.00, 20, 'assets/images/products/cartier/cartier1.png'),
('W002', 'Cartier', 'Tank Basculante White Dial 2405', 'An exquisite reversible case design, the Tank Basculante combines timeless Cartier style with playful ingenuity. The crisp white dial and elegant hands reflect the brand''s mastery of understated luxury, making it a rare and collectible piece.', 8000.00, 20, 'assets/images/products/cartier/cartier2.png'),
('W003', 'Cartier', 'Panthere Two Tone Yellow Gold White Dial 110000R 2', 'This iconic Panthere model blends stainless steel and yellow gold in a harmonious two-tone design. The gleaming white dial and flexible bracelet exude sophistication, offering a perfect balance of sportiness and refined elegance.', 9000.00, 20, 'assets/images/products/cartier/cartier3.png'),
('W004', 'Cartier', 'Ronde Solo Yellow Gold W6700355 2021', 'A modern classic, the Ronde Solo features a round yellow gold case with crisp Roman numerals and a polished finish. Its balanced proportions and timeless design make it an effortlessly stylish companion for every occasion.', 10000.00, 20, 'assets/images/products/cartier/cartier4.png'),
('W005', 'Cartier', 'Basculante Yellow Gold White Dial 2480', 'The Tank Basculante in yellow gold showcases Cartier''s inventive reversible case, combining elegance with mechanical ingenuity. Its pure white dial and polished finish create a timepiece that is both refined and visually striking.', 7500.00, 20, 'assets/images/products/cartier/cartier5.png'),
('W006', 'MontBlac', 'Star Legacy Chronograph 42mm Limited Edition - 178', 'A masterful embodiment of Montblanc''s dedication to haute horology, this limited-edition chronograph is one of only 1,786 pieces worldwide. Its 42mm case elegantly balances boldness and refinement, while the meticulously crafted dial, adorned with classic Arabic numerals and blued steel hands, pays homage to the brand''s historic Minerva heritage. Designed for those who appreciate both technical precision and timeless style, this piece is a true collector''s gem.', 10000.00, 20, 'assets/images/products/montblac/montblac1.png'),
('W007', 'MontBlac', 'Iced Sea Automatic Date 0 Oxygen', 'Inspired by the pristine and uncharted waters of alpine lakes, the Iced Sea Automatic features a unique oxygen-free case, ensuring longevity and clarity even in extreme conditions. The watch''s luminous markers and robust bezel combine sportiness with sophistication, making it an ideal companion for adventurous souls who refuse to compromise elegance for performance.', 9000.00, 20, 'assets/images/products/montblac/montblac2.png'),
('W008', 'MontBlac', 'Star Legacy Small Second 36 mm', 'This refined 36mm dress watch encapsulates the essence of understated luxury. The delicately crafted small-seconds subdial adds subtle complexity to its otherwise minimalist white dial, while the polished stainless steel case reflects light with graceful sophistication. A perfect companion for formal occasions or quiet moments of distinction.', 8000.00, 20, 'assets/images/products/montblac/montblac3.png'),
('W009', 'MontBlac', 'Star Legacy Orbis Terrarum', 'The Star Legacy Orbis Terrarum is a celebration of world exploration and precision engineering. Its intricate globe dial presents a sophisticated world-time complication that allows the wearer to instantly read the time across all 24 time zones. Encased in polished stainless steel and accented with elegant guilloché patterns, this timepiece is as much a conversation piece as it is a practical instrument for the cosmopolitan traveler.', 10000.00, 20, 'assets/images/products/montblac/montblac4.png'),
('W010', 'MontBlac', 'Tradition Automatic Date 40 mm', 'A quintessential automatic watch that marries classical aesthetics with modern reliability. Its 40mm case houses a perfectly balanced dial, featuring subtle markers and a practical date window. Whether for boardroom meetings or casual elegance, the Tradition Automatic embodies Montblanc''s commitment to timeless design and exceptional craftsmanship.', 7500.00, 20, 'assets/images/products/montblac/montblac5.png'),
('W011', 'Petek', 'Grand Complications 5303R-001', 'The Grand Complications 5303R-001 is a breathtaking display of mechanical artistry. Its skeletonized dial offers a window into the intricate movement beneath, allowing admirers to witness the harmony of gears, springs, and levers in motion. Crafted for connoisseurs of haute horology, this piece exemplifies Patek Philippe''s mastery of both technical complexity and aesthetic perfection.', 10000.00, 20, 'assets/images/products/petek/petek1.png'),
('W012', 'Petek', 'Golden Ellipse 5738R-001', 'Revered for its mathematically inspired elliptical case, the Golden Ellipse 5738R-001 blends classical design with a modern sensibility. The rose-gold case gleams softly under light, while the minimalistic dial reflects a commitment to purity and elegance. This watch is an ideal expression of quiet sophistication, suited for the wearer who values both style and intellectual refinement.', 9500.00, 20, 'assets/images/products/petek/petek2.png'),
('W013', 'Petek', 'Grand Complications Annual Calendar', 'This annual calendar timepiece combines functional complexity with aesthetic refinement. Its polished case and harmonious dial design present a sophisticated statement for the discerning wearer.', 9000.00, 20, 'assets/images/products/petek/petek3.png'),
('W014', 'Petek', 'Complications 5205R-001', 'A masterclass in functionality and beauty, the Complications 5205R-001 presents an annual calendar complication with effortless elegance. Its polished case, delicate dial layout, and intricately finished hands make it a statement piece that seamlessly combines mechanical ingenuity with refined aesthetics.', 8500.00, 20, 'assets/images/products/petek/petek4.png'),
('W015', 'Petek', 'Calatrava 5227J-001', 'The Calatrava 5227J-001 epitomizes minimalist elegance. Its clean dial, slender hands, and classic case design pay homage to Patek Philippe''s timeless philosophy of restraint and refinement. A watch that complements every attire, it stands as a symbol of understated luxury and lasting value.', 9500.00, 20, 'assets/images/products/petek/petek5.png'),
('W016', 'Rolex', 'Explorer I 36 14270 1996', 'Designed for adventurers and explorers, the Explorer I is celebrated for its robustness, clarity, and legendary reliability. Its 36mm case offers perfect proportions for everyday wear, while the simple, luminous dial ensures maximum legibility under all conditions. A classic Rolex icon that has inspired generations of collectors and enthusiasts alike.', 10000.00, 20, 'assets/images/products/rolex/rolex1.png'),
('W017', 'Rolex', 'Datejust 26 Two Tone Yellow Champagne Crystal Flak', 'The Datejust 26 exudes timeless glamour, combining the warmth of yellow gold with the sparkle of a diamond-set bezel. Its champagne dial glows with understated elegance, making it an ideal watch for those who appreciate luxury in every detail. Compact yet commanding, this piece perfectly marries sophistication with everyday versatility.', 10000.00, 20, 'assets/images/products/rolex/rolex2.png'),
('W018', 'Rolex', 'Air-King 116900 2018', 'A tribute to Rolex''s aviation heritage, the Air-King 116900 features a bold, high-contrast dial and a robust Oystersteel case. Its precision and sporty aesthetic make it a dynamic choice for individuals who embrace adventure without compromising style.', 9500.00, 20, 'assets/images/products/rolex/rolex3.png'),
('W019', 'Rolex', 'Datejust 28 Two Tone Yellow Gold White Roman Dial', 'An elegant blend of classic and contemporary design, this 28mm Datejust features a two-tone case and pristine white dial adorned with Roman numerals. It''s a refined and versatile timepiece that elevates both casual and formal ensembles.', 9000.00, 20, 'assets/images/products/rolex/rolex4.png'),
('W020', 'Rolex', 'Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 10000.00, 20, 'assets/images/products/rolex/rolex5.png');

-- Indexes
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `watch`
  ADD PRIMARY KEY (`watch_id`);

ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`);

ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `fk_cartitems_watch` (`watch_id`),
  ADD KEY `fk_cartitems_cart` (`cart_id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `fk_orders_watch` (`watch_id`),
  ADD KEY `idx_orders_order_date` (`order_date`);

ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_order` (`order_id`);

ALTER TABLE `paypalpayment`
  ADD PRIMARY KEY (`paypal_transaction_id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`);

-- Foreign keys
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cartitems`
  ADD CONSTRAINT `fk_cartitems_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cartitems_watch` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`watch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_watch` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`watch_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `paypalpayment`
  ADD CONSTRAINT `fk_paypalpayment_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
