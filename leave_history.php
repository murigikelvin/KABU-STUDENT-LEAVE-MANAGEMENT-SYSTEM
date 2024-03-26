<?php
require_once("DBConnection.php");
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION["sess_user"])) {
    header("Location: index.php");
    exit; // Add exit after redirect
} else {
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST['id']; // Change variable name from eid to id
        $remarks = $_POST['remarks'];

        if ($action === 'accept') {
            // Update leave status to Accepted for the specific application
            $update_query = "UPDATE leaves SET status='Accepted', remarks='$remarks' WHERE id=$id"; // Change eid to id
        } elseif ($action === 'reject') {
            // Update leave status to Rejected for the specific application
            $update_query = "UPDATE leaves SET status='Rejected', remarks='$remarks' WHERE id=$id"; // Change eid to id
        }

        if (mysqli_query($conn, $update_query)) {
            // Successfully updated, refresh the page to reflect changes
            header("Location: ".$_SERVER['PHP_SELF']);
            exit; // Add exit after redirect
        } else {
            // Error updating record
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <title>KABU ADMINISTRATOR</title>

    <style>
        h1 {
            text-align: center;
            font-size: 2.5em;
            font-weight: bold;
            padding-top: 1em;
        }

        .mycontainer {
            width: 90%;
            margin: 1.5rem auto;
            min-height: 60vh;
        }

        .mycontainer table {
            margin: 1.5rem auto;
        }

        /* Styling for form */
        form {
            display: flex;
            align-items: center;
        }

        input[type="text"] {
            margin-right: 10px;
            padding: 5px;
            border: 1px solid maroon;
            border-radius: 5px;
        }

        .btn-success {
            background-color: green;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: maroon;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

</head>

<body>
    <nav class="navbar header-nav navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">

            <a class="navbar-brand" href="#">KABU STUDENT ONLINE LEAVE APPLICATION</a>

            <ul class="nav justify-content-end">
                <li class="nav-item">
                    <a class="nav-link" href="list_emp.php" style="color:white;">View Students <span class="badge badge-pill" style="background-color:#2196f3;"><?php include('count_emp.php');?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_history.php" style="color:white;">View Leave History</a>
                </li>
                <li class="nav-item">
                    <button id="logout" onclick="window.location.href='logout.php';">LOGOUT</button>
                </li>
            </ul>

        </div>
    </nav>

    <h1>KABU ADMINISTRATOR - Student Leave History</h1>

    <div class="mycontainer">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <th>#</th>
                    <th>Students </th>
                    <th>Leave Application</th>
                    <th>Days</th>
                    <th>From-Date</th>
                    <th>To-Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th>Remarks</th>
                </thead>
                <tbody>
                    <!-- Loading all leave applications of the user -->
                    <?php
                    $leaves = mysqli_query($conn, "SELECT * FROM leaves");
                    if ($leaves) {
                        $numrow = mysqli_num_rows($leaves);
                        if ($numrow != 0) {
                            $cnt = 1;
                            while($row1 = mysqli_fetch_array($leaves)) {
                                $datetime1 = new DateTime($row1['fromdate']);
                                $datetime2 = new DateTime($row1['todate']);
                                $interval = $datetime1->diff($datetime2);
                                $remarks = isset($row1['remarks']) ? $row1['remarks'] : '';
                                echo "<tr id='{$row1['id']}'>
                                    <td>$cnt</td>
                                    <td>{$row1['ename']}</td>
                                    <td>{$row1['descr']}</td>
                                    <td>{$interval->format('%a Day/s')}</td>
                                    <td>{$datetime1->format('Y/m/d')}</td>
                                    <td>{$datetime2->format('Y/m/d')}</td>
                                    <td><b>{$row1['status']}</b></td>
                                    <td>";
                                // Display form only if leave status is Pending
                                if ($row1['status'] == 'Pending') {
                                    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>
                                        <input type='hidden' name='id' value='{$row1['id']}'> <!-- Changed eid to id -->
                                        <input type='text' name='remarks' placeholder='Enter remarks'> <!-- Added input field for remarks -->
                                        <button type='submit' name='action' value='accept' class='btn-success btn-sm'>Accept</button>
                                        <button type='submit' name='action' value='reject' class='btn-danger btn-sm'>Reject</button>
                                    </form>";
                                }
                                echo "</td>
                                    <td><b>$remarks</b></td>
                                </tr>";
                                $cnt++;
                            }
                        } else {
                            echo "<tr class='text-center'><td colspan='12'>YOU DON'T HAVE ANY LEAVE HISTORY! PLEASE APPLY TO VIEW YOUR STATUS HERE!</td></tr>";
                        }
                    } else {
                        echo "Query Error : " . "SELECT descr,status FROM leaves WHERE eid='" . $_SESSION['sess_eid'] . "'" . "<br>" . mysqli_error($conn);;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer class="footer navbar navbar-expand-lg navbar-light bg-light" style="color:white;">
        <div>
            <p class="text-center">&copy; <?php echo date("Y"); ?> KABU STUDENT ONLINE LEAVE APPLICATION</p>
            <p class="text-center">www.kabarak.ac.ke</p>
        </div>
    </footer>
</body>

</html>

<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
?>
