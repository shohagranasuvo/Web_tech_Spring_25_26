<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../View/login.php");
    exit();
}

include "../Model/db.php";

$category = new db();
$connection = $category->connection();


// ADD CATEGORY
if (isset($_POST["add_category"])) {

    $name = trim($_POST["category_name"] ?? "");

    if (empty($name)) {
        header("Location: ../View/AdminCategory.php?error=empty");
        exit();
    }

    $result = $category->addCategory($connection, $name);

    if ($result) {
        header("Location: ../View/AdminCategory.php?success=added");
    } else {
        header("Location: ../View/AdminCategory.php?error=add_failed");
    }

    exit();
}



// UPDATE CATEGORY
if (isset($_POST["update_category"])) {

    $id = intval($_POST["update_id"] ?? 0);
    $name = trim($_POST["update_name"] ?? "");

    if ($id <= 0 || empty($name)) {
        header("Location: ../View/AdminCategory.php?error=empty");
        exit();
    }

    $result = $category->updateCategory($connection, $id, $name);

    if ($result) {
        header("Location: ../View/AdminCategory.php?success=updated");
    } else {
        header("Location: ../View/AdminCategory.php?error=update_failed");
    }

    exit();
}



// DELETE CATEGORY
if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    if (!$category->checkCategoryExists($connection, $id)) {
        header("Location: ../View/AdminCategory.php?error=category_not_found");
        exit();
    }

    // check jobs exist
    if ($category->checkJobsInCategory($connection, $id)->num_rows > 0) {
        header("Location: ../View/AdminCategory.php?error=has_jobs");
        exit();
    }

    $result = $category->deleteCategory($connection, $id);

    if ($result) {
        header("Location: ../View/AdminCategory.php?success=deleted");
    } else {
        header("Location: ../View/AdminCategory.php?error=delete_failed");
    }

    exit();
}


header("Location: ../View/AdminCategory.php");
exit();
?>