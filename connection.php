<?php
    //connecting to database....
    $database= new mysqli("localhost","root","","booking_db");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>