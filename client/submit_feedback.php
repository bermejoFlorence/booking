<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$useremail = $_SESSION["user"];

// Kunin ang client_id ng user
$userrow = $database->query("SELECT * FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["client_id"];
} else {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit();
}

// Mapping ng rating numbers sa word equivalent
$rating_words = [
    1 => "Very Dissatisfied",
    2 => "Dissatisfied",
    3 => "Neutral",
    4 => "Satisfied",
    5 => "Very Satisfied"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating_number = isset($_POST["rating"]) ? intval($_POST["rating"]) : 0;
    $comment = isset($_POST["feedback"]) ? trim($_POST["feedback"]) : "";

    // I-convert ang rating number sa word
    $rating_word = isset($rating_words[$rating_number]) ? $rating_words[$rating_number] : "Unknown";

    if ($rating_number > 0 && !empty($comment)) {
        $stmt = $database->prepare("INSERT INTO feedback (client_id, rating, comment, date_created) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $userid, $rating_word, $comment);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Thank you for your feedback!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Please provide a rating and comment."]);
    }
}
?>
