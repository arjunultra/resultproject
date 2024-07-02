<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "resultsdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection
    failed: " . mysqli_connect_error());
}
// update functionality
$update_id = "";
$edit_subject_code = "";
$edit_subject_name = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = " SELECT * FROM subjects WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_subject_code = $row['subject_code'];
            $edit_subject_name = $row['subject_name'];
        }
    }
}

// Create subjects table if not exists
$sqlCreateSubjects = " CREATE TABLE IF NOT EXISTS subjects ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, subject_code
    VARCHAR(255) NOT NULL,subject_name VARCHAR(255) NOT NULL )";
if (mysqli_query($conn, $sqlCreateSubjects)) { // Check if the table was actually created or it already existed 
    if (mysqli_affected_rows($conn) > 0) {
        echo "Subjects table created successfully.<br>";
    }
} else {
    echo "Error creating subjects table: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Subject Entry Form</title>
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <h1 class="main-title text-center">Enter Your Subjects</h1>
    <form method="POST" class="form w-100 text-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="update_id" value="<?= $update_id ?>">
        <div class="form-group">
            <label for="subject-code">Subject Code:</label>
            <input
                value="<?php echo isset($_POST['subject_code']) ? $_POST['subject_code'] : (isset($edit_subject_code) ? $edit_subject_code : ''); ?>"
                type="text" id="subject-code" name="subject_code" class="form-control">
            <label for="subject-name">Subject Name:</label>
            <input
                value="<?php echo isset($_POST['subject_name']) ? $_POST['subject_name'] : (isset($edit_subject_name) ? $edit_subject_name : ''); ?>"
                type="text" id="subject-name" name="subject_name" class="form-control">
            <div class="subjectname-error">
                <?php
                // Assuming you're submitting data to this same PHP script
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_code'])) {

                    $subjectCode = $_POST['subject_code'];
                    $subjectName = $_POST['subject_name'];

                    if (empty($subjectName) && empty($subjectCode)) {
                        echo "<div class='mt-5 alert alert-danger' role='alert'>Subject name and subject code cannot be empty!</div>";
                    } else if (preg_match('/\d/', $subjectName) && preg_match('/^[a-zA-Z0-9]+$/', $subjectCode)) {
                        echo "<div class='alert alert-danger mt-5' role='alert'>Please Enter a valid subject name and valid subject code</div>";
                    } else {
                        // Check if we are updating or inserting
                        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
                            // Prepare an update statement
                            $sqlUpdate = "UPDATE subjects SET subject_code = '$subjectCode' ,subject_name = '$subjectName' WHERE id = $update_id";


                            if (mysqli_query($conn, $sqlUpdate)) {
                                echo "<p class='text-bg-success p-2 mt-4'>Record updated successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
                            }
                        } else {
                            // Prepare an insert statement
                            $sqlInsert = "INSERT INTO subjects (subject_code,subject_name) VALUES ('$subjectCode','$subjectName')";

                            if (mysqli_query($conn, $sqlInsert)) {
                                echo "<p class='text-bg-success p-2 mt-4'>New record created successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                            }
                        }
                    }
                }

                mysqli_close($conn);
                ?>


            </div>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a class="btn btn-dark" href="subjects_table.php">Go to Table</a>
    </form>
</body>

</html>