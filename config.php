<?php

    $host = 'localhost';
    $user = 'root';
    $password = '02122003';
    $database = 'api';

    global $conn = new mysqli($host, $user, $password, $database);

    if($conn->connect_error){
        die('Connection failed: ' . $conn->connect_error);
    }

    $conn->close();

?>