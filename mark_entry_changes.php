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
if (isset($_GET['student_name'])) {
    $id = "";
    $studentName = $_REQUEST['student_name'];
    $errorQuery = "SELECT id FROM markentry WHERE student_name ='$studentName'";
    $errorResult = mysqli_query($conn, $errorQuery);
    if (!empty($errorResult)) {
        foreach ($errorResult as $result) {
            $id = $result['id'];
        }
    }
    if (empty($id)) {

        $query = "SELECT student_rollno, student_class FROM studentadmission WHERE student_name = '$studentName'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                echo $row['student_rollno'] . ',' . $row['student_class'];
            } else {
                echo ",";
            }
        }
    } else {
        echo "<p class='text-bg-danger'>Result Already Declared</p>";
    }
}
// Table Ajax
if (isset($_REQUEST['selected_subject'])) {
    $subject_name = "";
    $mark_obtained = "";

    $row_index = $_REQUEST['row_index'];
    $mark_obtained = $_REQUEST['mark_obtained'];
    $subject_name = $_REQUEST['selected_subject'];
    if (!empty($subject_name) && (!empty($mark_obtained))) { ?>
        <tr <?php echo $row_index; ?>>
            <td><?= $row_index ?></td>
            <td><?php echo $subject_name ?>
                <input type="hidden" name="subject_name[]" value="<?php echo $subject_name ?>">
            </td>
            <td><?php echo $mark_obtained ?>
                <input type="hidden" name="mark_obtained[]" value="<?php echo $mark_obtained ?>">
            </td>
        </tr>


    <?php }

}


mysqli_close($conn);
