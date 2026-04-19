<?php

$name="" ;
$password="";
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $name=$_POST["name"];
    $password=$_POST["password"];
    if(!empty($name) && strlen($name)>=5 && strlen($password)>=5)
    {
        echo "Log in done buddy" ;
    }
    else
    {
        echo "Fail to log in" ;
    }

}
