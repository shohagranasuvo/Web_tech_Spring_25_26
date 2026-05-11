<?php

class db{

function connection() {
        $db_host ="localhost" ;
        $db_user="root";
        $db_password="";
        $db_database="university_db" ;
        $connection =new mysqli($db_host,$db_user,$db_password,$db_database);
        if($connection->connect_error)
        {
            die("con't connect to database".$connection->connect_error);
        }
        return $connection ;

    }
function register ($connection ,$id ,$name ,$email ,$age ,$dept)
{
    $tableName="students";
    $sql = "INSERT INTO ".$tableName." (name,email ,age,dept ) values ('".$name."' ,'".$email."' ,'".$age."' ,'".$dept."')" ;
    $result =$connection->query($sql);
    return $result ;
}    


}
?>