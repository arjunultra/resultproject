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
$studentNameError = $studentGenderError = $studentRollnoError = $studentDobError = $studentClassError = $duplicateEntryError = "";

// Update variables
$update_id = isset($_REQUEST['update_id']) ? $_REQUEST['update_id'] : "";
$update_student_name = "";
$update_student_gender = "";
$update_student_rollno = "";
$update_student_dob = "";
$update_student_class = "";

// Fetch student data for update if update_id is set
if ($update_id) {
    $query = "SELECT * FROM studentadmission WHERE id='$update_id'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $update_student_name = $row['student_name'];
        $update_student_gender = $row['student_gender'];
        $update_student_rollno = $row['student_rollno'];
        $update_student_dob = $row['student_dob'];
        $update_student_class = $row['student_class'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate student name
    if (empty($_POST['student_name'])) {
        $studentNameError = "Student name cannot be empty.";
        $isValid = false;
    }

    // Validate student gender
    if (empty($_POST['student_gender'])) {
        $studentGenderError = "Student gender cannot be empty.";
        $isValid = false;
    }

    // Validate student roll number
    if (empty($_POST['student_rollno'])) {
        $studentRollnoError = "Student roll number cannot be empty.";
        $isValid = false;
    }

    // Validate student date of birth
    if (empty($_POST['student_dob'])) {
        $studentDobError = "Student date of birth cannot be empty.";
        $isValid = false;
    }

    // Validate student class
    if (empty($_POST['student_class'])) {
        $studentClassError = "Student class cannot be empty.";
        $isValid = false;
    }

    if ($isValid) {
        $student_name = $_POST['student_name'];
        $student_gender = $_POST['student_gender'];
        $student_rollno = $_POST['student_rollno'];
        $student_dob = $_POST['student_dob'];
        $student_class = $_POST['student_class'];

        if ($update_id) {
            // Update operation
            $sqlUpdate = "UPDATE studentadmission SET student_name='$student_name', student_gender='$student_gender', student_rollno='$student_rollno', student_dob='$student_dob', student_class='$student_class' WHERE id='$update_id'";
            if (mysqli_query($conn, $sqlUpdate)) {
                echo "<script>alert('Record updated successfully.');</script>";
            } else {
                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
            }
        } else {
            // Check for duplicates before insert
            $checkDuplicate = "SELECT * FROM studentadmission WHERE student_rollno='$student_rollno'";
            $duplicateResult = mysqli_query($conn, $checkDuplicate);

            if (mysqli_num_rows($duplicateResult) > 0) {
                $duplicateEntryError = "This student roll number already exists.";
            } else {
                // Insert operation
                $sqlInsert = "INSERT INTO studentadmission (student_name, student_gender, student_rollno, student_dob, student_class) VALUES ('$student_name', '$student_gender', '$student_rollno', '$student_dob', '$student_class')";
                if (mysqli_query($conn, $sqlInsert)) {
                    echo "<script>
                            alert('New record created successfully.');
                            setTimeout(function() {
                                window.location.href = 'admission_form.php';
                            }, 3000);
                          </script>";
                } else {
                    echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}

// Fetch unique class options
$sqlClasses = "SELECT DISTINCT class_name FROM classes";
$resultClasses = mysqli_query($conn, $sqlClasses);
$classOptions = "";

if (mysqli_num_rows($resultClasses) > 0) {
    while ($row = mysqli_fetch_assoc($resultClasses)) {
        $selected = ($row['class_name'] === $update_student_class) ? 'selected' : '';
        $classOptions .= "<option value='" . $row['class_name'] . "' $selected>" . $row['class_name'] . "</option>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Form</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <h1 class="main-title text-center">Enter Student Admission Details</h1>
    <div class="container-sm">
        <form method="POST" class="form w-100 text-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="update_id" value="<?= $update_id ?>">
            <div class="form-group">
                <label for="student-name">Student Name:</label>
                <input
                    value="<?php echo isset($_POST['student_name']) ? $_POST['student_name'] : $update_student_name; ?>"
                    type="text" id="student-name" name="student_name" class="form-control">
                <span class="error"><?php echo $studentNameError; ?></span>
                <label for="student-gender">Student Gender:</label>
                <select name="student_gender" id="student-gender" class="form-control">
                    <option value="">Select Your Gender</option>
                    <option value="male" <?php echo (isset($_POST['student_gender']) && $_POST['student_gender'] === 'male') ? 'selected' : ($update_student_gender === 'male' ? 'selected' : ''); ?>>Male</option>
                    <option value="female" <?php echo (isset($_POST['student_gender']) && $_POST['student_gender'] === 'female') ? 'selected' : ($update_student_gender === 'female' ? 'selected' : ''); ?>>Female</option>
                </select>
                <span class="error"><?php echo $studentGenderError; ?></span>
                <label for="student-rollno">Student Roll No:</label>
                <input
                    value="<?php echo isset($_POST['student_rollno']) ? $_POST['student_rollno'] : $update_student_rollno; ?>"
                    type="text" id="student-rollno" name="student_rollno" class="form-control">
                <span class="error"><?php echo $studentRollnoError; ?></span>
                <label for="student-dob">Student Date of Birth:</label>
                <input value="<?php echo isset($_POST['student_dob']) ? $_POST['student_dob'] : $update_student_dob; ?>"
                    type="date" id="student-dob" name="student_dob" class="form-control">
                <span class="error"><?php echo $studentDobError; ?></span>
                <label for="student-class">Student Class:</label>
                <select name="student_class" id="student-class" class="form-control">
                    <option value="">Select Your Class</option>
                    <?= $classOptions ?>
                </select>
                <span class="error"><?php echo $studentClassError; ?></span>
            </div>
            <br>
            <input class="btn btn-primary" type="submit" value="Submit">
            <a class="btn btn-outline-warning ms-3" href="./admission_table.php">Go to Table</a>
            <br><br>
            <span class="error"><?php echo $duplicateEntryError; ?></span>
        </form>
    </div>