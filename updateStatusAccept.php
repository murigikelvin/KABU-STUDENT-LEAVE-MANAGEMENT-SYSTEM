<?php
require_once("DBConnection.php");
session_start();

if (!isset($_SESSION["sess_user"])) {
    header("Location: index.php");
} else {
    $eid = $_GET['eid'];
    $descr = $_GET['descr'];
    
    // Check if the current status is not 'Accepted' before updating
    $check_status_query = "SELECT status FROM leaves WHERE eid='$eid' AND descr='$descr'";
    $status_result = mysqli_query($conn, $check_status_query);
    $row = mysqli_fetch_assoc($status_result);
    $current_status = $row['status'];
    
    if ($current_status !== 'Accepted') {
        echo "Cannot update. The status is not 'Accepted'.";
    } else {
        if (isset($_POST['action']) && $_POST['action'] == 'accept') {
            $eid = $_POST['eid'];
            $remarks = $_POST['remarks'];

            $add_to_db = mysqli_query($conn, "UPDATE leaves SET status='Accepted', remarks='$remarks' WHERE eid='$eid'");

            if ($add_to_db) {
                echo 'Saved!!';
                header("Location: admin.php");
            } else {
                echo "Query Error : " . "UPDATE leaves SET status='Accepted', remarks='$remarks' WHERE eid='$eid'" . "<br>" . mysqli_error($conn);
            }
        }
    }
}

ini_set('display_errors', true);
error_reporting(E_ALL);
?>
