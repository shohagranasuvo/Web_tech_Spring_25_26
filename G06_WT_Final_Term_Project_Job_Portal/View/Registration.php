<?php
include "../Controller/RegistrationController.php";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
</head>
<body>
    <h1>Registration Form</h1>
    <form method="post" action="../Controller/RegistrationController.php" enctype="multipart/form-data">
        <table>
            <tr>
                <td><Label for ="type"> Select Role:</Label></td>
                <td><input type ="radio" name="type" value="employee">Employee
                    <input type="radio" name="type" value="jobseeker">Job Seeker</td>
            </tr>
            <tr>
                <td><label for="name">Name:</label></td>
                <td><input type="text" id ="name" name="name" placeholder="Enter your name"></td>
            </tr>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" placeholder="Enter your email"></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" placeholder="Enter your password"></td>
            </tr>
            <tr>
                <td>File Upload:</td>
                <td><input type="file" name="file"></td><br>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="Register"></td>
            </tr>
        </table>
    </form>
</body>
</html>
        