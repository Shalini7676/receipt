<?php
session_start();
require('database.php');

// Get payment data from URL parameters
$payment_id = $_GET['payment_id'] ?? '';
$amount = $_GET['amount'] ?? 0;
$cause = $_GET['cause'] ?? '';

// ✅ Sanitize inputs
$payment_id = htmlspecialchars($payment_id);
$amount = floatval($amount);
$cause = htmlspecialchars($cause);

// Sanity check for valid payment data
if (empty($payment_id) || $amount <= 0 || empty($cause)) {
    die("Invalid payment data received.");
}

// Insert the payment data into the database
$query = "INSERT INTO moneyy (payment_id, amount, cause, date) 
          VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($query);

// Update bind_param to remove 'user_id'
$stmt->bind_param("sds", $payment_id, $amount, $cause); // Fix type binding

// Execute query and close the statement and connection
if ($stmt->execute()) {
    // Successfully inserted
    $stmt->close();
} else {
    // Handle the failure
    error_log("Database insertion failed: " . $stmt->error);
    die("There was an error processing your donation.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - CharityCart</title>
    <style>
   body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color:rgb(34, 31, 31);
    color: #333;
    padding-top: 120px;
    text-align: center;
}


    header {
    background: linear-gradient(to right, #000000, #333333);
    padding: 2em 2em;
    color: white;
    display: flex;
    justify-content: space-between; /* Space between the div and nav */
    align-items: center;
    position: fixed;
    width: 100%;
    top: 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

header div {
    font-size: 1.5em;
    font-weight: 700;
}
nav {
    display: flex;
    align-items: center;
    gap: 100px; /* Adjust spacing between navigation links */
}


nav a {
            color: white;
            margin-left: 1.5em;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: var(--accent-color);
        }


.confirmation-card {
    background-color: white;
    max-width: 500px;
    margin: 0 auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.container {
            max-width: 900px;
            margin: 3em auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            margin-top: 60px;
        }

.success-icon {
    font-size: 60px;
    color: #00c853;
    margin-bottom: 20px;
}

h2 {
    color: #00c853;
    margin-bottom: 10px;
}

.message {
    font-size: 18px;
    margin: 8px 0;
}

.amount {
    font-size: 24px;
    font-weight: bold;
    margin-top: 15px;
    color: #333;
}

.cta {
    margin-top: 25px;
}

.cta a {
    color: #00c853;
    text-decoration: none;
    font-weight: bold;
}

.cta a:hover {
    text-decoration: underline;
}



    </style>
</head>
<body>
<header>
    <div><strong>CharityCart</strong></div>
    <nav>
        <a href="home.php">Home</a>
        <a href="event.php">Book an Event</a>
        <a href="logout.php">Log Out</a>
    </nav>
    </header>

<div class="confirmation-card">
<div class="container">
    <div class="success-icon">✔</div>
    <h2>Payment Successful!</h2>
    <div class="message">Thank you for donating to:</div>
    <div class="message"><strong><?= $cause ?></strong></div>
    <div class="amount">₹<?= number_format($amount, 2) ?></div>
    <div class="cta">
        <p><a href="money.php">Go Back Home</a></p>
        <p><a href="receipt_download.php?payment_id=<?= $payment_id ?>">Download Receipt</a></p>
    </div>
</div>

</body>
</html>
