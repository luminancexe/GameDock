-- phpMyAdmin-compatible SQL rebuilt from MariaDB dump source
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `forum_posts`;
DROP TABLE IF EXISTS `pc_requirements`;
DROP TABLE IF EXISTS `purchases`;
DROP TABLE IF EXISTS `rentals`;
DROP TABLE IF EXISTS `sell_games`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `games`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES
(1,'Demo User','demo@gamehub.com','1234567890','$2y$10$dQXKXmZRARWznmZpw0ZGsOvv7YL6rSRQ3BjulfTOZK5SlfnuaMGdi','2026-07-12 15:33:04','default.png'),
(2,'Demo User','demo@gamedock.com','1234567890','$2y$10$ED.inZ.BhtlRAcOGmf32TuKmABhOLFfhEp0h9XXnDks72irM/nN2G','2026-07-15 16:18:32','default.png');

CREATE TABLE `contacts` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `games` (
  `game_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `platform` enum('PC','PS4','PS5','Xbox') NOT NULL,
  `category` varchar(50) NOT NULL,
  `service_type` enum('pc_purchase','ps_rental','xbox_rental') NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `rent_price` decimal(10,2) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT 'default-game.jpg',
  `stock` int(10) unsigned DEFAULT 0,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`game_id`),
  KEY `idx_games_title` (`title`),
  KEY `idx_games_platform` (`platform`),
  KEY `idx_games_category` (`category`),
  KEY `idx_games_service_type` (`service_type`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `games` VALUES
(1,'Red Dead Redemption 2','PC','Action','pc_purchase',59.99,NULL,'An open-world action-adventure game with story and offline-play support where permitted.','red-dead-redemption-2.jpg',10,'available','2026-07-12 15:25:42'),
(2,'Cyberpunk 2077','PC','RPG','pc_purchase',49.99,NULL,'A futuristic role-playing game set in Night City.','Cyberpunk-2077-Phantom-Liberty-PS5.jpg',8,'available','2026-07-12 15:25:42'),
(3,'Forza Horizon 5','PC','Racing','pc_purchase',59.99,NULL,'An open-world racing game with a large collection of cars.','f31forza-horizon-5.jpg',6,'available','2026-07-12 15:25:42'),
(4,'God of War Ragnarok','PS5','Action','ps_rental',NULL,12.00,'A story-driven action-adventure title available as a demonstration rental listing.','god of war ragnarok.jpeg',4,'available','2026-07-12 15:25:42'),
(5,'EA Sports FC 26','PS5','Sports','ps_rental',NULL,10.00,'A football simulation game available for selected rental periods.','EA-Sports-FC-262.jpg',3,'available','2026-07-12 15:25:42'),
(6,'The Last of Us Part II','PS4','Adventure','ps_rental',NULL,10.00,'A cinematic action-adventure game available for PS4 rental.','the last of us 2.jpg',3,'available','2026-07-12 15:25:42'),
(7,'Elden Ring','PC','RPG','pc_purchase',59.99,NULL,'A massive open-world fantasy action-RPG.','elden ring.jpg',15,'available','2026-07-12 15:29:30'),
(8,'Hogwarts Legacy','PC','RPG','pc_purchase',49.99,NULL,'An immersive, open-world action RPG set in the world of Harry Potter.','hogwarts legacy.jpeg',20,'available','2026-07-12 15:29:30'),
(9,'Spider-Man 2','PS5','Action','ps_rental',NULL,15.00,'Swing through New York as Peter Parker and Miles Morales.','spiderman 2.jpg',5,'available','2026-07-12 15:29:30'),
(10,'Ghost of Tsushima','PS5','Adventure','ps_rental',NULL,10.00,'Uncover the hidden wonders of Tsushima in this open-world action adventure.','GhostofTsushima_1.jpg',3,'available','2026-07-12 15:29:30'),
(11,'Resident Evil 4','PC','Horror','pc_purchase',59.99,NULL,'A total reimagining of the 2005 survival-horror classic. Rescue the President''s daughter from a remote, cult-infested village in Spain.','resident-evil-4.jpg',18,'available','2026-07-15 15:11:14'),
(12,'Resident Evil Village','PC','Horror','pc_purchase',39.99,NULL,'Ethan Winters searches for his kidnapped daughter in a nightmarish village ruled by four mysterious lords.','resident-evil-village.png',20,'available','2026-07-15 15:11:14'),
(13,'Final Fantasy VII Rebirth','PC','RPG','pc_purchase',69.99,NULL,'The second chapter of the Final Fantasy VII remake trilogy. Cloud and friends chase Sephiroth across the wide world beyond Midgar.','final-fantasy-7-rebirth.jpg',15,'available','2026-07-15 15:11:14'),
(14,'Baldur''s Gate 3','PC','RPG','pc_purchase',59.99,NULL,'A story-rich, party-based RPG set in the world of Dungeons and Dragons, with unmatched freedom to explore, fight, and romance your way through Faerun.','baldurs-gate-3.jpg',25,'available','2026-07-15 15:11:14'),
(15,'S.T.A.L.K.E.R. 2: Heart of Chornobyl','PC','Survival','pc_purchase',59.99,NULL,'A story-driven open-world survival shooter set in the deadly, anomaly-ridden Chornobyl Exclusion Zone.','stalker-2-heart-of-chornobyl.jpg',12,'available','2026-07-15 15:11:14'),
(16,'Lies of P','PC','Action','pc_purchase',49.99,NULL,'A dark, soulslike reimagining of Pinocchio, fighting to become a real human in a collapsing, puppet-infested city.','lies-of-p.png',16,'available','2026-07-15 15:11:14'),
(17,'Star Wars Jedi: Survivor','PC','Action','pc_purchase',69.99,NULL,'Cal Kestis returns in this galaxy-spanning action-adventure, wielding the lightsaber and the Force against the remnants of the Empire.','star-wars-jedi-survivor.jpg',14,'available','2026-07-15 15:11:14'),
(18,'Marvel''s Wolverine','PS5','Action','ps_rental',NULL,14.00,'An adult, brutal, open-world adventure starring Logan as he claws his way through Sabretooth-controlled New York.','marvels-wolverine.jpg',4,'available','2026-07-15 15:13:30'),
(19,'Halo: Campaign Evolved','PS5','FPS','ps_rental',NULL,10.00,'The original Halo: Combat Evolved rebuilt from the ground up. Relive the Master Chief''s first fight against the Covenant and the Flood.','halo-campaign-evolved.png',5,'available','2026-07-15 15:13:30'),
(20,'Resident Evil Requiem','PS5','Horror','ps_rental',NULL,12.00,'The next chapter of Resident Evil survival-horror, returning to Raccoon City with a new protagonist caught in a fresh nightmare.','resident-evil-requiem.webp',4,'available','2026-07-15 15:13:30'),
(21,'Nioh 3','PS5','Action','ps_rental',NULL,11.00,'A brutal souls-like set in feudal Japan, blending stances, spirit weapons, and yokai-infested battlefields.','nioh-3.jpg',5,'available','2026-07-15 15:13:30'),
(22,'Minecraft','Xbox','Sandbox','xbox_rental',NULL,6.00,'Build, mine, and survive in a blocky, procedurally generated world with friends or solo.','minecraft.jpg',8,'available','2026-07-15 16:03:35'),
(23,'Halo Infinite','Xbox','FPS','xbox_rental',NULL,10.00,'Master Chief returns to face the Banished across the ringworld Zeta Halo in this sprawling sci-fi shooter.','halo-infinite.webp',6,'available','2026-07-15 16:03:35'),
(24,'Farming Simulator 15','Xbox','Simulation','xbox_rental',NULL,5.00,'Run your own farm, drive authentic tractors and machinery, and grow crops and livestock into a full agribusiness.','farming-simulator-15.jpg',5,'available','2026-07-15 16:03:35'),
(25,'Fallout 4','Xbox','RPG','xbox_rental',NULL,9.00,'Emerge from Vault 111 into the irradiated wasteland of the Commonwealth and rebuild what was lost.','fallout-4.jpg',6,'available','2026-07-15 16:03:35'),
(26,'Gears 5','Xbox','Action','xbox_rental',NULL,10.00,'Kait Diaz uncovers the origins of the Locust as she leads Delta squad across the frozen frontier of Sera.','gears-5.jpg',6,'available','2026-07-15 16:04:13'),
(27,'Gears of War','Xbox','Action','xbox_rental',NULL,6.00,'The original cover-based shooter that started it all, as the COG make their last stand against the Locust Horde.','gears-of-war.jpg',5,'available','2026-07-15 16:04:13'),
(28,'Avowed','Xbox','RPG','xbox_rental',NULL,10.00,'A first-person fantasy RPG set in the world of Eora, where you explore the Living Lands and battle a mysterious plague.','avowed.jpg',6,'available','2026-07-15 16:04:13'),
(29,'Indiana Jones and the Great Circle','Xbox','Adventure','xbox_rental',NULL,10.00,'Whip-crack your way through a globe-trotting pulp adventure as Indiana Jones, chasing down a mystery tied to ancient relics.','indiana-jones-and-the-great-circle.webp',6,'available','2026-07-15 16:04:13');

CREATE TABLE `forum_posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `forum_posts` VALUES
(1,1,'Anyone excited for the new GTA 6 trailer?','I just saw the latest rumors and it looks like we might get another trailer next month. What features are you all hoping to see in the new game?','2026-07-14 05:07:23'),
(2,1,'Best build for Elden Ring DLC?','I am struggling with the new bosses in Shadow of the Erdtree. Does anyone have a good strength/faith build recommendation? Using the Blasphemous Blade right now.','2026-07-14 05:07:23'),
(3,1,'GameHub PS5 Rental Review','Just rented Ghost of Tsushima from here. The process was super smooth and the game is absolutely gorgeous on the PS5. Highly recommend the rental service if you want to try before you buy.','2026-07-14 05:07:23');

CREATE TABLE `pc_requirements` (
  `requirement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(10) unsigned NOT NULL,
  `operating_system` varchar(100) NOT NULL,
  `processor` varchar(150) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `graphics` varchar(150) NOT NULL,
  `storage` varchar(50) NOT NULL,
  PRIMARY KEY (`requirement_id`),
  KEY `fk_pc_requirements_game` (`game_id`),
  CONSTRAINT `fk_pc_requirements_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pc_requirements` VALUES
(1,1,'Windows 10 64-bit','Intel Core i5-2500K or AMD FX-6300','8 GB','NVIDIA GTX 770 2 GB or AMD Radeon R9 280 3 GB','150 GB'),
(2,2,'Windows 10 64-bit','Intel Core i7-6700 or AMD Ryzen 5 1600','12 GB','NVIDIA GTX 1060 6 GB or AMD Radeon RX 580 8 GB','70 GB'),
(3,3,'Windows 10 64-bit','Intel Core i5-4460 or AMD Ryzen 3 1200','8 GB','NVIDIA GTX 970 or AMD RX 470','110 GB');

CREATE TABLE `purchases` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `game_id` int(10) unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','bKash','Nagad','Card','Demo Payment') DEFAULT 'Demo Payment',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `order_status` enum('processing','completed','cancelled') DEFAULT 'processing',
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`purchase_id`),
  KEY `fk_purchases_game` (`game_id`),
  KEY `idx_purchases_user_id` (`user_id`),
  CONSTRAINT `fk_purchases_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_purchases_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `purchases` VALUES
(1,1,1,450.00,'Demo Payment','paid','completed','2026-07-12 15:33:55'),
(2,1,1,450.00,'Demo Payment','paid','completed','2026-07-12 16:46:45');

CREATE TABLE `rentals` (
  `rental_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `game_id` int(10) unsigned NOT NULL,
  `console` enum('PS4','PS5') NOT NULL,
  `rental_days` int(10) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','bKash','Nagad','Card','Demo Payment') DEFAULT 'Demo Payment',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `rental_status` enum('pending','active','expired','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rental_id`),
  KEY `fk_rentals_game` (`game_id`),
  KEY `idx_rentals_user_id` (`user_id`),
  CONSTRAINT `fk_rentals_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_rentals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rentals` VALUES
(1,1,4,'PS5',3,'2026-07-12','2026-07-15',300.00,'Demo Payment','paid','active','2026-07-12 15:34:45'),
(2,1,5,'PS5',30,'2026-07-12','2026-08-11',3600.00,'Demo Payment','paid','active','2026-07-12 16:47:15');

CREATE TABLE `sell_games` (
  `sell_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `platform` enum('PC','PS4','PS5') NOT NULL,
  `product_type` enum('Game Key','Disc','Digital License','Other') DEFAULT 'Other',
  `asking_price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT 'default-game.jpg',
  `status` enum('submitted','listed','sold','removed') DEFAULT 'submitted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sell_id`),
  KEY `idx_sell_games_user_id` (`user_id`),
  CONSTRAINT `fk_sell_games_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sell_games` VALUES
(1,1,'fef','PC','Game Key',0.08,'','6a53b408609d3.jpg','submitted','2026-07-12 15:34:32'),
(2,1,'gaming','PC','Other',23.00,'','6a53c529d881d.jpg','submitted','2026-07-12 16:47:37');

CREATE TABLE `wishlists` (
  `wishlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `game_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `user_game` (`user_id`,`game_id`),
  KEY `fk_wishlist_game` (`game_id`),
  CONSTRAINT `fk_wishlist_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `wishlists` VALUES
(2,1,3,'2026-07-12 16:46:52'),
(3,1,2,'2026-07-12 16:46:54'),
(4,1,5,'2026-07-12 16:47:05');

SET FOREIGN_KEY_CHECKS=1;