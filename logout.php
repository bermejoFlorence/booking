<?php 
	session_start();

	// Clear session
	$_SESSION = array();

	// Destroy session cookie
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time() - 86400, '/');
	}

	// Destroy session
	session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    Swal.fire({
        title: "Logged Out!",
        text: "You have successfully logged out.",
        icon: "success",
        timer: 2000, // 2 seconds before redirect
        showConfirmButton: false
    }).then(() => {
        window.location.href = "login.php";
    });
</script>

</body>
</html>
