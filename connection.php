<?php
      //connecting to database....
    $database= new mysqli("localhost","u941558601_exzphotogprahy","studio_2025S","u941558601_booking_db");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>