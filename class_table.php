<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "resultsdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check Connection
if (!$conn) {
    die("Connection Failed:" . mysqli_connect_error());
}
// Getting Data from classes table in resultsdb
$sql = "SELECT * FROM classes";
$result = mysqli_query($conn, $sql);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Class Form Data</title>
    <link rel="stylesheet" href="CSS/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include_once ('sidebar.php') ?>
    <div class="container mt-5">
        <h2>Classes Form Data</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark bg-primary">
                    <tr>
                        <th>ID</th>
                        <th>Class Name</th>
                        <th>Subject Name</th>
                        <th class="text-center">Function</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) == !empty($result)) {
                        // Fetch all data at once and store it in an associative array
                        $allRows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        // Iterate through each row using a foreach loop
                        foreach ($allRows as $row) { ?>
                            <tr>
                                <td><?php echo $row["id"] ?></td>
                                <td><?php echo $row["class_name"] ?></td>
                                <td><?php echo $row["subject_name"] ?></td>
                                <td class='d-flex'> <a target="_blank" class="btn btn-outline-primary w-50 me-2"
                                        href="class_form.php?update_id=<?php echo $row['id']; ?>">UPDATE</a>
                                    <a class="btn btn-danger w-50"
                                        href="class_table.php?delete_id=<?php echo $row['id']; ?>">DELETE</a>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td class='bg-danger text-light text-center fw-bold h1' colspan='5'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
            // Delete functionality
            if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];
                $sql = "DELETE FROM classes WHERE id=$delete_id";
                if (mysqli_query($conn, $sql)) {
                    echo "<h5 class='d-inline-block p-2 text-center text-danger fw-bold border border-danger'>Record Deleted Successfully</h2>";
                } else {
                    echo "Error deleting record: " . mysqli_error($conn);
                }
            }
            ?>
        </div>
    </div>
    <?php
    mysqli_close($conn);
    ?>
</body>

</html>