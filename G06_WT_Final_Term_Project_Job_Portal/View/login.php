<?php
session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <title>LOG IN</title>
</head>
<body>
    <form method ="post" action="../Controller/LoginController.php">
        <table>
            <tr>
                <td><label for="id">ID:</label></td>
                <td><input type="text" id="id" name="id" placeholder="Enter your ID"></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" placeholder="Enter your password"></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="Log In"></td>
            </tr>
        </table>
    </form>
</body>
</html>
