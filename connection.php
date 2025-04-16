<?php
      //connecting to database....
      $database = new mysqli("localhost", "u941558601_exzphotogprahy", "capstonE@2025", "u941558601_finalbooking");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>