-- Create Database
CREATE DATABASE IF NOT EXISTS bookshelf_db;

-- Use the database
USE bookshelf_db;

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data (optional)
INSERT INTO books (title, author, price, stock) VALUES
('The Great Gatsby', 'F Scott Fitzgerald', 12.99, 15),
('To Kill a Mockingbird', 'Harper Lee', 14.50, 20),
('Pride and Prejudice', 'Jane Austen', 10.99, 10);
