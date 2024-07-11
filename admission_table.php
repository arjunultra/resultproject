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
// Fetch students data for display
$sqlStudents = "SELECT * FROM studentadmission";
$resultStudents = mysqli_query($conn, $sqlStudents);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Table</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <h1 class="main-title text-center">Student Admission Records</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Roll No</th>
                <th>Date of Birth</th>
                <th>Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($resultStudents) > 0) {
                while ($row = mysqli_fetch_assoc($resultStudents)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['student_name'] . "</td>";
                    echo "<td>" . $row['student_gender'] . "</td>";
                    echo "<td>" . $row['student_rollno'] . "</td>";
                    echo "<td>" . $row['student_dob'] . "</td>";
                    echo "<td>" . $row['student_class'] . "</td>";
                    echo "<td>
                            <a href='admission_form.php?update_id=" . $row['id'] . "' class='btn btn-warning'>Update</a>
                            <a href='admission_table.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr class='text-bg-danger'><td colspan='7' class='text-center'>No records found</td></tr>";
            }
            ?>
            <?php
            // Delete operation
            if (isset($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];
                $sqlDelete = "DELETE FROM studentadmission WHERE id='$delete_id'";
                if (mysqli_query($conn, $sqlDelete)) {
                    echo "<script>
                alert('Record deleted successfully.');
                //  window.location.href = 'admission_form.php';
              </script>";
                } else {
                    echo "Error: " . $sqlDelete . "<br>" . mysqli_error($conn);
                }
            }

            // Close connection
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
    <a class="d-block w-25 mx-auto btn btn-primary text-center" href="admission_form.php">Go to Admission
        Form</a>
</body>

</html>