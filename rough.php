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

$subjectName = isset($_POST["subject_select"]) ? $_POST["subject_select"] : "";
$className = isset($_POST["class_name"]) ? $_POST["class_name"] : "";

// Error Handling
$classNameError = $subjectNameError = $duplicateEntryError = "";

// Fetch class data for update if update_id is set
$update_id = isset($_REQUEST['update_id']) ? $_REQUEST['update_id'] : "";
$update_class_name = "";
$update_subject_name = "";

if ($update_id) {
    $query = "SELECT * FROM classes WHERE id='$update_id'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $update_class_name = $row['class_name'];
        $update_subject_name = $row['subject_name'];
        $subjectName = $update_subject_name;  // Set subjectName for the select element
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Sanitize and validate class name
    if (empty($_POST['class_name'])) {
        $classNameError = "Class name cannot be empty.";
        $isValid = false;
    } else if (!preg_match("/^[0-9 \-_]+$/", $className)) {
        $classNameError = "Class name contains invalid characters.";
        $isValid = false;
    }

    if (empty($_POST['subject_select'])) {
        $subjectNameError = "Subject name cannot be empty!";
        $isValid = false;
    } else {
        $subjectName = $_POST['subject_select'];
    }

    if ($isValid) {
        $className = mysqli_real_escape_string($conn, $_POST['class_name']);
        $subjectName = mysqli_real_escape_string($conn, $_POST['subject_select']);

        if ($update_id) {
            // Update operation
            $sqlUpdate = "UPDATE classes SET class_name='$className', subject_name='$subjectName' WHERE id='$update_id'";
            if (mysqli_query($conn, $sqlUpdate)) {
                echo "<script>alert('Record updated successfully.');</script>";
            } else {
                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
            }
        } else {
            // Check for duplicates before insert
            $checkDuplicate = "SELECT * FROM classes WHERE class_name='$className' AND subject_name='$subjectName'";
            $duplicateResult = mysqli_query($conn, $checkDuplicate);

            if (mysqli_num_rows($duplicateResult) > 0) {
                $duplicateEntryError = "This class and subject combination already exists.";
            } else {
                // Insert operation
                $sqlInsert = "INSERT INTO classes (class_name, subject_name) VALUES ('$className', '$subjectName')";
                if (mysqli_query($conn, $sqlInsert)) {
                    echo "<script>
                            alert('New record created successfully.');
                            setTimeout(function() {
                                window.location.href = 'class_form.php';
                            }, 3000);
                          </script>";
                } else {
                    echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}

// Fetch subjects data from subjects table
$sqlSubjects = "SELECT * FROM subjects";
$resultSubjects = mysqli_query($conn, $sqlSubjects);
$subjectOptions = "";

if (mysqli_num_rows($resultSubjects) > 0) {
    while ($row = mysqli_fetch_assoc($resultSubjects)) {
        $selected = ($row['subject_name'] === $subjectName) ? 'selected' : '';
        $subjectOptions .= "<option value='" . $row['subject_name'] . "' $selected>" . $row['subject_name'] . "</option>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Form</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <h1 class="main-title text-center">Enter Your Class</h1>
    <form method="POST" class="form w-100 text-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="update_id" value="<?= $update_id ?>">
        <div class="form-group">
            <label for="class-name">Class Name:</label>
            <input value="<?php echo isset($_POST['class_name']) ? $_POST['class_name'] : $update_class_name; ?>"
                type="text" id="class-name" name="class_name" class="form-control">
            <span class="error"><?php echo $classNameError; ?></span>

            <label for="subject-select">Subject Name:</label>
            <select name="subject_select" id="subject-select" class="form-control">
                <option value="">Select a Subject</option>
                <?= $subjectOptions ?>
            </select>
            <span class="error"><?php echo $subjectNameError; ?></span>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a class="btn btn-dark" href="subjects_table.php">Go to Table</a>
        <br><br>
        <span class="error"><?php echo $duplicateEntryError; ?></span>
    </form>
</body>

</html>