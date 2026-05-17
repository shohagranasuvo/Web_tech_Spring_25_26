<?php
include "../Model/db.php";
session_start();

// admin check
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: Login.php");
    exit();
}

$database = new db();
$connection = $database->connection();

$error_message = "";
$success_message = "";

if (isset($_GET['error'])) {

    if ($_GET['error'] == "empty")
        $error_message = "Category name cannot be empty.";

    elseif ($_GET['error'] == "add_failed")
        $error_message = "Failed to add category.";

    elseif ($_GET['error'] == "update_failed")
        $error_message = "Failed to update category.";

    elseif ($_GET['error'] == "delete_failed")
        $error_message = "Failed to delete category.";

    elseif ($_GET['error'] == "has_jobs")
        $error_message = "Cannot delete category! Jobs are currently referencing it.";

    elseif ($_GET['error'] == "category_not_found")
        $error_message = "Category not found.";
}

if (isset($_GET['success'])) {

    if ($_GET['success'] == "added")
        $success_message = "Category added successfully!";

    elseif ($_GET['success'] == "updated")
        $success_message = "Category updated successfully!";

    elseif ($_GET['success'] == "deleted")
        $success_message = "Category deleted successfully!";
}

$categories = $database->getAllCategories($connection);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Category Panel</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }

        h2, h3, h4 {
            color: #333;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
        }

        .error {
            background-color: #ffe6e6;
            color: red;
            border: 1px solid red;
        }

        .success {
            background-color: #e6ffe6;
            color: green;
            border: 1px solid green;
        }

        input[type=text] {
            width: 70%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type=submit] {
            padding: 10px 20px;
            font-size: 15px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: darkblue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-edit {
            background-color: orange;
            color: white;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            margin-right: 5px;
        }

        .btn-edit:hover {
            background-color: darkorange;
        }

        .btn-delete {
            background-color: red;
            color: white;
            padding: 6px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }

        .btn-delete:hover {
            background-color: darkred;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Admin Dashboard</h2>
    <h3>Job Category Management</h3>

    <?php if (!empty($error_message)): ?>

        <div class="message error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($success_message)): ?>

        <div class="message success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>

    <?php endif; ?>

    <div style="margin-bottom: 20px; background: #fafafa; padding: 15px; border-radius: 5px;">

        <?php if (isset($_GET['edit_id'])): ?>

            <h4>Edit Category Name</h4>

            <form method="POST" action="../Controller/CategoryController.php">

                <input type="hidden"
                       name="update_id"
                       value="<?php echo intval($_GET['edit_id']); ?>">

                <input type="text"
                       name="update_name"
                       value="<?php echo htmlspecialchars($_GET['edit_name'] ?? ''); ?>"
                       required>

                <input type="submit"
                       name="update_category"
                       value="Update Category">

                <a href="AdminCategory.php"
                   style="margin-left:10px; color:#555; text-decoration:none; font-size:14px;">
                   Cancel
                </a>

            </form>

        <?php else: ?>

            <h4>Add New Category</h4>

            <form method="POST" action="../Controller/CategoryController.php">

                <input type="text"
                       name="category_name"
                       placeholder="Enter new category name..."
                       required>

                <input type="submit"
                       name="add_category"
                       value="Add Category">

            </form>

        <?php endif; ?>

    </div>

    <table>

        <thead>

        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>

        </thead>

        <tbody>

        <?php if ($categories->num_rows > 0): ?>

            <?php while ($row = $categories->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row['id']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </td>

                    <td>

                        <a href="AdminCategory.php?edit_id=<?php echo $row['id']; ?>&edit_name=<?php echo urlencode($row['name']); ?>"
                           class="btn-edit">
                           Edit
                        </a>

                        <a href="../Controller/CategoryController.php?delete=<?php echo $row['id']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Are you sure you want to delete this category?');">
                           Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>

                <td colspan="3" style="text-align:center;">
                    No categories found.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

</body>
</html>