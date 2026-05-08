<?php
season_start();
$datafile="../data.json" ;

$name="" ;
$password="";
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $name=$_POST["name"];
    $password=$_POST["password"];
    if(!empty($name) && strlen($name)>=5 && strlen($password)>=5)
    {
        echo "Log in done buddy" ;
        setcookie("UserName",$name ,time()+3600);
        $formdata=array("Name"=>$name ,"Password"=>$password);
        

    }
    else
    {
        echo "Fail to log in" ;
    }

}
