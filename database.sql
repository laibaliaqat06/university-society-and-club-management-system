-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 09:21 AM
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
-- Database: `universal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `society_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `society_id`, `created_by`, `created_at`) VALUES
(1, 'Join us now', 'Be a part of your university society', NULL, 1, '2026-03-04 00:56:44');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `certificate_hash` varchar(64) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `user_id`, `event_id`, `certificate_hash`, `generated_at`) VALUES
(1, 2, 116, '7cbb1c0323707bcfe82b4e0eb608facd8bdc0ee4c42062fefce8d6b2caf2bd91', '2026-03-04 01:03:33'),
(2, 2, 129, 'f00849e072cb5d83ee4f24aefef45d197293b05898ebc764e8638d226b411151', '2026-03-04 01:07:43'),
(3, 16, 119, '3e0c0561b62734016fb6949b68884ea21c48680bf3f12aaac0a28df8987595de', '2026-03-11 18:29:06');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `category` varchar(100) DEFAULT 'General',
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `president_info` text DEFAULT NULL,
  `faculty_advisors` text DEFAULT NULL,
  `core_committee` text DEFAULT NULL,
  `joining_rules` text DEFAULT NULL,
  `exit_rules` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `description`, `logo`, `created_by`, `created_at`, `cover_image`, `contact_email`, `contact_phone`, `social_links`, `category`, `mission`, `vision`, `president_info`, `faculty_advisors`, `core_committee`, `joining_rules`, `exit_rules`) VALUES
(3, 'Debating Society', 'For those who love to argue.', 'https://images.unsplash.com/photo-1544928147-79a2dbc1f389?auto=format&fit=crop&q=80&w=800', 2, '2026-02-16 17:24:01', 'https://images.unsplash.com/photo-1471439274527-a5169856ad96?auto=format&fit=crop&q=80&w=1200', 'debate@uni.com', '+123456789', NULL, 'Academic', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(4, 'Coding Club', 'For the tech enthusiasts.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800', 2, '2026-02-16 17:24:01', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=1200', 'code@uni.com', '+123456780', NULL, 'Tech', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(5, 'Art Club', 'Express yourself.', 'https://images.unsplash.com/photo-1460518451285-97b6aa32095a?auto=format&fit=crop&q=80&w=800', 2, '2026-02-16 17:24:01', 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?auto=format&fit=crop&q=80&w=1200', 'art@uni.com', '+123456781', NULL, 'Arts', NULL, NULL, 'John Doe (Senior, Computer Science)\r\nContact: john.president@uni.edu\r\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\r\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\r\nAlice Johnson (Secretary)\r\nBob Brown (Treasurer)\r\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\r\n2. Fill out the membership application form online.\r\n3. Pay the annual membership fee of $15.\r\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\r\n2. Return any club property or equipment.\r\n3. Hand over any pending responsibilities if you hold a committee position.'),
(6, 'Music Society', 'Feel the rhythm.', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&q=80&w=800', 2, '2026-02-16 17:24:01', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?auto=format&fit=crop&q=80&w=1200', 'music@uni.com', '+123456782', NULL, 'Arts', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(7, 'IT & Innovation Club', 'A university based student innovation hub.', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800', 5, '2026-02-19 04:43:57', 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=1200', 'it@uni.com', '+123456783', NULL, 'Tech', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(8, 'Test Club', 'A club for testing purposes.', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=800', 4, '2026-02-19 04:43:57', 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&q=80&w=1200', 'test@uni.com', '+123456784', NULL, 'Tech', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(9, 'Science Club', 'Explore the wonders of science through experiments and events.', 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:09', 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&q=80&w=1200', 'science@uni.com', '+123456785', NULL, 'Academic', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(10, 'Literature Society', 'For the love of books, poetry, and prose.', 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&q=80&w=1200', 'literature@uni.com', '+123456786', NULL, 'Academic', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(11, 'Theater Group', 'Acting, writing, and stage production.', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1491321415170-c75cbedbf0fb?auto=format&fit=crop&q=80&w=1200', 'theater@uni.com', '+123456787', NULL, 'Arts', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(12, 'Football Club', 'Training, matches, and tournaments.', 'https://images.unsplash.com/photo-1518605368461-1e967a5b3a4d?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&q=80&w=1200', 'football@uni.com', '+123456788', NULL, 'Sports', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(13, 'Basketball Team', 'Dribble, shoot, and score.', 'https://images.unsplash.com/photo-1519861531473-9200262188bf?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&q=80&w=1200', 'basketball@uni.com', '+123456789', NULL, 'Sports', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(14, 'Robotics Club', 'Build and program robots from scratch.', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800', 4, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1535378620166-273708d44e4c?auto=format&fit=crop&q=80&w=1200', 'robotics@uni.com', '+123456790', NULL, 'Tech', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(15, 'Volunteers Society', 'Give back to the community and help those in need.', 'https://images.unsplash.com/photo-1593113580326-9e67ca2b6534?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&q=80&w=1200', 'volunteer@uni.com', '+123456791', NULL, 'Social', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.'),
(16, 'Green Environment Club', 'Promoting sustainability and ecological awareness.', 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800', 5, '2026-02-27 00:32:10', 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?auto=format&fit=crop&q=80&w=1200', 'green@uni.com', '+123456792', NULL, 'Social', NULL, NULL, 'John Doe (Senior, Computer Science)\nContact: john.president@uni.edu\nOffice Hours: Tue/Thu 2-4 PM', 'Dr. Alan Turing (Computer Science Dept)\nProf. Ada Lovelace (Mathematics Dept)', 'Jane Smith (Vice President)\nAlice Johnson (Secretary)\nBob Brown (Treasurer)\nCharlie Davis (Event Coordinator)', '1. Must be a currently enrolled student.\n2. Fill out the membership application form online.\n3. Pay the annual membership fee of $15.\n4. Attend at least one orientation session.', '1. Submit a formal resignation email to the Secretary.\n2. Return any club property or equipment.\n3. Hand over any pending responsibilities if you hold a committee position.');

-- --------------------------------------------------------

--
-- Table structure for table `club_gallery`
--

CREATE TABLE `club_gallery` (
  `id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_gallery`
--

INSERT INTO `club_gallery` (`id`, `club_id`, `image_url`, `created_at`) VALUES
(1, 3, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:52'),
(2, 3, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:52'),
(3, 3, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:52'),
(4, 4, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(5, 4, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(6, 4, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(7, 5, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(8, 5, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(9, 5, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(10, 6, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(11, 6, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(12, 6, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(13, 7, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(14, 7, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(15, 7, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(16, 8, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(17, 8, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(18, 8, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-19 05:02:53'),
(19, 5, 'https://images.unsplash.com/photo-1460662194856-732fc209c784?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:49'),
(20, 5, 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:49'),
(21, 5, 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:49'),
(22, 5, 'https://images.unsplash.com/photo-1541119638723-c51cbe2262aa?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:49'),
(23, 5, 'https://images.unsplash.com/photo-1515405299443-8b0bb4283895?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:50'),
(24, 5, 'https://images.unsplash.com/photo-1554188248-986adbb73be4?auto=format&fit=crop&q=80&w=800', '2026-02-24 03:58:50'),
(25, 3, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(26, 3, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(27, 3, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(28, 4, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(29, 4, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(30, 4, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(31, 5, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(32, 5, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(33, 5, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(34, 6, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(35, 6, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:10'),
(36, 6, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(37, 7, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(38, 7, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(39, 7, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(40, 8, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(41, 8, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(42, 8, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(43, 9, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(44, 9, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(45, 9, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(46, 10, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(47, 10, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(48, 10, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(49, 11, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(50, 11, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(51, 11, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(52, 12, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(53, 12, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(54, 12, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(57, 13, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(58, 14, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:11'),
(59, 14, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(60, 14, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(61, 15, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(62, 15, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(63, 15, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(64, 16, 'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(65, 16, 'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12'),
(66, 16, 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800', '2026-02-27 00:32:12');

-- --------------------------------------------------------

--
-- Table structure for table `club_memberships`
--

CREATE TABLE `club_memberships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `role` enum('member','admin','president','staff','coordinator') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_memberships`
--

INSERT INTO `club_memberships` (`id`, `user_id`, `club_id`, `role`, `joined_at`, `status`) VALUES
(3, 1, 5, 'president', '2026-02-16 17:58:59', 'approved'),
(4, 1, 4, 'member', '2026-02-16 17:59:31', 'approved'),
(5, 1, 3, 'member', '2026-02-16 18:24:02', 'approved'),
(6, 8, 3, 'member', '2026-02-19 04:20:33', 'approved'),
(7, 12, 3, 'member', '2026-02-19 04:20:33', 'approved'),
(8, 13, 4, 'member', '2026-02-19 04:20:34', 'approved'),
(9, 14, 4, 'member', '2026-02-19 04:20:34', 'approved'),
(12, 12, 6, 'member', '2026-02-19 04:20:34', 'approved'),
(13, 14, 6, 'member', '2026-02-19 04:20:34', 'approved'),
(14, 8, 3, 'member', '2026-02-19 04:43:57', 'approved'),
(15, 14, 3, 'member', '2026-02-19 04:43:57', 'approved'),
(16, 13, 4, 'member', '2026-02-19 04:43:57', 'approved'),
(18, 13, 6, 'member', '2026-02-19 04:43:58', 'approved'),
(19, 14, 6, 'member', '2026-02-19 04:43:58', 'approved'),
(20, 8, 7, 'member', '2026-02-19 04:43:58', 'approved'),
(21, 9, 7, 'member', '2026-02-19 04:43:58', 'approved'),
(22, 12, 7, 'member', '2026-02-19 04:43:58', 'approved'),
(23, 13, 8, 'member', '2026-02-19 04:43:58', 'approved'),
(24, 12, 3, 'member', '2026-02-19 05:02:53', 'approved'),
(25, 13, 3, 'member', '2026-02-19 05:02:53', 'approved'),
(26, 14, 3, 'member', '2026-02-19 05:02:53', 'approved'),
(27, 9, 4, 'member', '2026-02-19 05:02:53', 'approved'),
(28, 13, 4, 'member', '2026-02-19 05:02:53', 'approved'),
(29, 14, 4, 'member', '2026-02-19 05:02:53', 'approved'),
(31, 12, 5, 'coordinator', '2026-02-19 05:02:53', 'approved'),
(33, 14, 5, 'coordinator', '2026-02-19 05:02:53', 'approved'),
(34, 8, 6, 'member', '2026-02-19 05:02:53', 'approved'),
(35, 9, 6, 'member', '2026-02-19 05:02:53', 'approved'),
(36, 12, 6, 'member', '2026-02-19 05:02:54', 'approved'),
(37, 14, 6, 'member', '2026-02-19 05:02:54', 'approved'),
(38, 9, 7, 'member', '2026-02-19 05:02:54', 'approved'),
(39, 12, 7, 'member', '2026-02-19 05:02:54', 'approved'),
(40, 13, 7, 'member', '2026-02-19 05:02:54', 'approved'),
(41, 14, 7, 'member', '2026-02-19 05:02:54', 'approved'),
(42, 13, 8, 'member', '2026-02-19 05:02:54', 'approved'),
(43, 14, 8, 'member', '2026-02-19 05:02:54', 'approved'),
(44, 2, 5, 'staff', '2026-02-24 03:37:40', 'approved'),
(45, 9, 5, 'staff', '2026-02-24 03:37:40', 'approved'),
(46, 1, 7, 'member', '2026-02-24 04:16:39', 'pending'),
(47, 8, 3, 'member', '2026-02-27 00:32:12', 'approved'),
(48, 13, 3, 'member', '2026-02-27 00:32:12', 'approved'),
(49, 8, 4, 'member', '2026-02-27 00:32:12', 'approved'),
(50, 12, 4, 'member', '2026-02-27 00:32:12', 'approved'),
(51, 13, 4, 'member', '2026-02-27 00:32:12', 'approved'),
(52, 14, 4, 'member', '2026-02-27 00:32:12', 'approved'),
(53, 9, 5, 'member', '2026-02-27 00:32:12', 'approved'),
(54, 12, 5, 'member', '2026-02-27 00:32:12', 'approved'),
(56, 14, 5, 'member', '2026-02-27 00:32:12', 'approved'),
(57, 8, 6, 'member', '2026-02-27 00:32:12', 'approved'),
(58, 14, 6, 'member', '2026-02-27 00:32:12', 'approved'),
(59, 14, 7, 'member', '2026-02-27 00:32:12', 'approved'),
(60, 9, 8, 'member', '2026-02-27 00:32:12', 'approved'),
(61, 13, 8, 'member', '2026-02-27 00:32:12', 'approved'),
(62, 14, 8, 'member', '2026-02-27 00:32:12', 'approved'),
(63, 8, 9, 'member', '2026-02-27 00:32:12', 'approved'),
(64, 9, 9, 'member', '2026-02-27 00:32:13', 'approved'),
(65, 12, 9, 'member', '2026-02-27 00:32:13', 'approved'),
(66, 14, 9, 'member', '2026-02-27 00:32:13', 'approved'),
(67, 13, 10, 'member', '2026-02-27 00:32:13', 'approved'),
(68, 14, 10, 'member', '2026-02-27 00:32:13', 'approved'),
(69, 8, 11, 'member', '2026-02-27 00:32:13', 'approved'),
(70, 9, 11, 'member', '2026-02-27 00:32:13', 'approved'),
(71, 12, 11, 'member', '2026-02-27 00:32:13', 'approved'),
(72, 8, 12, 'member', '2026-02-27 00:32:13', 'approved'),
(73, 13, 12, 'member', '2026-02-27 00:32:13', 'approved'),
(74, 12, 13, 'member', '2026-02-27 00:32:13', 'approved'),
(75, 13, 13, 'member', '2026-02-27 00:32:13', 'approved'),
(76, 12, 14, 'member', '2026-02-27 00:32:13', 'approved'),
(77, 13, 14, 'member', '2026-02-27 00:32:13', 'approved'),
(78, 14, 14, 'member', '2026-02-27 00:32:13', 'approved'),
(79, 8, 15, 'member', '2026-02-27 00:32:13', 'approved'),
(80, 9, 15, 'member', '2026-02-27 00:32:13', 'approved'),
(81, 12, 15, 'member', '2026-02-27 00:32:13', 'approved'),
(82, 8, 16, 'member', '2026-02-27 00:32:13', 'approved'),
(83, 9, 16, 'member', '2026-02-27 00:32:13', 'approved'),
(84, 13, 16, 'member', '2026-02-27 00:32:13', 'approved'),
(87, 1, 13, 'member', '2026-03-08 06:51:42', 'approved'),
(88, 1, 15, 'president', '2026-03-11 17:24:21', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `club_id`, `title`, `description`, `event_date`, `location`, `created_by`, `created_at`) VALUES
(116, 3, 'Art Exhibition', 'Showcase your work.', '2023-11-20 11:00:00', 'Hall A', NULL, '2026-02-16 17:24:01'),
(117, 4, 'Music Concert', 'Live music.', '2023-12-05 18:00:00', 'Stadium', NULL, '2026-02-16 17:24:01'),
(118, 5, 'Canvas Carnival 2026', '', '2026-02-16 11:10:00', 'university of sahiwal ', 1, '2026-02-16 18:08:08'),
(119, 3, 'Annual Debate', 'The big debate event.', '2026-03-19 05:20:34', 'Auditorium', 6, '2026-02-19 04:20:34'),
(120, 4, 'Hackathon 2024', 'Code all night.', '2026-03-05 05:20:34', 'Lab 1', 6, '2026-02-19 04:20:34'),
(121, 5, 'Art Exhibition', 'Showcase your work.', '2026-02-12 05:20:34', 'Hall A', 6, '2026-02-19 04:20:34'),
(122, 6, 'Music Concert', 'Live music.', '2026-02-24 05:20:34', 'Stadium', 6, '2026-02-19 04:20:34'),
(123, 3, 'Annual Debate', 'The big debate event.', '2026-03-19 05:43:58', 'Auditorium', 6, '2026-02-19 04:43:58'),
(124, 4, 'Hackathon 2024', 'Code all night.', '2026-03-05 05:43:58', 'Lab 1', 6, '2026-02-19 04:43:58'),
(125, 5, 'Art Exhibition', 'Showcase your work.', '2026-02-12 05:43:58', 'Hall A', 6, '2026-02-19 04:43:58'),
(126, 6, 'Music Concert', 'Live music.', '2026-02-24 05:43:58', 'Stadium', 6, '2026-02-19 04:43:58'),
(127, 3, 'Annual Debate', 'The big debate event.', '2026-03-19 06:02:54', 'Auditorium', 6, '2026-02-19 05:02:54'),
(128, 4, 'Hackathon 2024', 'Code all night.', '2026-03-05 06:02:54', 'Lab 1', 6, '2026-02-19 05:02:54'),
(129, 3, 'Art Exhibition', 'Showcase your work.', '2026-02-12 06:02:54', 'Hall A', 6, '2026-02-19 05:02:54'),
(130, 6, 'Music Concert', 'Live music.', '2026-02-24 06:02:54', 'Stadium', 6, '2026-02-19 05:02:54'),
(131, 3, 'Annual Debate', 'The big debate event.', '2026-03-27 01:32:13', 'Auditorium', 6, '2026-02-27 00:32:13'),
(132, 4, 'Hackathon 2024', 'Code all night.', '2026-03-13 01:32:13', 'Lab 1', 6, '2026-02-27 00:32:13'),
(133, 5, 'Art Exhibition', 'Showcase your work.', '2026-02-20 01:32:13', 'Hall A', 6, '2026-02-27 00:32:13'),
(134, 6, 'Music Concert', 'Live music.', '2026-03-04 01:32:13', 'Stadium', 6, '2026-02-27 00:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `event_attendance`
--

CREATE TABLE `event_attendance` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `attended_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_attendance`
--

INSERT INTO `event_attendance` (`id`, `event_id`, `user_id`, `marked_by`, `attended_at`) VALUES
(1, 116, 2, 1, '2026-03-04 01:03:12'),
(2, 129, 2, 1, '2026-03-04 01:06:34'),
(4, 119, 16, 1, '2026-03-11 18:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `event_enrollments`
--

CREATE TABLE `event_enrollments` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_phone` varchar(50) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_enrollments`
--

INSERT INTO `event_enrollments` (`id`, `event_id`, `user_id`, `student_name`, `student_email`, `student_phone`, `message`, `status`, `created_at`) VALUES
(1, 119, 1, 'tooba', 'tooba@gmail.com', '03138697273', 'to enhance  my skill of debating', 'approved', '2026-03-11 18:01:17'),
(2, 119, 16, 'irsha', 'irsha@gmail.com', '03138697273', 'jniuhoi', 'approved', '2026-03-11 18:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `event_rsvps`
--

CREATE TABLE `event_rsvps` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('going','maybe','not_going') DEFAULT 'going',
  `rsvp_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_rsvps`
--

INSERT INTO `event_rsvps` (`id`, `event_id`, `user_id`, `status`, `rsvp_at`) VALUES
(1, 116, 2, 'going', '2026-03-04 01:03:11'),
(2, 129, 2, 'going', '2026-03-04 01:06:34'),
(3, 132, 1, '', '2026-03-11 17:42:32'),
(4, 119, 1, '', '2026-03-11 17:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `finance_records`
--

CREATE TABLE `finance_records` (
  `id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `description` varchar(255) NOT NULL,
  `record_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finance_records`
--

INSERT INTO `finance_records` (`id`, `club_id`, `amount`, `type`, `description`, `record_date`, `status`, `created_by`) VALUES
(1, 4, 1000.00, 'income', 'Sponsorship from TechCorp', '2026-02-18 20:20:34', 'approved', 5),
(2, 5, 500.00, 'expense', 'Catering for Annual Debate', '2026-02-18 20:20:34', 'pending', 5),
(3, 3, 200.00, 'expense', 'Printing flyers', '2026-02-18 20:20:34', 'approved', 5),
(4, 6, 150.00, 'income', 'Membership fees', '2026-02-18 20:20:34', 'approved', 5),
(5, 8, 1000.00, 'income', 'Sponsorship from TechCorp', '2026-02-18 20:43:58', 'approved', 5),
(6, 3, 500.00, 'expense', 'Catering for Annual Debate', '2026-02-18 20:43:58', 'pending', 5),
(7, 4, 200.00, 'expense', 'Printing flyers', '2026-02-18 20:43:58', 'approved', 5),
(8, 7, 150.00, 'income', 'Membership fees', '2026-02-18 20:43:58', 'approved', 5),
(9, 6, 1000.00, 'income', 'Sponsorship from TechCorp', '2026-02-18 21:02:54', 'approved', 5),
(10, 4, 500.00, 'expense', 'Catering for Annual Debate', '2026-02-18 21:02:54', 'pending', 5),
(11, 8, 200.00, 'expense', 'Printing flyers', '2026-02-18 21:02:54', 'approved', 5),
(12, 3, 150.00, 'income', 'Membership fees', '2026-02-18 21:02:54', 'approved', 5),
(13, 5, 1000.00, 'income', 'Sponsorship from TechCorp', '2026-02-26 16:32:13', 'approved', 5),
(14, 13, 500.00, 'expense', 'Catering for Annual Debate', '2026-02-26 16:32:13', 'pending', 5),
(15, 14, 200.00, 'expense', 'Printing flyers', '2026-02-26 16:32:13', 'approved', 5),
(16, 7, 150.00, 'income', 'Membership fees', '2026-02-26 16:32:14', 'approved', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `announcement_id`, `is_read`, `created_at`) VALUES
(1, 1, 1, 1, '2026-03-04 00:56:44'),
(2, 2, 1, 0, '2026-03-04 00:56:44'),
(3, 3, 1, 0, '2026-03-04 00:56:44'),
(4, 4, 1, 0, '2026-03-04 00:56:44'),
(5, 5, 1, 0, '2026-03-04 00:56:44'),
(6, 6, 1, 0, '2026-03-04 00:56:44'),
(7, 7, 1, 0, '2026-03-04 00:56:44'),
(8, 8, 1, 0, '2026-03-04 00:56:44'),
(9, 9, 1, 0, '2026-03-04 00:56:44'),
(10, 10, 1, 0, '2026-03-04 00:56:44'),
(11, 11, 1, 0, '2026-03-04 00:56:44'),
(12, 12, 1, 0, '2026-03-04 00:56:44'),
(13, 13, 1, 0, '2026-03-04 00:56:44'),
(14, 14, 1, 0, '2026-03-04 00:56:44');

-- --------------------------------------------------------

--
-- Table structure for table `role_access`
--

CREATE TABLE `role_access` (
  `role_key` varchar(50) NOT NULL,
  `page_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_access`
--

INSERT INTO `role_access` (`role_key`, `page_id`) VALUES
('event_manager', 1),
('event_manager', 8),
('event_manager', 9),
('event_manager', 10),
('event_manager', 18),
('finance_manager', 1),
('finance_manager', 11),
('finance_manager', 12),
('finance_manager', 18),
('member', 1),
('member', 5),
('member', 6),
('member', 8),
('member', 9),
('member', 18),
('society_admin', 1),
('society_admin', 5),
('society_admin', 6),
('society_admin', 7),
('society_admin', 8),
('society_admin', 9),
('society_admin', 10),
('society_admin', 11),
('society_admin', 12),
('society_admin', 18),
('student', 1),
('student', 5),
('student', 6),
('student', 8),
('student', 9),
('student', 18),
('super_admin', 1),
('super_admin', 2),
('super_admin', 3),
('super_admin', 4),
('super_admin', 5),
('super_admin', 6),
('super_admin', 8),
('super_admin', 9),
('super_admin', 10),
('super_admin', 11),
('super_admin', 12),
('super_admin', 13),
('super_admin', 14),
('super_admin', 15),
('super_admin', 16),
('super_admin', 17),
('super_admin', 18);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('footer_text', '© 2026 Universal Systems. All rights reserved.'),
('site_name', 'University Society Hub'),
('system_logo', 'https://cdn-icons-png.flaticon.com/512/906/906343.png'),
('system_name', 'University Society & Club Management System\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `sys_pages`
--

CREATE TABLE `sys_pages` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `page_name` varchar(100) NOT NULL,
  `page_url` varchar(255) DEFAULT '#',
  `icon_class` varchar(50) DEFAULT 'bi bi-circle',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_pages`
--

INSERT INTO `sys_pages` (`id`, `parent_id`, `page_name`, `page_url`, `icon_class`, `sort_order`) VALUES
(1, 0, 'Dashboard', 'index.php', 'bi bi-speedometer', 10),
(2, 0, 'User Management', '#', 'bi bi-people', 20),
(3, 2, 'All Users', 'users/index.php', 'bi bi-circle', 1),
(4, 2, 'Add User', 'users/create.php', 'bi bi-circle', 2),
(5, 0, 'Societies', '#', 'bi bi-collection', 20),
(6, 5, 'All Societies', 'clubs/index.php', 'bi bi-circle', 1),
(7, 5, 'My Society', 'clubs/mysociety.php', 'bi bi-circle', 2),
(8, 0, 'Events', '#', 'bi bi-calendar-event', 30),
(9, 8, 'All Events', 'events/index.php', 'bi bi-circle', 1),
(10, 8, 'Manage Events', 'events/manage.php', 'bi bi-circle', 2),
(11, 0, 'Finance', '#', 'bi bi-cash-coin', 50),
(12, 11, 'Budget Overview', 'finance/overview.php', 'bi bi-circle', 1),
(13, 0, 'System Settings', 'settings.php', 'bi bi-gear', 99),
(14, 0, 'System Admin', '#', 'bi bi-shield-lock', 90),
(15, 14, 'Manage Users', 'dashboards/super_admin/manage_users.php', 'bi bi-circle', 1),
(16, 14, 'Manage Roles', 'dashboards/super_admin/manage_roles.php', 'bi bi-circle', 2),
(17, 14, 'Manage Pages', 'dashboards/super_admin/manage_pages.php', 'bi bi-circle', 3),
(18, 0, 'Announcements', 'announcements/index.php', 'bi bi-megaphone', 40);

-- --------------------------------------------------------

--
-- Table structure for table `sys_roles`
--

CREATE TABLE `sys_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_key` varchar(50) NOT NULL,
  `is_system_role` tinyint(1) DEFAULT 0 COMMENT '1=Cannot Delete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_roles`
--

INSERT INTO `sys_roles` (`id`, `role_name`, `role_key`, `is_system_role`) VALUES
(1, 'Super Admin', 'super_admin', 1),
(2, 'Administrator', 'admin', 0),
(3, 'Student', 'student', 0),
(4, 'Suspended', 'suspended', 1),
(8, 'Society Head', 'society_admin', 0),
(9, 'Event Manager', 'event_manager', 0),
(10, 'Finance Manager', 'finance_manager', 0),
(11, 'Member/Student', 'member', 0),
(12, 'Guest', 'guest', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `identity_no` varchar(50) DEFAULT NULL,
  `registration_no` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `identity_no`, `registration_no`, `is_active`) VALUES
(1, 'laiba', 'admin@sys.com', '$2y$10$8kg.O32DgvLObk.njuzt4uM6h.e2KKqOVe9m4ZqMf78V.42Ib94G2', 'super_admin', '12345-1234567-1', 'ADM-001', 1),
(2, 'student', 'student@gmail.com', '1234567', '', NULL, NULL, 1),
(3, 'Test User', 'testuser_1770817127@example.com', '$2y$10$/GX/6VTkZ2njGyDIo/kUA.zuH7iG9YA11BrOrt3BTsKJPbosDSrUq', 'student', NULL, NULL, 1),
(4, 'Super Admin', 'admin@universal.com', '$2y$10$T9bmQh6QYjepKI8Ya/I82OPEEB6vQ4tuC2Vrs3FabxZRILy5NTyW6', 'super_admin', NULL, NULL, 1),
(5, 'Society Head', 'head@society.com', '$2y$10$Ipgng9TtHHsoG/.reYwo9OcxV6jRprdufnnDhCiJHvswKzjwIB5mC', 'society_admin', NULL, NULL, 1),
(6, 'Event Manager', 'event@manager.com', '$2y$10$TIAQ1vwBxIHpV81UCTwoMOGb2JapMcDefTSpoeTMt2CZOdK.iyP9S', 'event_manager', NULL, NULL, 1),
(7, 'Finance Manager', 'finance@manager.com', '$2y$10$8x4BtSivjjMHBDb/.Dq1X.f/DXMQuthIZzxKEx0A1rAm4fIc99B8q', 'finance_manager', NULL, NULL, 1),
(8, 'John Doe', 'john@student.com', '$2y$10$akPRT3B3mdXYqnNCs4xEDeH7nWgrRyAqkShS6XBoLcGI7tVsbjJ1.', 'member', NULL, NULL, 1),
(9, 'Jane Smith', 'jane@student.com', '$2y$10$QHInsmU0U/Hnia93GmuJgOeS2BFPCwCjNnaY2uY7AYVeHxLAKmyCG', 'member', NULL, NULL, 1),
(10, 'Guest User', 'guest@user.com', '$2y$10$8mbqoCUsIiOy8gFwWbC32.grH5y6TgGdUg0OgM1MRBa3aIKq731uC', 'guest', NULL, NULL, 1),
(11, 'laiba liaqat', 'laiba@gmail.com', '$2y$10$H6c/P698qjk9jlj4Fk31Zu8teLgMQ.R/9ro.sXeUyiqEGk6ctXYEm', 'student', '', '', 1),
(12, 'Alice Johnson', 'alice@student.com', '$2y$10$Vda09qjfgujbSWrJQ9Qdr.lV9cejSdRDCLWuW86.u5pgtInFhcWw.', 'member', NULL, NULL, 1),
(13, 'Bob Brown', 'bob@student.com', '$2y$10$PPhq5YYMEeu1oekxgZ.NjOCH8nnUfanH2kFwlO1RLd6DiHNVCUHfO', 'member', NULL, NULL, 1),
(14, 'Charlie Davis', 'charlie@student.com', '$2y$10$mAm8hGnuC4TlLNH8pvMIgec84QIDHDH1tNo.9ESoSjLlch0Y.S/vO', 'member', NULL, NULL, 1),
(15, 'Minahil', 'minahil@gmail.com', '$2y$10$Yfs7A09akxt7Z3Og/llkmeXP2ee4DKlF8xSEZ/qFKizRY7JXIfxOq', 'student', NULL, NULL, 1),
(16, 'irsha', 'irsha@gmail.com', '$2y$10$FO4FDGm6c/AKdCINAxwi1uNfERRpPB/EaFNNtrYfIz/zrKU7r//Sy', 'student', NULL, NULL, 1),
(17, 'sarah', 'sarah@gmail.com', '$2y$10$3c4XEGM1WgGNdSWptd87bunaY7lwADW7xS4d3jIv/UIUrqcon0Rvu', 'student', '3530308763456', 'BBIS -22-01', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `society_id` (`society_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_hash` (`certificate_hash`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `club_gallery`
--
ALTER TABLE `club_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `club_memberships`
--
ALTER TABLE `club_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `event_attendance`
--
ALTER TABLE `event_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `marked_by` (`marked_by`);

--
-- Indexes for table `event_enrollments`
--
ALTER TABLE `event_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_rsvps`
--
ALTER TABLE `event_rsvps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `finance_records`
--
ALTER TABLE `finance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `role_access`
--
ALTER TABLE `role_access`
  ADD PRIMARY KEY (`role_key`,`page_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `sys_pages`
--
ALTER TABLE `sys_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_roles`
--
ALTER TABLE `sys_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_key` (`role_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD UNIQUE KEY `idx_identity` (`identity_no`),
  ADD UNIQUE KEY `idx_reg_no` (`registration_no`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `club_gallery`
--
ALTER TABLE `club_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `club_memberships`
--
ALTER TABLE `club_memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `event_attendance`
--
ALTER TABLE `event_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_enrollments`
--
ALTER TABLE `event_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_rsvps`
--
ALTER TABLE `event_rsvps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `finance_records`
--
ALTER TABLE `finance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sys_pages`
--
ALTER TABLE `sys_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sys_roles`
--
ALTER TABLE `sys_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`society_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `club_gallery`
--
ALTER TABLE `club_gallery`
  ADD CONSTRAINT `club_gallery_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `club_memberships`
--
ALTER TABLE `club_memberships`
  ADD CONSTRAINT `club_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_memberships_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_attendance`
--
ALTER TABLE `event_attendance`
  ADD CONSTRAINT `event_attendance_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_attendance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_enrollments`
--
ALTER TABLE `event_enrollments`
  ADD CONSTRAINT `event_enrollments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_enrollments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_rsvps`
--
ALTER TABLE `event_rsvps`
  ADD CONSTRAINT `event_rsvps_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_rsvps_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `finance_records`
--
ALTER TABLE `finance_records`
  ADD CONSTRAINT `finance_records_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `finance_records_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
