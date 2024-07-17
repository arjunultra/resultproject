<?php
// Check if session has not started and start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "resultsdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check Connection
if (!$conn) {
    die("Connection Failed:" . mysqli_connect_error());
}
$logged_user = "";
$logged_user = $_SESSION['user_name'];

// Initialize userType with a default value
$userType = "";

// Now, safely check if 'user_type' is in session and update userType accordingly
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
    if ($userType == "Student") {
    }
}
$resultdisplayquery = "SELECT * FROM markentry WHERE student_name = '$logged_user'";
$result = mysqli_query($conn, $resultdisplayquery);

if ($result) {
    $studentResult = mysqli_fetch_assoc($result);
    // echo '<pre>' . print_r($studentResult, true) . '</pre>';
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php
    if ($userType == "Student") {
        echo ('<button class="d-none btn btn-primary mt-5 ms-5" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Menu</button>');

    } else if ($userType == "Admin") {
        echo ('<button class="btn btn-primary mt-5 ms-5" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Menu</button>');
    } ?>
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-center" id="offcanvasWithBothOptionsLabel">Navigation Menu</h5>
            <button id="button-close" type="button" class="btn-close" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="container-fluid">
                <ul class="d-flex flex-column justify-content-evenly gap-3 align-items-center list-unstyled">
                    <?php
                    if ($userType == "Admin") { ?>

                        <li class=""><a class="text-decoration-none text-uppercase" href="admin_registration_form.php">Admin
                                Registration</a>
                        </li>
                        <li class=""><a class="text-decoration-none text-uppercase" href="subjects_form.php">Subjects
                                Form</a>
                        </li>
                        <li class=""><a class="text-decoration-none text-uppercase" href="class_form.php">Class Form</a>
                        </li>
                        <li class=""><a class="text-decoration-none text-uppercase" href="admission_form.php">Student
                                Admission
                                Form</a></li>
                        <li class=""><a class="text-decoration-none text-uppercase" href="mark_entry.php">Student
                                Mark Entry</a></li>
                        <li class=""><a class="text-decoration-none text-uppercase text-bg-danger"
                                href="logout.php">Logout</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="./JS/bootstrap.bundle.min.js"></script>
</body>

</html>