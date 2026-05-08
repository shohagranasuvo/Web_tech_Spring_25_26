<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method ='post' action ="../Controller/LogInValidation.php"  >
        <?php
        echo "<h1 style='color:red'>Log in page</h1>" ;


        ?>
        <table>
            <tr><td>User Name :</td>
            <td> <input type="text"></td>
        
        </tr>
        <tr><td>Password : </td>
        <td><input type="password"></td>
        <tr><td></td>
        <td>  <input type="submit" name="Submit"></td></tr>
        </table>
    </form>
    <?php
    $text1="SHOHAG RANA";
    echo "<b>$text1</b>" ;
    echo "<br>";
    echo '<b>'.$text1.'</b>';
    echo "<br>";
     $num1 =20 ;
     $num2 =10 ;
    echo "sum = " ;
    echo $num1+$num2 ;
    $sub =array("Course " =>array("web tech", "Micro") ,"Pre requisit" =>"Java" ,"Operating System") ;

    echo "<br>";
    var_dump($sub);
     echo "<br>";
     echo var_dump($sub['Course ']) ;

    ?>
</body>
</html>