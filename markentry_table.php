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
// Fetch markentry data for display
$sqlMarks = "SELECT * FROM markentry";
$resultMarks = mysqli_query($conn, $sqlMarks);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Mark Entry Table</title>
    <link rel="stylesheet" href="./CSS/bootstrap.min.css">
    <link rel="stylesheet" href="./CSS/style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <h1 class="main-title text-center">Student Mark Statement Records</h1>
    <table class="table table-bordered">
        <thead>
            <tr class="text-bg-dark">
                <th>ID</th>
                <th>Student Name</th>
                <th>Student Roll No</th>
                <th>Student Class</th>
                <th>Subject Name</th>
                <th>Marks Obtained</th>
                <th>Result Status</th>
                <th>Functions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($resultMarks) > 0) {
                while ($row = mysqli_fetch_assoc($resultMarks)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['student_name'] . "</td>";
                    echo "<td>" . $row['student_rollno'] . "</td>";
                    echo "<td>" . $row['student_class'] . "</td>";
                    echo "<td>" . $row['subject_name'] . "</td>";
                    echo "<td>" . $row['student_marks'] . "</td>";
                    echo "<td>" . $row['result_declared'] . "</td>";
                    echo "<td>
                            <a href='mark_entry.php?update_id=" . $row['id'] . "' class='btn btn-warning'>Update</a>
                            <a href='markentry_table.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
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
                $sqlDelete = "DELETE FROM markentry WHERE id='$delete_id'";
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
    <a target="_blank" class="d-block w-25 mx-auto btn btn-primary text-center" href="mark_entry.php">Go to Mark Entry
        Form</a>
</body>

</html>