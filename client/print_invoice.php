<?php

session_start();

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALES INVOICE</title>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                width: 8.5in;
                height: 11in;
                margin: 0;
                padding: 20px;
            }

            .invoice-container {
                width: 100%;
                border: 1px solid black;
                padding: 20px;
                box-sizing: border-box;
            }

            h1 {
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 10px;
                text-align: left;
            }

            .no-print {
                display: none; /* Para hindi maisama sa print */
            }
        }

        .print-btn {
            margin: 20px;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <h1>SALES INVOICE</h1>
      
    </div>

    <button class="print-btn no-print" onclick="window.print()">Print</button>

</body>
</html>
