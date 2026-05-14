<?php
include "db.php";

function getAllBooks($connection)
{
    $sql = "SELECT * FROM books ORDER BY id DESC";
    $result = mysqli_query($connection, $sql);
    
    $books = array();
    if($result && mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $books[] = $row;
        }
    }
    
    return $books;
}

function insertBook($connection, $title, $author, $price, $stock)
{
    $title = mysqli_real_escape_string($connection, $title);
    $author = mysqli_real_escape_string($connection, $author);
    $price = mysqli_real_escape_string($connection, $price);
    $stock = mysqli_real_escape_string($connection, $stock);
    
    $sql = "INSERT INTO books (title, author, price, stock) VALUES ('$title', '$author', '$price', '$stock')";
    $result = mysqli_query($connection, $sql);
    
    return $result;
}

function deleteBook($connection, $id)
{
    $id = mysqli_real_escape_string($connection, $id);
    
    $sql = "DELETE FROM books WHERE id = '$id'";
    $result = mysqli_query($connection, $sql);
    
    return $result;
}
?>
