<?php

class db{
  fuction connection(){
    public $db_host ="localhost" ;
    public $db_user ="root" ;
    public $db_password="";
    public $db_database ="web_tech_validation" ;
    $connection =new mysqli($db_host,$db_user,$db_password,$db_database) ;
    if(connection->connect_error)
    {
        die("Could not connect Database".$connection->connect_error) ;
    }
    return $connection

}

fuction signup($connection ,$tablename ,$username ,$password)
{
    $sql ="INSERT INTO " .$tablename."(username,password) VALUES ('"$username"' ,'"$password"')" ;
    $result =$connection->query($sql) ;
    return $result ;

}

fuction signin($connection ,$tablename ,$username,$password)
{
    $result =connectiom->query(sql) ;
    return $result ;
}


    
}