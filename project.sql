drop database if exists inventory;
create database inventory; 
Use inventory; 
 -- Table: products 
 
CREATE TABLE products ( 
  `product_id` int(11) NOT NULL, 
  `product_name` varchar(255) NOT NULL, 
  `description` text NOT NULL, 
  `price` decimal(10,2) NOT NULL, 
  `quantity_in_stock` int(11) NOT NULL, 
  `category` varchar(100) NOT NULL, 
  `sup_id` int(11) NOT NULL, 
  `product_img` varchar(255) NOT NULL 
)  ;
 -- Table: sales 
 
CREATE TABLE sales ( 
  `sales_id` int(11) NOT NULL, 
  `product_id` int(11) NOT NULL, 
  `user_id` int(11) NOT NULL, 
  `quantity_sold` int(11) NOT NULL, 
  `total_price` decimal(10,2) NOT NULL, 
  `date_of_sale` datetime NOT NULL 
) ;
 -- Table: suppliers 
 
 
CREATE TABLE suppliers ( 
  `sup_id` int(11) NOT NULL, 
  `sup_name` varchar(255) NOT NULL, 
  `contact_person` varchar(255) NOT NULL, 
  `sup_address` text NOT NULL, 
  `sup_email` varchar(50) NOT NULL, 
  `sup_phoneNo` varchar(15) NOT NULL 
) ;
 -- Table: user 
 
CREATE TABLE  user ( 
  `id` int(11) NOT NULL, 
  `username` varchar(255) NOT NULL, 
  `email` varchar(255) NOT NULL, 
  `password` varchar(255) NOT NULL, 
  `role` varchar(50) NOT NULL 
) ;


-- Inventory Management System Sample Data

-- Suppliers Data
INSERT INTO suppliers (sup_id, sup_name, contact_person, sup_address, sup_email, sup_phoneNo) VALUES
(1, 'Tech Solutions Ltd.', 'John Smith', '123 Tech Park, Karachi', 'john@techsolutions.com', '0300-1234567'),
(2, 'Electronic World', 'Sarah Khan', '456 Electronics Market, Lahore', 'sarah@eworld.com', '0312-7654321'),
(3, 'Global Suppliers Inc.', 'Ali Ahmed', '789 Business Plaza, Islamabad', 'ali@globalsuppliers.com', '0333-9876543'),
(4, 'Quality Goods Co.', 'Fatima Raza', '321 Industrial Area, Faisalabad', 'fatima@qualitygoods.com', '0345-1122334'),
(5, 'Prime Distributors', 'Usman Malik', '654 Trade Center, Rawalpindi', 'usman@primedist.com', '0301-4455667');

-- Products Data  
INSERT INTO products (product_id, product_name, description, price, quantity_in_stock, category, sup_id, product_img) VALUES
(1, 'Dell Laptop', 'Dell Inspiron 15, 8GB RAM, 512GB SSD', 85000.00, 15, 'Electronics', 1, 'dell-laptop.jpg'),
(2, 'Wireless Mouse', 'Logitech Wireless Mouse, 2.4GHz', 2500.00, 50, 'Accessories', 2, 'wireless-mouse.jpg'),
(3, 'Mechanical Keyboard', 'RGB Mechanical Keyboard, Blue Switches', 6500.00, 30, 'Accessories', 2, 'mechanical-keyboard.jpg'),
(4, '27-inch Monitor', 'Samsung 27-inch LED Monitor, Full HD', 35000.00, 20, 'Electronics', 1, '27-monitor.jpg'),
(5, 'Web Camera', '1080p HD Web Camera with Microphone', 4500.00, 40, 'Accessories', 3, 'web-camera.jpg'),
(6, 'Laptop Bag', 'Waterproof Laptop Bag, 15.6 inch', 2800.00, 25, 'Accessories', 4, 'laptop-bag.jpg'),
(7, 'USB-C Hub', '7-in-1 USB-C Hub with HDMI', 5500.00, 35, 'Accessories', 3, 'usb-c-hub.jpg'),
(8, 'External Hard Drive', '1TB Seagate External Hard Drive', 12000.00, 18, 'Storage', 5, 'external-hdd.jpg');

-- Users Data
INSERT INTO user (id, username, email, password, role) VALUES
(1, 'seerat', 'admin@inventory.com', 'admin123', 'admin'),
(2, 'laiba', 'manager@inventory.com', 'manager123', 'admin'),
(3, 'zabi', 'staff1@inventory.com', 'staff123', 'staff'),
(4, 'sidra', 'staff2@inventory.com', 'staff123', 'staff'),
(5, 'maham', 'viewer@inventory.com', 'viewer123', 'staff');

-- Sales Data
INSERT INTO sales (sales_id, product_id, user_id, quantity_sold, total_price, date_of_sale) VALUES
(1, 1, 3, 2, 170000.00, '2024-01-15 10:30:00'),
(2, 2, 4, 5, 12500.00, '2024-01-16 14:20:00'),
(3, 3, 3, 3, 19500.00, '2024-01-17 11:15:00'),
(4, 4, 4, 1, 35000.00, '2024-01-18 16:45:00'),
(5, 5, 3, 4, 18000.00, '2024-01-19 09:30:00'),
(6, 6, 4, 2, 5600.00, '2024-01-20 13:20:00'),
(7, 7, 3, 3, 16500.00, '2024-01-21 15:10:00'),
(8, 8, 4, 1, 12000.00, '2024-01-22 12:00:00');