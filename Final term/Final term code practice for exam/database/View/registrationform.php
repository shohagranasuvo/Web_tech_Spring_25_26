<?php
include "../Controller/formController.php";
echo "<h1>Registration</h1>";
?>

<!DOCTYPE html>
<html>
    <form method ="post" action ="../Controller/formController.php">
        <table>
           <tr>
    <td><label for="id">ID</label></td>
    <td><input type="number" id="id" name="id"></td>
</tr>

<tr>
    <td><label for="name">Name</label></td>
    <td><input type="text" id="name" name="name"></td>
</tr>

<tr>
    <td><label for="email">Email</label></td>
    <td><input type="email" id="email" name="email"></td>
</tr>

<tr>
    <td><label for="age">Age</label></td>
    <td><input type="number" id="age" name="age"></td>
</tr>

<tr>
    <td><label for="department">Department</label></td>
    <td><input type="text" id="department" name="department"></td>
</tr>
<tr><td><input type="submit"></td></tr>
</table>
</form>
</html>