-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 04:46 PM
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
-- Database: `game_center`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `image_url` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `genre`, `price`, `image_url`, `image_path`, `description`, `created_at`) VALUES
(15, 'Cyberpunk 2077', 'RPG', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1091500/header.jpg?t=1734434803', NULL, 'Cyberpunk 2077 is an open-world, action-adventure RPG set in the megalopolis of Night City, where you play as a cyberpunk mercenary wrapped up in a do-or-die fight for survival. Improved and featuring all-new free additional content, customize your character and playstyle as you take on jobs, build a reputation, and unlock upgrades. The relationships you forge and the choices you make will shape the story and the world around you. Legends are made here. What will yours be?', '2026-04-14 22:21:49'),
(16, 'Red Dead Redemption 2', 'Adventure', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1174180/header.jpg?t=1720558643', NULL, 'America, 1899. Arthur Morgan and the Van der Linde gang are outlaws on the run. With federal agents and the best bounty hunters in the nation massing on their heels, the gang must rob, steal and fight their way across the rugged heartland of America in order to survive.', '2026-04-14 22:21:49'),
(17, 'Grand Theft Auto VI', 'Open World Action-adventure', 60.00, 'https://i.ytimg.com/vi/hwIoeJTtExk/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLCaLifasXQEdFYkH3HIS4NpvWytZg', NULL, 'Grand Theft Auto VI heads to the state of Leonida, home to the neon-soaked streets of Vice City and beyond in the biggest, most immersive evolution of the Grand Theft Auto series yet.', '2026-04-14 22:21:49'),
(18, 'Ghost of Tsushima Director\'s Cut', 'Action-adventure', 69.99, 'https://helios-i.mashable.com/imagery/articles/00iMVz5oU69RK9UEoPsZTMW/hero-image.fill.size_1248x702.v1623390188.jpg', NULL, 'Uncover the hidden wonders of Tsushima in this open-world action adventure from Sucker Punch Productions and PlayStation Studios, available for PS5 and PS4.', '2026-04-14 22:21:49'),
(21, 'The Witcher 3: Wild Hunt', 'RPG', 45.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/292030/header.jpg?t=1736424367', NULL, 'You are Geralt of Rivia, mercenary monster slayer. Before you stands a war-torn, monster-infested continent you can explore at will. Your current contract? Tracking down Ciri - the Child of Prophecy, a living weapon that can alter the shape of the world.', '2026-04-14 22:21:49'),
(22, 'Crysis 3 Remastered', 'Action', 40.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/2096610/header.jpg?t=1709652109', NULL, 'In Crysis 3 Remastered, the fate of the world is once again in your hands. Returning to the fight as super-soldier Prophet, wielding a powerful Predator Bow, take on new and old enemies that threaten the peace you worked so hard to achieve.', '2026-04-14 22:21:49'),
(24, 'Counter-Strike: Global Offensive', 'Shooting-FPS', 0.00, 'https://media.steampowered.com/apps/csgo/blog/images/fb_image.png?v=6', NULL, 'Counter-Strike: Global Offensive (CS:GO) is a 2012 multiplayer tactical first-person shooter developed by Valve and Hidden Path Entertainment. It is the fourth game in the Counter-Strike series.', '2026-04-14 22:21:49'),
(25, 'Counter Strike 2', 'Shooting-FPS', 0.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/730/header.jpg?t=1745368595', NULL, 'For over two decades, Counter-Strike has offered an elite competitive experience, one shaped by millions of players from across the globe. And now the next chapter in the CS story is about to begin. This is Counter-Strike 2.', '2026-04-14 22:21:49'),
(38, 'Watch Dogs: Legion', 'Action', 50.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2239550/header.jpg?t=1736258947', NULL, 'Build a resistance made from anyone in the world to take back a near-future London that is facing its downfall. Recruit and play as anyone from London. Everyone you see has a unique backstory, personality, and skill set. Hack armed drones, deploy spider-bots, and take down enemies using an Augmented Reality Cloak.', '2026-04-14 22:21:49'),
(39, 'Valorant', 'Shooting-FPS', 0.00, 'https://cmsassets.rgpub.io/sanity/images/dsfx7636/news_live/f657721a7eb06acae52a29ad3a951f20c1e5fc60-1920x1080.jpg', NULL, 'VALORANT is a global arena where you can compete. A 5v5 tactical shooter where players take turns planting and neutralizing spikes, with a one-life-per-round system for 13 rounds. Use fast, lethal, and situationally adaptable abilities to create opportunities with your superior weaponry.', '2026-04-14 22:21:49'),
(40, 'League of Legends', 'FPS', 0.00, 'https://cdn1.epicgames.com/offer/24b9b5e323bc40eea252a10cdd3b2f10/EGS_LeagueofLegends_RiotGames_S1_2560x1440-80471666c140f790f28dff68d72c384b?resize=1&w=480&h=270&quality=medium', NULL, 'League of Legends (LoL), commonly referred to as League, is a 2009 multiplayer online battle arena video game developed and published by Riot Games. Inspired by Defense of the Ancients, a custom map for Warcraft III.', '2026-04-14 22:21:49'),
(41, 'Assassin\'s Creed Shadows', 'Action-Adventure', 70.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/3159330/header.jpg?t=1742705129', NULL, 'Set in feudal Japan, this installment introduces dual protagonists: Naoe, a shinobi assassin, and Yasuke, a Black samurai. The game emphasizes stealth, exploration, and a rich historical narrative.', '2026-04-14 22:21:49'),
(42, 'Monster Hunter Wilds', 'Action RPG', 70.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2246340/header.jpg?t=1745366582', NULL, 'An open-world monster hunting game featuring dynamic weather systems and massive behemoths. Players can craft gear from defeated monsters and explore a vast ecosystem.', '2026-04-14 22:21:49'),
(43, 'Blue Prince', 'Puzzle, Adventure', 27.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1569580/header.jpg?t=1744394425', NULL, 'A narrative-driven puzzle game where players construct rooms in a mysterious mansion, uncovering secrets and stories with each new space.', '2026-04-14 22:21:49'),
(44, 'Clair Obscur: Expedition 33', 'RPG', 0.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1903340/be3305b02d4db0dffa3458537118423bf2792d7e/header.jpg?t=1745509513', NULL, 'An RPG featuring turn-based combat and a dark-fantasy setting inspired by French revolutionary imagery. The game boasts a notable voice cast including Andy Serkis and Charlie Cox.', '2026-04-14 22:21:49'),
(46, 'Final Fantasy XVI', 'RPG', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/2515020/extras/FAITH_Steam_Demo_Main_Capsule_616x353px_english.jpg?t=1741059170', NULL, 'The next chapter in the legendary RPG series, with an all-new combat system, a rich fantasy world, and a gripping story set in the land of Valisthea.', '2026-04-14 22:21:49'),
(47, 'Hogwarts Legacy', 'Action RPG', 60.00, 'https://assets.nintendo.com/image/upload/q_auto/f_auto/ncom/software/switch/70070000019147/8d6950111fb9a0ece31708dcd6ac893f93c012bc585a6a09dfd986d56ab483d1', NULL, 'An immersive RPG set in the wizarding world of Harry Potter. Explore an open world as a student at Hogwarts, mastering magic and uncovering secrets.', '2026-04-14 22:21:49'),
(48, 'The Legend of Zelda: Tears of the Kingdom', 'Adventure', 70.00, 'https://media.nichegamer.com/wp-content/uploads/2023/05/the-legend-of-zelda-tears-of-the-kingdom-05-23-23-1.jpeg', NULL, 'Embark on an epic adventure with Link to save the kingdom of Hyrule. Solve puzzles, fight enemies, and uncover hidden secrets in this highly anticipated sequel.', '2026-04-14 22:21:49'),
(49, 'Spider-Man 2', 'Action-Adventure', 60.00, 'https://image.api.playstation.com/vulcan/ap/rnd/202306/1219/97e9f5fa6e50c185d249956c6f198a2652a9217e69a59ecd.jpg', NULL, 'The next installment in the Spider-Man series. Play as both Peter Parker and Miles Morales to save New York City from a new wave of threats.', '2026-04-14 22:21:49'),
(50, 'Starfield', 'RPG', 70.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1716740/header.jpg?t=1727384525', NULL, 'A vast space RPG from Bethesda Game Studios. Explore the universe, build your own spaceship, and embark on a journey to uncover the mysteries of space.', '2026-04-14 22:21:49'),
(51, 'Resident Evil 4 Remake', 'Survival Horror', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2050650/header.jpg?t=1736385712', NULL, 'A remake of the iconic survival horror game. Follow Leon S. Kennedy as he attempts to rescue the president\'s daughter in a terrifying and dangerous village.', '2026-04-14 22:21:49'),
(52, 'Elden Ring', 'Action RPG', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1245620/capsule_616x353.jpg?t=1744748041', NULL, 'From the makers of Dark Souls, this game features a massive open world, challenging combat, and deep lore. Players explore the Lands Between and uncover the mysteries surrounding the Elden Ring.', '2026-04-14 22:21:49'),
(53, 'Diablo IV', 'Action RPG', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2344520/header.jpg?t=1744225365', NULL, 'The latest installment in the iconic Diablo series. Experience the dark, action-packed gameplay with a variety of classes and an expansive open world to explore.', '2026-04-14 22:21:49'),
(54, 'Baldurs Gate 3', 'RPG', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1086940/6b6b46089067683cecf6acfc92b39fc4c72e3fac/header.jpg?t=1744744220', NULL, 'A new chapter in the Baldurs Gate series set in the Dungeons & Dragons universe. Features turn-based combat, rich character customization, and a branching story.', '2026-04-14 22:21:49'),
(55, 'Star Wars Jedi: Survivor', 'Action-Adventure', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1774580/header.jpg?t=1701206599', NULL, 'The next installment in the Star Wars Jedi series. Play as Cal Kestis and continue his journey as he fights to survive in the galaxy under the rule of the Empire.', '2026-04-14 22:21:49'),
(56, 'Alan Wake 2', 'Survival Horror', 60.00, 'https://image.api.playstation.com/vulcan/ap/rnd/202305/2601/47b97b7a4e7493d9701c7ecabdbdfff0ea49c61effeb6299.jpg', NULL, 'The long-awaited sequel to the psychological thriller Alan Wake. Uncover more mysteries as you dive into a new chapter in the eerie world of Alan Wake.', '2026-04-14 22:21:49'),
(57, 'Hollow Knight: Silksong', 'Action-Adventure', 30.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1030300/header.jpg?t=1742776298', NULL, 'The highly anticipated sequel to the indie hit Hollow Knight. Explore a new world, battle fierce enemies, and uncover secrets in this action-packed Metroidvania.', '2026-04-14 22:21:49'),
(58, 'The Witcher 4', 'RPG', 60.00, 'https://gamingbolt.com/wp-content/uploads/2024/12/the-witcher-4-image-3.jpeg', NULL, 'Geralt of Rivia returns in the next chapter of The Witcher series. This open-world RPG continues the story of the famed monster hunter, with a new storyline and setting.', '2026-04-14 22:21:49'),
(59, 'Forza Motorsport 8', 'Racing', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2440510/header.jpg?t=1741989226', NULL, 'The next installment in the Forza Motorsport series. Race through a variety of tracks, customize your vehicles, and experience realistic graphics and physics.', '2026-04-14 22:21:49'),
(60, 'Fable 4', 'RPG', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2769570/header.jpg?t=1717960572', NULL, 'A new adventure in the Fable universe. Build your legend as you explore a fantastical world filled with magic, quests, and moral choices.', '2026-04-14 22:21:49'),
(61, 'Street Fighter 6', 'Fighting', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1364780/header.jpg?t=1745549689', NULL, 'The latest in the Street Fighter series. Engage in epic one-on-one battles with a new roster of fighters and improved mechanics for a fresh fighting experience.', '2026-04-14 22:21:49'),
(62, 'God of War Ragnarok', 'Action-Adventure', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/2322010/capsule_616x353.jpg?t=1738256985', NULL, 'Join Kratos and Atreus on an epic journey through Norse mythology in this action-packed adventure. Battle gods, explore the Nine Realms, and uncover secrets of the fate that awaits them.', '2026-04-14 22:21:49'),
(63, 'Far Cry', 'First-Person Shooter', 40.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/13520/header.jpg?t=1602602361', NULL, 'The original Far Cry introduces players to an open-world FPS with a variety of combat styles and strategies in a jungle environment.', '2026-04-14 22:21:49'),
(64, 'Far Cry 2', 'First-Person Shooter', 40.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/19900/header.jpg?t=1709317476', NULL, 'A follow-up to Far Cry, this installment takes place in Africa, with a new protagonist and a focus on realistic combat and resource management.', '2026-04-14 22:21:49'),
(65, 'Far Cry 3', 'First-Person Shooter', 50.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/220240/header.jpg?t=1738250672', NULL, 'A beloved installment where players take on the role of Jason Brody, stranded on a tropical island, fighting to survive against pirates and mercenaries.', '2026-04-14 22:21:49'),
(66, 'Far Cry 4', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/298110/header.jpg?t=1739176495', NULL, 'Set in the Himalayan region, this game features a new protagonist and open-world exploration, vehicular combat, and wild animals.', '2026-04-14 22:21:49'),
(67, 'Far Cry 5', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/552520/header.jpg?t=1738249745', NULL, 'Set in a rural Montana town, players fight against a doomsday cult. It introduces dynamic events and the ability to recruit allies in an open-world environment.', '2026-04-14 22:21:49'),
(68, 'Far Cry New Dawn', 'First-Person Shooter', 40.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/939960/header.jpg?t=1745423863', NULL, 'A direct sequel to Far Cry 5 set in a post-apocalyptic world, where players fight for survival in a colorful yet dangerous environment.', '2026-04-14 22:21:49'),
(69, 'Far Cry 6', 'First-Person Shooter', 60.00, 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/2369390/header.jpg?t=1738249424', NULL, 'Set in the fictional country of Yara, players fight to overthrow a dictator in this action-packed installment featuring the first open-world Far Cry game with a female protagonist.', '2026-04-14 22:21:49'),
(70, 'Far Cry Primal', 'First-Person Shooter', 50.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/371660/header.jpg?t=1694554883', NULL, 'A spin-off set in the prehistoric age, where players use primitive weapons and hunt wild animals in a world before modern technology.', '2026-04-14 22:21:49'),
(71, 'Far Cry 3: Blood Dragon', 'First-Person Shooter', 20.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/233270/header.jpg?t=1733168266', NULL, 'A standalone spin-off of Far Cry 3 set in a neon-lit 1980s-inspired world, offering a fun, over-the-top action experience with a retro-futuristic style.', '2026-04-14 22:21:49'),
(72, 'Call of Duty: Modern Warfare II', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2000950/header.jpg?t=1678294805', NULL, 'A reimagining of the classic Call of Duty: Modern Warfare, featuring intense multiplayer modes, a gripping single-player campaign, and a variety of new weapons and tactics.', '2026-04-14 22:21:49'),
(73, 'DOOM Eternal', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/782330/header.jpg?t=1702308063', NULL, 'The highly anticipated sequel to DOOM, delivering fast-paced combat, an arsenal of weapons, and a battle against hellish creatures in stunning environments.', '2026-04-14 22:21:49'),
(74, 'Halo Infinite', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1240440/3c103afc1db13776d5d411ccfd3037e2f43f3a5b/header.jpg?t=1744138118', NULL, 'The latest installment in the Halo franchise featuring Master Chief in a new chapter of the story. The game boasts incredible multiplayer modes, vehicles, and an open-world experience.', '2026-04-14 22:21:49'),
(75, 'Battlefield V', 'First-Person Shooter', 40.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1238810/header.jpg?t=1738598409', NULL, 'Set during World War II, Battlefield V offers large-scale, destructive multiplayer battles with dynamic weather conditions and destructible environments.', '2026-04-14 22:21:49'),
(76, 'Rainbow Six Siege', 'Tactical Shooter', 40.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/359550/header.jpg?t=1742315874', NULL, 'A tactical first-person shooter that focuses on team-based, strategic combat. Players take on the role of elite operators who must work together to complete high-stakes missions.', '2026-04-14 22:21:49'),
(77, 'Battlefield 2042', 'First-Person Shooter', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1517290/header.jpg?t=1744718390', NULL, 'Battlefield 2042 is a next-generation first-person shooter featuring large-scale multiplayer battles, advanced vehicles, and dynamic environments set in a futuristic world of warfare.', '2026-04-14 22:21:49'),
(78, 'Microsoft Flight Simulator', 'Simulation', 60.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/1250410/header.jpg?t=1740686114', NULL, 'Microsoft Flight Simulator offers the most realistic flight simulation experience ever created, featuring a vast open world with real-time weather conditions and accurate aircraft models.', '2026-04-14 22:21:49'),
(79, 'Among Us', 'Party Game', 5.00, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/945360/header.jpg?t=1731953093', NULL, 'Among Us is an online multiplayer party game where players work together to complete tasks on a spaceship while trying to unmask the Impostor who is sabotaging them. With its fun and suspenseful gameplay, Among Us has become a social gaming phenomenon.', '2026-04-14 22:21:49'),
(80, 'Culling Game', 'Action', 999.00, 'https://static0.gamerantimages.com/wordpress/wp-content/uploads/2023/10/culling-game-strongest-characters.jpg', NULL, 'The Culling Game is the most unprecedented act of jujutsu terrorism ever enacted. Orchestrated by Kenjaku with the goal …', '2026-04-14 22:21:49'),
(81, 'Greed Island', 'Adventure', 9999.00, 'https://cdn2.inkarnate.com/cdn-cgi/image/width=1800,height=1400/https://inkarnate-api-as-production.s3.amazonaws.com/4AG5cnXa9YBCCq6EeVEBNA', NULL, 'Greed Island is a dangerous video game meant only for Hunters. Played on the JoyStation Console, it is out of print and …', '2026-04-14 22:21:49'),
(82, 'Hollow Knight: Silksong', 'Souls', 300.00, 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/1030300/7983574d464e6559ac7e24275727f73a8bcca1f3/header.jpg?t=1776125736', NULL, 'As the lethal hunter Hornet, adventure through a kingdom ruled by silk and song! Captured and taken to this unfamiliar world, prepare to battle mighty foes and solve ancient mysteries as you ascend on a deadly pilgrimage to the kingdom’s peak.\r\n\r\nHollow Knight: Silksong is the epic sequel to Hollow Knight, the award winning action-adventure. Journey to all-new lands, discover new powers, battle vast hordes of bugs and beasts and uncover secrets tied to your nature and your past.', '2026-04-19 18:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(1, 3, 99999999.99, 'cancelled', '2026-04-14 22:23:23'),
(2, 3, 99999999.99, 'completed', '2026-04-19 17:58:45'),
(3, 3, 99999999.99, 'completed', '2026-04-19 18:00:28'),
(4, 3, 50.00, 'completed', '2026-04-19 18:05:04'),
(5, 3, 0.00, 'completed', '2026-04-19 18:37:21'),
(6, 3, 60.00, 'completed', '2026-04-20 22:22:55'),
(7, 5, 60.00, 'completed', '2026-04-23 20:04:46'),
(8, 3, 40.00, 'completed', '2026-04-23 20:47:52'),
(9, 3, 40.00, 'completed', '2026-05-03 14:08:36'),
(10, 3, 300.00, 'completed', '2026-05-03 14:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `game_id`, `quantity`, `price`) VALUES
(1, 1, 80, 1, 99999999.99),
(2, 2, 81, 1, 99999999.99),
(3, 3, 80, 1, 99999999.99),
(4, 3, 78, 1, 60.00),
(5, 3, 65, 1, 50.00),
(6, 3, 59, 1, 60.00),
(7, 4, 70, 1, 50.00),
(8, 5, 39, 1, 0.00),
(9, 6, 46, 1, 60.00),
(10, 7, 56, 1, 60.00),
(11, 8, 68, 1, 40.00),
(12, 9, 64, 1, 40.00),
(13, 10, 82, 1, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `game_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 81, 5, 'Good one', '2026-04-23 19:56:33'),
(2, 5, 56, 5, 'Good', '2026-04-23 20:05:44'),
(3, 3, 65, 4, 'xxx', '2026-05-03 14:20:31'),
(4, 3, 82, 5, 'sss', '2026-05-03 14:28:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `avatar_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `avatar_path`, `created_at`) VALUES
(1, 'admin', 'admin@gamecenter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, '2026-04-14 22:09:39'),
(2, 'George Wassouf', 'georgew@gmail.com', '$2y$10$eeI9iNF8pd/WwCJh0lV5Ou3hkQSUKP46KXZdFpl/yNpT0sChdXmSa', 'admin', 'uploads/avatars/avatar_2_69e50f49967a7.jpg', '2026-04-14 22:09:49'),
(3, 'Ultra Male', 'ultramale200987@gmail.com', '$2y$10$JgiRFAiHkIZJTbZsImHJZegF4pPAIgatCum677tsxHEZdUHEI5oPC', 'user', 'uploads/avatars/avatar_3_69e50fb4d188c.jpg', '2026-04-14 22:23:02'),
(4, 'User1', 'user@gmail.com', '$2y$10$GBJNoTHR9C0yptSJIKhFH.yAvN3FZFAnirGxQd8LLvkZPy8N17SFG', 'user', NULL, '2026-04-19 16:55:27'),
(5, 'Mohany165', 'Mohany165@gmail.com', '$2y$10$7Ywg7RkZE6nm1I4hXjoVy.KcuRl0U3qZySOV245roTYjsjZffzSgK', 'user', 'uploads/avatars/avatar_5_69ea7b3dc7045.png', '2026-04-19 17:05:37'),
(6, 'Admin1', 'admin@gmail.com', '$2y$10$.4hXOhF69Vo/mR57VNPHcuwWSofJ2f9.1tcdaylL4EDrJWAIG7vzy', 'user', NULL, '2026-04-19 17:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_library`
--

CREATE TABLE `user_library` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_library`
--

INSERT INTO `user_library` (`id`, `user_id`, `game_id`, `purchase_date`) VALUES
(3, 3, 81, '2026-04-19 17:59:19'),
(4, 3, 80, '2026-04-19 18:00:47'),
(5, 3, 78, '2026-04-19 18:00:47'),
(6, 3, 65, '2026-04-19 18:00:47'),
(7, 3, 59, '2026-04-19 18:00:47'),
(8, 3, 70, '2026-04-19 18:05:27'),
(9, 3, 39, '2026-04-19 18:37:34'),
(10, 3, 46, '2026-04-20 22:23:14'),
(11, 5, 56, '2026-04-23 20:05:03'),
(12, 3, 68, '2026-04-23 20:49:04'),
(13, 3, 64, '2026-05-03 14:10:58'),
(14, 3, 82, '2026-05-03 14:26:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_game` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_game` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_library`
--
ALTER TABLE `user_library`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_game` (`user_id`,`game_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_library`
--
ALTER TABLE `user_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_library`
--
ALTER TABLE `user_library`
  ADD CONSTRAINT `lib_fk_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lib_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
