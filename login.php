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
// validation variables
$adminUsernameError = $adminPasswordError = "";
$adminID = $studentID = "";

// post variables
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminUsername = $_POST['admin_username'];
    $adminPassword = $_POST['admin_password'];
    $isValid = true;
    // Validate admin username
    if (empty($_POST['admin_username'])) {
        $adminUsernameError = "Admin username cannot be empty.";
        $isValid = false;
    } else {
        if (!preg_match('/^\w{3,15}$/', $adminUsername)) {
            $adminUsernameError = "Admin username is invalid.";
            $isValid = false;
            /*
            ^ asserts the start of the string.
        \w matches any word character (equivalent to [a-zA-Z0-9_]).
        {3,15} ensures the length is between 3 and 15 characters.
        $ asserts the end of the string.*/

        }
    }
    // Validate admin password
    if (empty($_POST['admin_password'])) {
        $adminPasswordError = "Admin Password cannot be empty.";
        $isValid = false;
    } else {
        if (!preg_match('/^[1-9A-Za-z]{8,}$/', $adminPassword)) {
            $isValid = false;
            $adminPasswordError = "Admin Password Invalid!";
        }
    }
    if ($isValid) {
        $sql = "SELECT id FROM admins WHERE admin_username = '$adminUsername' AND admin_password = '$adminPassword'";
        $result = mysqli_query($conn, $sql);
        // Check if the query was successful and if it returned at least one row
        if ($result && mysqli_num_rows($result) > 0) {
            // Fetch the first row of the result
            $row = mysqli_fetch_assoc($result);
            $adminID = $row['id'];
        }
    }

    if (!empty($adminID)) {
        $userType = "Admin";
    } else {
        $studentsql = "SELECT id FROM studentadmission WHERE student_name = '$adminUsername'AND student_rollno = '$adminPassword'";
        $studentResult = mysqli_query($conn, $studentsql);
        if (!empty($studentResult)) {
            foreach ($studentResult as $row) {
                $studentID = $row['id'];
            }
        }
    }
    if (!empty($studentID)) {
        $userType = "Student";
    }
    // echo $adminID . "/" . $studentID . "hello";
    if (!empty($adminID) || !empty($studentID)) {
        $_SESSION['user_name'] = $adminUsername;
        $_SESSION['password'] = $adminPassword;
        $_SESSION['user_type'] = $userType;
        header("location: dashboard.php");
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <h1 class="main-title text-center">Login Form</h1>
    <div class="container-xs">
        <form method="POST" class="form w-100 text-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="update_id" value="<?= $update_id ?>">
            <div class="form-group">
                <label for="admin-username">User Name:</label>
                <input value="<?php echo isset($_POST['admin_username']) ? $_POST['admin_username'] : ""; ?>"
                    type="text" id="admin-username" name="admin_username" class="form-control w-75">
                <span class="error text-bg-danger"><?php echo $adminUsernameError; ?></span>
                <label for="admin-password">Password:</label>
                <input value="<?php echo isset($_POST['admin_password']) ? $_POST['admin_password'] : ""; ?>"
                    type="password" id="admin-password" name="admin_password" class="form-control w-75">
                <span class="text-bg-danger error"><?php echo $adminPasswordError; ?></span>
            </div>
            <button class="btn btn-danger btn-lg mt-5 rounded-pill " type="submit">Login</button>
            <button class="btn btn-primary py-2 mt-5 ms-3 rounded-pill " type="button">Forgot Password</button>
        </form>
</body>
<?php mysqli_close($conn); ?>

</html>