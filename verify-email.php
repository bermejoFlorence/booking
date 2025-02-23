<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include("connection.php");

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];
    $query = "SELECT * FROM client WHERE verification_code='$verification_code' AND verified=0";
    $result = $database->query($query);

    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

    if ($result->num_rows == 1) {
        $updateQuery = "UPDATE client SET verified=1 WHERE verification_code='$verification_code'";
        $database->query($updateQuery);

        $updateWebuser = "UPDATE webuser SET verified=1 WHERE email=(SELECT c_email FROM client WHERE verification_code='$verification_code')";
        $database->query($updateWebuser);

        echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Email verified! You can now log in.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                 window.location.href = "https://exzphotograpy.com/login.php";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Invalid or already verified.",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "https://exzphotograpy.com/login.php";
            });
        </script>';
    }
} else {
    header("Location: https://exzphotograpy.com/login.php");
}
?>
