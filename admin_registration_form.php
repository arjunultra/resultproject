<?php
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

// Validation variables
$adminNameError = $adminMobileError = $adminUserNameError = $adminPasswordError = $duplicateEntryError = "";

// Update variables
$update_id = isset($_REQUEST['update_id']) ? $_REQUEST['update_id'] : "";
$update_admin_name = "";
$update_admin_mobile = "";
$update_admin_username = "";
$update_admin_password = "";

// Fetch admin data for update if update_id is set
if ($update_id) {
    $query = "SELECT * FROM admins WHERE id='$update_id'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $update_admin_name = $row['admin_name'];
        $update_admin_mobile = $row['admin_mobile'];
        $update_admin_username = $row['admin_username'];
        $update_admin_password = $row['admin_password'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate admin name
    if (empty($_POST['admin_name'])) {
        $adminNameError = "Admin name cannot be empty.";
        $isValid = false;
    }

    // Validate admin mobile
    if (empty($_POST['admin_mobile'])) {
        $adminMobileError = "Admin mobile cannot be empty.";
        $isValid = false;
    }

    // Validate admin username
    if (empty($_POST['admin_username'])) {
        $adminUserNameError = "Admin username cannot be empty.";
        $isValid = false;
    }

    // Validate admin password
    if (empty($_POST['admin_password'])) {
        $adminPasswordError = "Admin password cannot be empty.";
        $isValid = false;
    }

    if ($isValid) {
        $admin_name = $_POST['admin_name'];
        $admin_mobile = $_POST['admin_mobile'];
        $admin_username = $_POST['admin_username'];
        $admin_password = $_POST['admin_password'];

        if ($update_id) {
            // Update operation
            $sqlUpdate = "UPDATE admins SET admin_name='$admin_name', admin_mobile='$admin_mobile', admin_username='$admin_username', admin_password='$admin_password' WHERE id='$update_id'";
            if (mysqli_query($conn, $sqlUpdate)) {
                echo "<script>alert('Record updated successfully.');</script>";
            } else {
                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
            }
        } else {
            // Check for duplicates before insert
            $checkDuplicate = "SELECT * FROM admins WHERE admin_username='$admin_username'";
            $duplicateResult = mysqli_query($conn, $checkDuplicate);

            if (mysqli_num_rows($duplicateResult) > 0) {
                $duplicateEntryError = "This admin username already exists.";
            } else {
                // Insert operation
                $sqlInsert = "INSERT INTO admins (admin_name, admin_mobile, admin_username, admin_password) VALUES ('$admin_name', '$admin_mobile', '$admin_username', '$admin_password')";
                if (mysqli_query($conn, $sqlInsert)) {
                    echo "<script>
                            alert('New record created successfully.');
                            setTimeout(function() {
                                window.location.href = 'admin_registration_form.php';
                            }, 3000);
                          </script>";
                } else {
                    echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration Form</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <h1 class="main-title text-center">Admin Registration</h1>
    <div class="container-sm">
        <form method="POST" class="form w-100 text-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="update_id" value="<?= $update_id ?>">
            <div class="form-group">
                <label for="admin-name">Admin Name:</label>
                <input value="<?php echo isset($_POST['admin_name']) ? $_POST['admin_name'] : $update_admin_name; ?>"
                    type="text" id="admin-name" name="admin_name" class="form-control">
                <span class="error"><?php echo $adminNameError; ?></span>

                <label for="admin-mobile">Admin Mobile:</label>
                <input
                    value="<?php echo isset($_POST['admin_mobile']) ? $_POST['admin_mobile'] : $update_admin_mobile; ?>"
                    type="text" id="admin-mobile" name="admin_mobile" class="form-control">
                <span class="error"><?php echo $adminMobileError; ?></span>

                <label for="admin-username">Admin Username:</label>
                <input
                    value="<?php echo isset($_POST['admin_username']) ? $_POST['admin_username'] : $update_admin_username; ?>"
                    type="text" id="admin-username" name="admin_username" class="form-control">
                <span class="error"><?php echo $adminUserNameError; ?></span>

                <label for="admin-password">Admin Password:</label>
                <input
                    value="<?php echo isset($_POST['admin_password']) ? $_POST['admin_password'] : $update_admin_password; ?>"
                    type="password" id="admin-password" name="admin_password" class="form-control">
                <span class="error"><?php echo $adminPasswordError; ?></span>
            </div>
            <br>
            <input class="btn btn-primary" type="submit" value="Submit">
            <a class="btn btn-outline-warning ms-3" href="./admission_table.php">Go to Table</a>
            <br><br>
            <span class="error"><?php echo $duplicateEntryError; ?></span>
        </form>
    </div>
</body>

</html>