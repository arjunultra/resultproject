<?php
session_start();
$user_name = $_SESSION["user_name"];


if (isset($_SESSION["user_name"]) && isset($_SESSION["password"])) {
    echo "<p class='display-4 text-center text-bg-success'>Hello " . $_SESSION['user_name'] . " welcome 👋</p>";
    echo "<p class='display-6 text-center text-bg-info'>You are logged in as " . $_SESSION['user_type'] . "</p>";
}
?>


<!-- html starts -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Dashboard</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <a class="btn btn-outline-danger btn-lg d-block w-25 mt-5 mx-auto" href="logout.php">log out</a>
</body>

</html>