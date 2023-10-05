-- Create the database for the food delivery service
CREATE DATABASE `food_delivery`;

-- Use the newly created database
USE `food_delivery`;

-- Create a table for customers
CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  PRIMARY KEY (`customer_id`)
);

-- Create a table for food items
CREATE TABLE `food_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`item_id`)
);

-- Create a table for orders
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `order_name` varchar(255) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `order_total` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`)
);

-- Create a table for order items
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `food_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`item_id`)
);

-- Sample data for customers
INSERT INTO `customers` (`name`, `email`, `address`) VALUES
('John Doe', 'john@example.com', '123 Main St'),
('Alice Smith', 'alice@example.com', '456 Elm St');

-- Sample data for food items
INSERT INTO `food_items` (`name`, `price`) VALUES
('Pizza', 12.99),
('Burger', 8.99),
('Pasta', 10.49);

-- Sample data for orders
INSERT INTO `orders` (`customer_id`, `order_name`, `order_status`, `order_total`) VALUES
(1, 'Order 1', 'Pending', 21.48),
(2, 'Order 2', 'Delivered', 8.99);

-- Sample data for order items
INSERT INTO `order_items` (`order_id`, `food_item_id`, `quantity`) VALUES
(1, 1, 1),
(1, 2, 2),
(2, 3, 1);
