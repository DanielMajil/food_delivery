-- Create roles table
CREATE TABLE `roles` (
  `role_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert roles
INSERT INTO `roles` (`name`, `created_at`) VALUES
('employee', current_timestamp()),
('customer', current_timestamp());

-- Create addresses table
CREATE TABLE `addresses` (
  `address_id` INT AUTO_INCREMENT PRIMARY KEY,
  `address` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert addresses
INSERT INTO `addresses` (`address`, `created_at`) VALUES 
('123 Main St', current_timestamp()),
('456 Elm St', current_timestamp()),
('789 Oak St', current_timestamp());

-- Create users table
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL,
  `firstname` VARCHAR(255) NOT NULL,
  `lastname` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(15),
  `age` INT,
  `password` VARCHAR(255) NOT NULL,
  `member` TINYINT NOT NULL,
  `status` TINYINT NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert customers
INSERT INTO `users` (`role_id`, `firstname`, `lastname`, `username`, `email`, `address`, `phone`, `age`, `password`, `member`, `status`, `created_at`) VALUES 
(2, 'John', 'Doe', 'johndoe', 'johndoe@example.com', '123 Main St', '555-123-4567', 30, 'password123', 1, 1, current_timestamp()),
(2, 'Alice', 'Smith', 'alicesmith', 'alicesmith@example.com', '456 Elm St', '555-987-6543', 25, 'alicepassword', 1, 1, current_timestamp()),
(2, 'Bob', 'Johnson', 'bobjohnson', 'bobjohnson@example.com', '789 Oak St', '555-789-1234', 28, 'bobspassword', 1, 1, current_timestamp());

-- Create restaurants table
CREATE TABLE `restaurants` (
  `restaurant_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `address` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(15) NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert restaurants
INSERT INTO `restaurants` (`name`, `description`, `address`, `phone_number`, `created_at`) VALUES
('Tasty Grill', 'Delicious grilled dishes', '321 Oak St', '555-555-1234', current_timestamp()),
('Pizza Palace', 'Home of the best pizzas', '567 Maple St', '555-777-8888', current_timestamp());

-- Create menu items table
CREATE TABLE `menu_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY,
  `restaurant_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert menu items for restaurants
INSERT INTO `menu_items` (`restaurant_id`, `name`, `description`, `price`, `created_at`) VALUES
(1, 'Steak', 'Juicy grilled steak', 15.99, current_timestamp()),
(1, 'Burger', 'Classic beef burger', 8.99, current_timestamp()),
(1, 'Salad', 'Fresh garden salad', 6.99, current_timestamp()),
(2, 'Margherita Pizza', 'Classic pizza with tomato and mozzarella', 10.99, current_timestamp()),
(2, 'Pepperoni Pizza', 'Pizza with pepperoni topping', 12.99, current_timestamp()),
(2, 'Vegetarian Pizza', 'Pizza with assorted veggies', 11.99, current_timestamp());

-- Create orders table
CREATE TABLE `orders` (
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL
);

-- Insert orders
INSERT INTO `orders` (`customer_id`, `total_amount`, `created_at`) 
VALUES 
(1, 36.97, current_timestamp()), -- John Doe's order
(2, 23.98, current_timestamp()), -- Alice Smith's order
(3, 46.97, current_timestamp()); -- Bob Johnson's order

-- Create order items table (what each customer ordered)
CREATE TABLE `order_items` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `menu_item_id` INT NOT NULL,
  `quantity` INT NOT NULL
);

-- Insert order items
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `quantity`) 
VALUES 
(1, 1, 2), -- John Doe ordered 2 Steaks
(1, 3, 1), -- John Doe ordered 1 Salad
(2, 4, 1), -- Alice Smith ordered 1 Margherita Pizza
(3, 2, 3), -- Bob Johnson ordered 3 Burgers
(3, 5, 2); -- Bob Johnson ordered 2 Pepperoni Pizzas
