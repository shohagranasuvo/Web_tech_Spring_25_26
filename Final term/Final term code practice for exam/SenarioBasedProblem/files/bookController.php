<?php
include "bookModel.php";

header('Content-Type: application/json');

$database = new db();
$connection = $database->connection();

$response = array();

// Handle Add Book Request
if(isset($_POST['action']) && $_POST['action'] == 'add')
{
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    
    $errors = array();
    
    // Validate Title
    if(empty($title))
    {
        $errors['title'] = "Title is required";
    }
    elseif(!preg_match("/^[a-zA-Z\s\-]+$/", $title))
    {
        $errors['title'] = "Title must contain only letters, spaces, and hyphens";
    }
    
    // Validate Author
    if(empty($author))
    {
        $errors['author'] = "Author is required";
    }
    elseif(strlen($author) < 3)
    {
        $errors['author'] = "Author must be at least 3 characters";
    }
    
    // Validate Price
    if(empty($price))
    {
        $errors['price'] = "Price is required";
    }
    elseif(!is_numeric($price) || $price <= 0)
    {
        $errors['price'] = "Price must be numeric and greater than 0";
    }
    
    // Validate Stock
    if(empty($stock))
    {
        $errors['stock'] = "Stock is required";
    }
    elseif(!ctype_digit($stock) || $stock < 1)
    {
        $errors['stock'] = "Stock must be an integer greater than or equal to 1";
    }
    
    if(count($errors) > 0)
    {
        $response['success'] = false;
        $response['errors'] = $errors;
    }
    else
    {
        $result = insertBook($connection, $title, $author, $price, $stock);
        
        if($result)
        {
            $response['success'] = true;
            $response['message'] = "Book added successfully!";
            $response['books'] = getAllBooks($connection);
        }
        else
        {
            $response['success'] = false;
            $response['message'] = "Failed to add book. Please try again.";
        }
    }
}

// Handle Delete Book Request
elseif(isset($_POST['action']) && $_POST['action'] == 'delete')
{
    $id = $_POST['id'] ?? '';
    
    if(empty($id))
    {
        $response['success'] = false;
        $response['message'] = "Invalid book ID";
    }
    else
    {
        $result = deleteBook($connection, $id);
        
        if($result)
        {
            $response['success'] = true;
            $response['message'] = "Book deleted successfully!";
            $response['books'] = getAllBooks($connection);
        }
        else
        {
            $response['success'] = false;
            $response['message'] = "Failed to delete book. Please try again.";
        }
    }
}

// Handle Get All Books Request
elseif(isset($_GET['action']) && $_GET['action'] == 'getAll')
{
    $books = getAllBooks($connection);
    $response['success'] = true;
    $response['books'] = $books;
}

mysqli_close($connection);
echo json_encode($response);
?>
