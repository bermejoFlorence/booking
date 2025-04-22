<?php
// Enable CORS if called from browser JS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Read raw JSON input from fetch
$input = json_decode(file_get_contents("php://input"), true);

$name = $input["name"] ?? "Unknown";
$email = $input["email"] ?? "unknown@example.com";
$amount = $input["amount"] ?? 0; // must be in centavos (e.g., 10000 = ₱100)

if ($amount < 1000) { // PHP 10.00 minimum
    echo json_encode(["error" => "Minimum amount is ₱10"]);
    exit();
}

// Replace with your own TEST SECRET KEY from PayMongo
$secretKey = "sk_test_MtY68qsiaBZpDr9XBgqjPf5L";

// Build PayMongo request payload
$payload = [
    "data" => [
        "attributes" => [
            "amount" => $amount,
            "redirect" => [
                "success" => "https://exzphotography/client/payment_success.php",
                "failed" => "https://exzphotography/client_failed.php"
            ],
            "billing" => [
                "name" => $name,
                "email" => $email
            ],
            "type" => "gcash",
            "currency" => "PHP"
        ]
    ]
];

// Send POST request to PayMongo
$ch = curl_init("https://api.paymongo.com/v1/sources");
curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ":");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return raw response to frontend
if ($httpCode === 201) {
    echo $response;
} else {
    echo json_encode(["error" => "Failed to create GCash link", "status" => $httpCode, "response" => $response]);
}
