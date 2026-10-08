<?php
    $host= 'localhost';
    $user= 'root';
    $pass= '';
    $db = 'e_commerce';
    

    $mysqli = new mysqli($host,$user,$pass,$db);


    if ($mysqli->connect_error) {
        echo "Failed to connect to MySql: ". $mysqli->connect_error;
        exit();
    }
    
    else{
        // Do Nothing
    }
 
?>