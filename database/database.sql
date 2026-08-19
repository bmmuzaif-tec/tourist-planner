CREATE DATABASE IF NOT EXISTS tourist_planner;
USE tourist_planner;

-- Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Tourist Places Table
CREATE TABLE tourist_places (
    place_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    place_name VARCHAR(150) NOT NULL,
    description TEXT,
    address VARCHAR(255),
    opening_hours VARCHAR(100),
    travel_tips TEXT,
    distance DECIMAL(5,2),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Administrator Table
CREATE TABLE administrator (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Visit Plan Table
CREATE TABLE visit_plan (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_name VARCHAR(100),
    visit_date DATE
);

-- Visit Plan Details
CREATE TABLE visit_plan_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    place_id INT,
    visit_order INT,
    FOREIGN KEY (plan_id) REFERENCES visit_plan(plan_id),
    FOREIGN KEY (place_id) REFERENCES tourist_places(place_id)
);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    place_id INT,
    visitor_name VARCHAR(100),
    rating INT,
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (place_id) REFERENCES tourist_places(place_id)
);

-- Contact Messages
CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(200),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);