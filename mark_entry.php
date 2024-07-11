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
// initializing variables
$student_name = $student_rollno = $student_class = "";
$subject_name = $student_marks = [];

// Error handling variables
$studentNameError = $studentRollnoError = $studentClassError = $subjectNameError = $studentmarksError = $duplicateEntryError = "";

// Update operation variables
$update_id = isset($_REQUEST['update_id']) ? $_REQUEST['update_id'] : "";
$update_student_name = $update_student_rollno = $update_student_class = $update_subject_name = $update_student_marks = "";
$subjectStrValue = "";

// Update operation
if ($update_id) {
    $update_id = $_REQUEST['update_id'];
    $query = "SELECT * FROM markentry WHERE id='$update_id'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $update_student_name = $row['student_name'];
        $update_student_rollno = $row['student_rollno'];
        $update_student_class = $row['student_class'];
        $update_subject_name = $row['subject_name'];
        $update_student_marks = $row['student_marks'];
    }
}

// Create table if not exists
$tableSql = "CREATE TABLE IF NOT EXISTS markentry (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(255) NOT NULL,
    student_rollno VARCHAR(255) NOT NULL,
    student_class VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    student_marks VARCHAR(255)
)";
if (!mysqli_query($conn, $tableSql)) {
    echo "Error creating table: " . mysqli_error($conn);
}

// POST Starts 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : "";
    $student_name = isset($_POST['student_name']) ? $_POST['student_name'] : "";
    $student_rollno = isset($_POST['student_rollno']) ? $_POST['student_rollno'] : "";
    $student_class = isset($_POST['student_class']) ? $_POST['student_class'] : "";
    $subject_name = isset($_POST['subject_name']) ? $_POST['subject_name'] : "";
    $student_marks = isset($_POST['mark_obtained']) ? $_POST['mark_obtained'] : "";
    print_r($subject_name) . "hi";

    // Regex patterns
    $namePattern = "/^[a-zA-Z ]*$/";
    $numberPattern = "/^[0-9]*$/";

    // Validation
    $isValid = true;
    if (empty($student_name) || !preg_match($namePattern, $student_name)) {
        $studentNameError = "Invalid name";
        $isValid = false;
    }
    if (empty($student_rollno) || !preg_match($numberPattern, $student_rollno)) {
        $studentRollnoError = "Invalid roll number";
        $isValid = false;
    }
    if (empty($student_class)) {
        $studentClassError = "Class is required";
        $isValid = false;
    }
    if (empty($subject_name)) {
        $subjectNameError = "Subject is required";
        $isValid = false;
    }
    $studentmarksError = "";
    $isValid = true;

    foreach ($student_marks as $mark) {
        if (!preg_match($numberPattern, $mark)) {
            $studentmarksError = "Invalid marks";
            $isValid = false;
            break; // Exit loop as soon as an invalid mark is found
        }
    }

    if ($isValid) {
        // All marks are valid
    } else {
        echo $studentmarksError;
    }


    // Insert or update logic
    if ($isValid) {
        if (is_array($subject_name)) {
            $subjectStrValue = implode(',', $subject_name);
        } else {
            $subjectStrValue = $subject_name;
        }
        if (is_array($student_marks)) {
            $marksStrValue = implode(',', $student_marks);
        } else {
            $marksStrValue = $student_marks;
        }
        if ($update_id) {
            // Update record
            $updateSql = "UPDATE markentry SET 
                          student_name='$student_name', 
                          student_rollno='$student_rollno', 
                          student_class='$student_class', 
                          subject_name='$subjectStrValue', 
                          student_marks='$student_marks' 
                          WHERE id='$update_id'";
            if (mysqli_query($conn, $updateSql)) {
                echo "Record updated successfully.";
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            // Insert record
            $insertSql = "INSERT INTO markentry (student_name, student_rollno, student_class, subject_name, student_marks) 
                          VALUES ('$student_name', '$student_rollno', '$student_class', '$subjectStrValue', '$marksStrValue')";
            if (mysqli_query($conn, $insertSql)) {
                echo "New record created successfully.";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }

    }
}

// Retrieve Student Names
$sqlStudentNames = "SELECT student_name FROM studentadmission";
$resultStudentNames = mysqli_query($conn, $sqlStudentNames);
$studentNameOptions = "";
if (mysqli_num_rows($resultStudentNames) > 0) {
    while ($row = mysqli_fetch_assoc($resultStudentNames)) {
        $selected = ($row['student_name'] === $update_student_name) ? 'selected' : '';
        $studentNameOptions .= "<option value='" . $row['student_name'] . "'$selected>" . $row['student_name'] . "</option>";
    }
}

// Retrieve Student Subjects from classes
$sqlStudentSubjects = "SELECT DISTINCT subject_name FROM classes WHERE class_name";
$resultStudentSubjects = mysqli_query($conn, $sqlStudentSubjects);
$studentSubjectOptions = "";
if (mysqli_num_rows($resultStudentSubjects) > 0) {
    while ($row = mysqli_fetch_assoc($resultStudentSubjects)) {
        $selected = ($row['subject_name'] === $update_subject_name) ? 'selected' : '';
        $studentSubjectOptions .= "<option value='" . $row['subject_name'] . "'$selected>" . $row['subject_name'] . "</option>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Mark Entry</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <h1 class="main-title text-center">Students Mark Entry</h1>
    <div class="container-sm">
        <form method="POST" class="form w-100 text-center"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
            <div class="form-group">
                <label for="student-name">Student Name:</label>
                <select onchange="getRollno(this.value);" class="form-control" name="student_name" id="student-name">
                    <option value="">Select a Student</option>
                    <?php echo $studentNameOptions; ?>
                </select>
                <span class="error"><?php echo $studentNameError; ?></span>

                <label for="student-rollno">Student Roll No:</label>
                <input
                    value="<?php echo isset($_POST['student_rollno']) ? $_POST['student_rollno'] : $update_student_rollno; ?>"
                    type="text" id="student-rollno" name="student_rollno" class="form-control">
                <span class="error"><?php echo $studentRollnoError; ?></span>

                <label for="student-class">Student Class:</label>
                <input
                    value="<?php echo isset($_POST['student_class']) ? $_POST['student_class'] : $update_student_class; ?>"
                    type="text" id="student-class" name="student_class" class="form-control">
                <span class="error"><?php echo $studentClassError; ?></span>

                <label for="subjects-select">Subject Name:</label>
                <select name="subjects_select" id="subjects-select" class="form-control">
                    <option value="">Select a Subject</option>
                    <?php echo $studentSubjectOptions; ?>
                </select>
                <span class="error"><?php echo $subjectNameError; ?></span>

                <label for="student-marks">Student Marks:</label>
                <div class="flex-container d-flex">
                    <input
                        value="<?php echo isset($_POST['student_marks']) ? $_POST['student_marks'] : $update_student_marks; ?>"
                        type="text" id="student-marks" name="student_marks" class="form-control">
                    <!-- add button -->
                    <div><button id="add-btn" type="button" class="btn btn-success px-5 fw-bold">ADD</button></div>
                </div>
                <span class="error"><?php echo $studentmarksError; ?></span>
            </div>
            <br>
            <span class="error"><?php echo $duplicateEntryError; ?></span>
            <div class="container">
                <h2 class="text-center shadow-lg box-shadow text-danger-emphasis">Student Marks Report</h2>
                <div id="productTable" class="table-responsive">
                    <table id="marks-table" class="table table-striped table-hover table-bordered">
                        <thead class="table-dark bg-primary">
                            <tr>
                                <th>S.No</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <input class="btn btn-danger" type="submit" value="Submit">
            <a class="btn btn-outline-dark ms-3" href="./admission_table.php">Go to Table</a>
        </form>
        <!-- table -->

        <!-- JS Scripts -->
        <script src="./JS/jquery-3.7.1.min.js"></script>
        <script>
            function getRollno(student_name) {
                let post_url = "mark_entry_changes.php?student_name=" + student_name;
                jQuery.ajax({
                    url: post_url,
                    success: function (result) {
                        if (result) {
                            let [student_rollno, student_class] = result.split(',');
                            $("#student-rollno").val(student_rollno);
                            $("#student-class").val(student_class);
                        }
                    },
                });
            }

            // table ajax
            $("#add-btn").click(function () {
                let rowIndex = 1;
                // console.log('button clicked');
                let selectedSubject = "";
                if ($("#subjects-select").length > 0) {
                    selectedSubject = $("#subjects-select").val();
                }
                let markObtained = "";
                if ($("#student-marks").length > 0) {
                    markObtained = $("#student-marks").val();
                }
                let post_url = "mark_entry_changes.php?selected_subject=" + selectedSubject + "&mark_obtained=" + markObtained;
                loadContent(post_url, "#table-body");
            });
        </script>
        <script src="./JS/load-table-ajax.js"></script>
    </div>
</body>
<?php mysqli_close($conn); ?>

</html>