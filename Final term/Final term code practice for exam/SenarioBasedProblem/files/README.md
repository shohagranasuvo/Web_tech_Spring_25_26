# Bookshelf Management System

A simple web-based bookshelf management system for libraries or bookstores to manage book records efficiently.

## Technologies Used
- PHP (MySQLi Procedural Extension)
- MySQL Database
- HTML/CSS
- JavaScript (AJAX)

## Features
- Add new books with validation
- View all books in a dynamic table
- Delete books by ID
- Real-time validation with error messages
- No page reload (AJAX-powered)
- JSON-based communication between frontend and backend

## File Structure

```
bookshelf-management/
│
├── db.php                  # Database connection class
├── bookModel.php           # Database operations (get, insert, delete)
├── bookController.php      # Request handler and validation logic
├── book_form.php           # User interface (HTML form and table)
├── ajax.js                 # AJAX functions for form submission and deletion
└── database_setup.sql      # SQL script to create database and table
```

## Setup Instructions

### 1. Database Setup
- Open phpMyAdmin or MySQL command line
- Import or run the `database_setup.sql` file to create the database and table
- Or manually run the SQL commands:
  ```sql
  CREATE DATABASE bookshelf_db;
  USE bookshelf_db;
  -- Then create the books table as shown in database_setup.sql
  ```

### 2. Configure Database Connection
- Open `db.php`
- Update the database credentials if needed:
  ```php
  $db_host = "localhost";
  $db_user = "root";
  $db_password = "";
  $db_name = "bookshelf_db";
  ```

### 3. File Placement
- Place all files in your web server directory (e.g., htdocs for XAMPP, www for WAMP)
- Make sure all files are in the same directory

### 4. Access the System
- Start your web server (Apache and MySQL)
- Open your browser and navigate to:
  ```
  http://localhost/book_form.php
  ```

## Validation Rules

### Title
- Must not be empty
- Can only contain letters, spaces, and hyphens
- Example: "The Great Gatsby" ✓, "Book-123" ✗

### Author
- Must not be empty
- Minimum 3 characters required
- Example: "Jane Austen" ✓, "Jo" ✗

### Price
- Must be numeric
- Must be greater than 0
- Example: 12.99 ✓, -5 ✗, "abc" ✗

### Stock
- Must be an integer
- Must be greater than or equal to 1
- Example: 10 ✓, 0 ✗, 1.5 ✗

## How It Works

1. **Adding a Book:**
   - User fills out the form and clicks "Add Book"
   - AJAX sends data to `bookController.php`
   - Controller validates all fields
   - If valid, `insertBook()` is called from `bookModel.php`
   - Success/error message is displayed
   - Book table is updated without page reload

2. **Viewing Books:**
   - On page load, AJAX calls `bookController.php?action=getAll`
   - `getAllBooks()` retrieves all records from database
   - Books are displayed in a dynamic table

3. **Deleting a Book:**
   - User clicks "Delete" button on a book row
   - Confirmation dialog appears
   - AJAX sends delete request to `bookController.php`
   - `deleteBook()` removes the record
   - Book table is updated without page reload

## API Endpoints (AJAX)

### Add Book
- **Method:** POST
- **Data:** action=add, title, author, price, stock
- **Response:** JSON with success status, message, and updated book list

### Delete Book
- **Method:** POST
- **Data:** action=delete, id
- **Response:** JSON with success status, message, and updated book list

### Get All Books
- **Method:** GET
- **URL:** bookController.php?action=getAll
- **Response:** JSON with success status and books array

## Error Handling
- All validation errors are displayed in red below the respective input fields
- Database connection errors are caught and displayed
- User-friendly messages for all operations
- Confirmation dialog before deleting books

## Security Features
- Input sanitization using `mysqli_real_escape_string()`
- Server-side validation for all fields
- Prepared statements recommended for production (upgrade from current implementation)

## Future Enhancements
- Edit/Update book functionality
- Search and filter books
- User authentication
- Book categories/genres
- Export to CSV/PDF
- Pagination for large datasets
- Image upload for book covers

## Troubleshooting

### Books not loading?
- Check if database connection is successful
- Verify database name and table exist
- Check browser console for JavaScript errors

### Form not submitting?
- Check if ajax.js is properly loaded
- Verify bookController.php path is correct
- Check browser console for AJAX errors

### Database connection errors?
- Verify MySQL server is running
- Check database credentials in db.php
- Ensure bookshelf_db database exists

## License
This is a educational project for learning PHP, MySQL, and AJAX.
