<?php
include "../Model/db.php" ;
session_start() ;
$id="";
$name="" ;
$email="";
$age="";
$dept="";


if($_SERVER["REQUEST_METHOD"]=="POST")

    {
        $id=$_POST["id"];
        $name=$_POST["name"];
        $email=$_POST["email"];
        $age=$_POST["age"];
        $dept=$_POST["department"];
        if(strlen($name)>=5  && $age>=18)
        {
            $db =new db();
            $connection =$db->connection();
            $result =$db->register($connection ,$id ,$name,$email ,$age ,$dept);
            if($result)
            {

                 $_SESSION["UserName"] = $name; 
                   echo "Registration Successful";
                    exit(); 
            }


        }
        else
        {
            echo "Invalid" ;
        }
    }


?>