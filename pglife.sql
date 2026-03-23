-- =============================================
-- PG LIFE - Database Schema
-- =============================================
-- Description: SQL schema for PG (Paying Guest) accommodation finder website
-- Author: Generated for PG Life Project
-- Date: February 2026
-- =============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS pglife;
USE pglife;

-- Set character set and collation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- Table: cities
-- Description: Stores information about cities where PGs are available
-- =============================================
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample cities
INSERT INTO `cities` (`id`, `name`) VALUES
(1, 'Delhi'),
(2, 'Mumbai'),
(3, 'Bengaluru'),
(4, 'Hyderabad'),
(5, 'Pune'),
(6, 'Chennai'),
(7, 'Kolkata');

-- =============================================
-- Table: users
-- Description: Stores user information for authentication
-- =============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `college_name` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample users (password: 'password123' - remember to hash in production)
INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `phone`, `college_name`, `gender`) VALUES
(1, 'john.doe@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'John Doe', '9876543210', 'Delhi University', 'male'),
(2, 'jane.smith@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Jane Smith', '9876543211', 'Mumbai University', 'female'),
(3, 'rahul.kumar@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Rahul Kumar', '9876543212', 'IIT Bangalore', 'male');

-- =============================================
-- Table: properties
-- Description: Stores PG property details
-- =============================================
DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `description` text,
  `gender` enum('male','female','unisex') NOT NULL DEFAULT 'unisex',
  `rent` decimal(10,2) NOT NULL,
  `rating_clean` decimal(2,1) DEFAULT 0.0,
  `rating_food` decimal(2,1) DEFAULT 0.0,
  `rating_safety` decimal(2,1) DEFAULT 0.0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`),
  CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample properties
INSERT INTO `properties` (`id`, `city_id`, `name`, `address`, `description`, `gender`, `rent`, `rating_clean`, `rating_food`, `rating_safety`) VALUES
(1, 1, 'Sunshine PG', 'Sector 15, Rohini, Delhi', 'A comfortable PG with all modern amenities. Close to metro station and market. Perfect for students and working professionals.', 'unisex', 8000.00, 4.2, 4.0, 4.5),
(2, 1, 'Green Valley Boys Hostel', 'Kamla Nagar, Delhi', 'Boys only accommodation with homely food and clean rooms. Near Delhi University North Campus.', 'male', 7500.00, 4.5, 4.3, 4.7),
(3, 2, 'Shree Krishna PG', 'Andheri West, Mumbai', 'Premium PG accommodation in the heart of Mumbai. Walking distance to railway station.', 'male', 12000.00, 4.0, 3.8, 4.2),
(4, 2, 'Sai Residency', 'Powai, Mumbai', 'Spacious rooms with attached washrooms. 24x7 security and Wi-Fi facility.', 'female', 11000.00, 4.3, 4.1, 4.6),
(5, 3, 'HSR Layout PG', 'HSR Layout, Bengaluru', 'Modern PG with all amenities. Very close to IT companies and metro.', 'unisex', 9500.00, 4.4, 4.2, 4.5),
(6, 3, 'Koramangala Heights', 'Koramangala, Bengaluru', 'Premium accommodation with gym and common lounge area.', 'male', 10500.00, 4.6, 4.4, 4.8),
(7, 4, 'Cyber Towers PG', 'Madhapur, Hyderabad', 'Located in IT hub, ideal for working professionals. AC rooms available.', 'unisex', 8500.00, 4.1, 3.9, 4.3);

-- =============================================
-- Table: amenities
-- Description: Stores different amenities available
-- =============================================
DROP TABLE IF EXISTS `amenities`;
CREATE TABLE `amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('Building','Common Area','Bedroom','Washroom') NOT NULL,
  `icon` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert amenities
INSERT INTO `amenities` (`id`, `name`, `type`, `icon`) VALUES
(1, 'Power Backup', 'Building', 'power-backup'),
(2, 'Lift', 'Building', 'lift'),
(3, 'Fire Extinguisher', 'Building', 'fire-extinguisher'),
(4, 'Security Guard', 'Building', 'security'),
(5, 'Wi-Fi', 'Common Area', 'wifi'),
(6, 'TV', 'Common Area', 'tv'),
(7, 'Refrigerator', 'Common Area', 'fridge'),
(8, 'Water Purifier', 'Common Area', 'water-purifier'),
(9, 'Dining', 'Common Area', 'dining'),
(10, 'AC', 'Bedroom', 'ac'),
(11, 'Bed', 'Bedroom', 'bed'),
(12, 'Wardrobe', 'Bedroom', 'wardrobe'),
(13, 'Study Table', 'Bedroom', 'table'),
(14, 'Geyser', 'Washroom', 'geyser'),
(15, 'Washing Machine', 'Washroom', 'washing-machine');

-- =============================================
-- Table: properties_amenities
-- Description: Junction table linking properties with their amenities
-- =============================================
DROP TABLE IF EXISTS `properties_amenities`;
CREATE TABLE `properties_amenities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `amenity_id` (`amenity_id`),
  CONSTRAINT `properties_amenities_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `properties_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert property-amenity relationships
INSERT INTO `properties_amenities` (`property_id`, `amenity_id`) VALUES
-- Property 1 amenities
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 8), (1, 11), (1, 12), (1, 13), (1, 14),
-- Property 2 amenities
(2, 1), (2, 3), (2, 4), (2, 5), (2, 7), (2, 8), (2, 11), (2, 12), (2, 13), (2, 14), (2, 15),
-- Property 3 amenities
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 10), (3, 11), (3, 12), (3, 14),
-- Property 4 amenities
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 7), (4, 8), (4, 10), (4, 11), (4, 12), (4, 13), (4, 14),
-- Property 5 amenities
(5, 1), (5, 2), (5, 4), (5, 5), (5, 6), (5, 8), (5, 9), (5, 11), (5, 12), (5, 13), (5, 14),
-- Property 6 amenities
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 8), (6, 10), (6, 11), (6, 12), (6, 13), (6, 14), (6, 15),
-- Property 7 amenities
(7, 1), (7, 2), (7, 4), (7, 5), (7, 7), (7, 8), (7, 10), (7, 11), (7, 12), (7, 13), (7, 14);

-- =============================================
-- Table: interested_users_properties
-- Description: Junction table tracking user interest in properties
-- =============================================
DROP TABLE IF EXISTS `interested_users_properties`;
CREATE TABLE `interested_users_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_property_unique` (`user_id`, `property_id`),
  KEY `user_id` (`user_id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `interested_users_properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interested_users_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample interested users
INSERT INTO `interested_users_properties` (`user_id`, `property_id`) VALUES
(1, 1),
(1, 5),
(2, 4),
(2, 5),
(3, 2),
(3, 6);

-- =============================================
-- Table: testimonials
-- Description: Stores user reviews and testimonials for properties
-- =============================================
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample testimonials
INSERT INTO `testimonials` (`property_id`, `user_name`, `content`) VALUES
(1, 'Amit Sharma', 'Great place to stay! The owner is very cooperative and the food is homely. Highly recommended for students.'),
(1, 'Priya Singh', 'Clean rooms and good facilities. The location is perfect with easy access to metro.'),
(2, 'Rahul Verma', 'Excellent hostel for boys. The security is top-notch and the environment is very friendly.'),
(2, 'Vikram Patel', 'Been staying here for 6 months. Very satisfied with the cleanliness and food quality.'),
(3, 'Arjun Mehta', 'Good PG in Andheri. A bit pricey but worth it for the location and amenities.'),
(4, 'Sneha Reddy', 'Safe and secure place for girls. The staff is very helpful and rooms are spacious.'),
(4, 'Anjali Desai', 'Amazing PG! The owner treats us like family. Food is delicious and homely.'),
(5, 'Karthik Krishnan', 'Perfect location for IT professionals. Very close to office and has all necessary facilities.'),
(5, 'Divya Nair', 'Good value for money. Wi-Fi speed is excellent and rooms are well-maintained.'),
(6, 'Rohan Gupta', 'Premium PG with gym facility. Loved the common area where we can relax and socialize.'),
(7, 'Suresh Babu', 'Great PG in Hyderabad. AC rooms are a blessing in summer. Highly recommended!');

-- =============================================
-- Additional Indexes for Performance
-- =============================================
CREATE INDEX idx_properties_city_gender ON properties(city_id, gender);
CREATE INDEX idx_properties_rent ON properties(rent);
CREATE INDEX idx_users_email ON users(email);

-- =============================================
-- Enable Foreign Key Checks
-- =============================================
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- Views (Optional - for easier queries)
-- =============================================

-- View: Property details with city name
CREATE OR REPLACE VIEW vw_properties_with_city AS
SELECT 
    p.*,
    c.name AS city_name,
    ROUND((p.rating_clean + p.rating_food + p.rating_safety) / 3, 1) AS avg_rating
FROM properties p
INNER JOIN cities c ON p.city_id = c.id;

-- View: Property interest count
CREATE OR REPLACE VIEW vw_property_interest_count AS
SELECT 
    p.id AS property_id,
    p.name AS property_name,
    COUNT(iup.user_id) AS interest_count
FROM properties p
LEFT JOIN interested_users_properties iup ON p.id = iup.property_id
GROUP BY p.id, p.name;

-- =============================================
-- Sample Useful Queries (Comments for reference)
-- =============================================

-- Get all properties in a specific city with average rating
-- SELECT * FROM vw_properties_with_city WHERE city_name = 'Delhi';

-- Get user's interested properties
-- SELECT p.* FROM properties p
-- INNER JOIN interested_users_properties iup ON p.id = iup.property_id
-- WHERE iup.user_id = 1;

-- Get properties with specific amenities
-- SELECT DISTINCT p.* FROM properties p
-- INNER JOIN properties_amenities pa ON p.id = pa.property_id
-- INNER JOIN amenities a ON pa.amenity_id = a.id
-- WHERE a.name IN ('Wi-Fi', 'AC');

-- Get top rated properties by city
-- SELECT * FROM vw_properties_with_city
-- WHERE city_name = 'Bengaluru'
-- ORDER BY avg_rating DESC
-- LIMIT 5;

-- =============================================
-- Database Schema Complete
-- =============================================
-- Total Tables: 7
-- Total Views: 2
-- Ready for use with PG Life application
-- =============================================


CREATE TABLE \ookings\ (
    \id\ int(11) NOT NULL AUTO_INCREMENT,
    \user_id\ int(11) NOT NULL,
    \property_id\ int(11) NOT NULL,
    \	otal_rent\ float NOT NULL,
    \ooking_date\ timestamp DEFAULT CURRENT_TIMESTAMP,
    \status\ varchar(50) DEFAULT 'Confirmed',
    PRIMARY KEY (\id\),
    FOREIGN KEY (\user_id\) REFERENCES \users\(\id\),
    FOREIGN KEY (\property_id\) REFERENCES \properties\(\id\)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
