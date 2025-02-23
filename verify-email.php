<?php
ob_start(); // Start output buffering to prevent unwanted output before the script runs
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Secure the query using prepared statements
    $stmt = $database->prepare("SELECT c_email FROM client WHERE verification_code = ? AND verified = 0");
    $stmt->bind_param("s", $verification_code);
    $stmt->execute();
    $stmt->store_result();

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'; // Ensure SweetAlert is loaded

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($c_email);
        $stmt->fetch();
        $stmt->close();

        // Update client verification
        $updateClient = $database->prepare("UPDATE client SET verified = 1 WHERE verification_code = ?");
        $updateClient->bind_param("s", $verification_code);
        $updateClient->execute();
        $updateClient->close();

        // Update webuser verification
        $updateWebuser = $database->prepare("UPDATE webuser SET verified = 1 WHERE email = ?");
        $updateWebuser->bind_param("s", $c_email);
        $updateWebuser->execute();
        $updateWebuser->close();

        // Success SweetAlert with redirection to exzphotograpy.com/login.php
        echo '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "Success!",
                    text: "Email verified! You can now log in.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "https://exzphotograpy.com/login.php";
                });
            }, 500); // Adding slight delay to ensure script execution
        </script>';
    } else {
        $stmt->close();

        // Error SweetAlert with redirection to exzphotograpy.com/login.php
        echo '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "Error!",
                    text: "Invalid or already verified.",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "https://exzphotograpy.com/login.php";
                });
            }, 500);
        </script>';
    }
} else {
    header("Location: https://exzphotograpy.com/login.php");
    exit();
}
?>
