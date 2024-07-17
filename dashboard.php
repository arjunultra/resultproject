<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "resultsdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$errorMsg = "";
// Ensure session variables are set
if (isset($_SESSION["user_name"]) && isset($_SESSION["user_type"])) {
    $user_name = $_SESSION["user_name"];
    $userType = $_SESSION["user_type"];

    echo "<p class='display-4 text-center text-bg-success'>Hello " . $user_name . " welcome 👋</p>";
    echo "<p class='display-6 text-center text-bg-info'>You are logged in as " . $userType . "</p>";

    $sqlResult = "SELECT * FROM markentry WHERE student_name = '$user_name'";
    $resultMarks = mysqli_query($conn, $sqlResult);

    $subjectName = "";
    $studentMarks = "";
    $subject = [];
    $mark = [];

    if (mysqli_num_rows($resultMarks) > 0) {
        while ($row = mysqli_fetch_assoc($resultMarks)) {
            $subjectName = $row['subject_name'];
            $studentMarks = $row['student_marks'];
        }
    } else {
        $errorMsg = "<h2 class='text-bg-danger text-center'>Result Not Declared</h2>";
    }

    if (!empty($subjectName) && !empty($studentMarks)) {
        $subject = explode(',', $subjectName);
        $mark = explode(',', $studentMarks);
    }
} else {
    // Redirect to login if session variables are not set
    header("Location: login.php");
    exit();
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
    <!-- marks display area -->
    <div class="container">
        <?php if ($userType == "Admin") { ?>
            <div class='container d-none'></div>
        <?php } else { ?>
            <div id="resultTableContainer" class="table-responsive">
                <?= "<h2 class='text-center display-5'>{$user_name}'s Marks</h2>" ?>
                <table id="resultTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark bg-primary">
                        <tr>
                            <th>Subject Name</th>
                            <th>Marks Secured</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $errorMsg ?>
                        <?php if (!empty($subject) && !empty($mark)) {
                            for ($i = 0; $i < count($subject); $i++) { ?>
                                <tr>
                                    <td><?php echo $subject[$i] ?></td>
                                    <td><?php echo $mark[$i] ?></td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
    <a class="btn btn-outline-danger btn-lg d-block w-25 mt-5 mx-auto" href="logout.php">log out</a>
</body>

</html>