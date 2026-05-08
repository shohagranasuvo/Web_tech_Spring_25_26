<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method ='post' action ="../Controller/RegistrationValidation.php"  enctype="multipart/form-data" >
        <?php
        echo "<h1 style='color:black'>PHP form validation example</h1>" ;
        echo "<h2 style='color:red'></h1>" ;

        ?>
        <table>
            <tr><td>User Name :</td>
            <td> <input type="text" name="name"></td>
        
        </tr>
        <tr><td>E-mail : </td>
        <td><input type="text" name="email"></td>
        <tr><td></td>
         <tr><td>Password:</td> <td><input type="password" name="password"></td></tr>
        <tr><td>Website:</td> <td><input type="text" name="website"></td></tr>
        <tr><td>Comment:</td><td><input type="text" name="comment"></td></tr>
        <tr><td>Gender</td><td><input type="radio" name="gender" value="male">Male
        <input type="radio" name="gender" value="female">female </td></tr>
        <tr>
            <td> File Upload </td>
            <td><input type ="file" id ="file" name="file"></td>
          </tr> <br>     
        <td>  <input type="submit" name="Submit"></td></tr>

        </table>
    </form>
   
</body>
</html>