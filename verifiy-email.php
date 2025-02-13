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

    if ($result->num_rows == 1) {
        $updateQuery = "UPDATE client SET verified=1 WHERE verification_code='$verification_code'";
        $database->query($updateQuery);

        $updateWebuser = "UPDATE webuser SET verified=1 WHERE email=(SELECT c_email FROM client WHERE verification_code='$verification_code')";
        $database->query($updateWebuser);

        echo '<script>
            alert("Email verified! You can now log in.");
            window.location.href = "login.php";
        </script>';
    } else {
        echo '<script>
            alert("Invalid or already verified.");
            window.location.href = "login.php";
        </script>';
    }
} else {
    header("Location: login.php");
}
?>
