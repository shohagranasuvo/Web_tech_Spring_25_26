<?php

include "../Model/db.php"; 
//season_start();
$datafile="../data.json" ;

$name="" ;
$email="";
$website="";
$comment="";

if($_SERVER["REQUEST_METHOD"]=="POST")
{
   
    if (empty($_POST["name"])) {
        $errors[] = "Name is required.";
    } else {
        $name = htmlspecialchars(trim($_POST["name"]));
        if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
            $errors[] = "Name must only contain letters and spaces.";
        }
    }

    if (empty($_POST["email"])) {
        $errors[] = "E-mail is required.";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }

    if (!empty($_POST["website"])) {
        $website = htmlspecialchars(trim($_POST["website"]));
        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            $errors[] = "Invalid website URL.";
        }
    }

    $comment = htmlspecialchars(trim($_POST["comment"] ?? ""));

    if (empty($_POST["gender"])) {
        $errors[] = "Gender is required.";
    } else {
        $gender = htmlspecialchars($_POST["gender"]);
    }

    if (empty($errors)) {

        $new_data = [
            "Name" => $name,
            "Email" => $email,
            "Website" => $website,
            "Comment" => $comment,
            "Gender" => $gender
        ];

        $current_data = file_exists($datafile)
            ? json_decode(file_get_contents($datafile), true)
            : [];

        if (!is_array($current_data)) {
            $current_data = [];
        }

        $current_data[] = $new_data;

        file_put_contents($datafile, json_encode($current_data, JSON_PRETTY_PRINT));

        setcookie("UserName", $name, time() + 3600);

        echo "Form submitted successfully!";

        $database =new db();
        $connection =$database->connection();
        $result =$database->signup() ;

    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>


