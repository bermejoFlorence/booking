<?php
include '../connection.php'; // Siguraduhin na tama ang connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Siguraduhin na may natanggap na data
    if (!isset($_POST['client_name'], $_POST['phone_num'], $_POST['email'], $_POST['message'])) {
        echo "error: Missing required fields!";
        exit;
    }

    $client_name = trim($_POST['client_name']);
    $phone_num = trim($_POST['phone_num']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $emp_id = 1; // Palitan kung kinakailangan

    // Iwasan ang SQL Injection
    $client_name = mysqli_real_escape_string($database, $client_name);
    $phone_num = mysqli_real_escape_string($database, $phone_num);
    $email = mysqli_real_escape_string($database, $email);
    $message = mysqli_real_escape_string($database, $message);

    // SQL Query para i-save sa database
    $query = "INSERT INTO contact_info (emp_id, client_name, phone_num, email, message, date_created) 
              VALUES ('$emp_id', '$client_name', '$phone_num', '$email', '$message', NOW())";

    if (mysqli_query($database, $query)) {
        echo "success"; // Success response sa AJAX
    } else {
        echo "error: " . mysqli_error($database); // Ipakita ang aktwal na error
    }

    mysqli_close($database);
}
?>
